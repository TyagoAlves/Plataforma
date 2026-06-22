<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TtsService
{
    private string $lang;
    private string $disk;

    public function __construct()
    {
        $this->lang = 'pt-BR';
        $this->disk = 'public';
    }

    public function generateAudio(string $text, string $filename): ?string
    {
        $this->ensureDiskDirectory();

        $mp3Path = "podcasts/{$filename}.mp3";

        if (Storage::disk($this->disk)->exists($mp3Path)) {
            return $mp3Path;
        }

        $audioGenerated = $this->generateWithGoogleTts($text, $mp3Path);

        if ($audioGenerated) {
            return $mp3Path;
        }

        return null;
    }

    private function generateWithGoogleTts(string $text, string $outputPath): bool
    {
        $maxChars = 180;
        $chunks = $this->splitText($text, $maxChars);

        if (empty($chunks)) return false;

        $audioParts = [];
        foreach ($chunks as $i => $chunk) {
            $url = 'https://translate.google.com/translate_tts';
            $params = [
                'ie' => 'UTF-8',
                'q' => $chunk,
                'tl' => $this->lang,
                'client' => 'tw-ob',
            ];

            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Referer' => 'https://translate.google.com/',
                    ])
                    ->get($url, $params);

                if ($response->successful()) {
                    $partPath = "podcasts/part_{$i}_{$outputPath}";
                    Storage::disk($this->disk)->put($partPath, $response->body());
                    $audioParts[] = Storage::disk($this->disk)->path($partPath);
                }
            } catch (\Exception $e) {
                Log::error('TTS chunk error', ['chunk' => $i, 'message' => $e->getMessage()]);
            }
        }

        if (empty($audioParts)) return false;

        $finalPath = Storage::disk($this->disk)->path($outputPath);
        $this->concatenateMp3s($audioParts, $finalPath);

        foreach ($audioParts as $part) {
            if (file_exists($part)) {
                unlink($part);
            }
        }

        return file_exists($finalPath) && filesize($finalPath) > 0;
    }

    private function ensureDiskDirectory(): void
    {
        $path = Storage::disk($this->disk)->path('podcasts');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    private function splitText(string $text, int $maxChars): array
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);
        $chunks = [];
        $currentChunk = '';

        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (empty($sentence)) continue;

            if (mb_strlen($currentChunk . $sentence) <= $maxChars) {
                $currentChunk .= ($currentChunk ? ' ' : '') . $sentence;
            } else {
                if (!empty($currentChunk)) {
                    $chunks[] = $currentChunk;
                }
                $currentChunk = $sentence;

                while (mb_strlen($currentChunk) > $maxChars) {
                    $chunks[] = mb_substr($currentChunk, 0, $maxChars);
                    $currentChunk = mb_substr($currentChunk, $maxChars);
                }
            }
        }

        if (!empty($currentChunk)) {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }

    private function concatenateMp3s(array $parts, string $output): void
    {
        if (empty($parts)) return;

        $merged = '';
        foreach ($parts as $part) {
            if (file_exists($part)) {
                $merged .= file_get_contents($part);
            }
        }

        if (!empty($merged)) {
            file_put_contents($output, $merged);
            chmod($output, 0644);
        }
    }

    public function scriptToSpeechText(string $script): string
    {
        $text = preg_replace('/\[Anfitrião\]:\s*/', '', $script);
        $text = preg_replace('/\[Convidado\]:\s*/', '', $text);
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text);
        $text = strip_tags($text);
        return trim($text);
    }

    public function estimateDuration(string $script): int
    {
        $words = str_word_count($script);
        $wordsPerMinute = 150;
        return (int) ceil(($words / $wordsPerMinute) * 60);
    }
}
