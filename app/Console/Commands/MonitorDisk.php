<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorDisk extends Command
{
    protected $signature = 'disk:monitor {--rotate-logs : Forçar rotação de logs}';
    protected $description = 'Monitora uso de disco e gerencia logs';

    private int $warningThreshold = 80;
    private int $criticalThreshold = 90;

    public function handle(): int
    {
        $output = shell_exec('df -h / | tail -1');
        $this->info($output);

        $parts = preg_split('/\s+/', trim($output));
        $usagePercent = $parts[4] ?? '0%';
        $usage = (int) str_replace('%', '', $usagePercent);

        $this->info("Uso do disco: {$usage}%");

        $this->rotateHealthcheckLog();

        if ($this->option('rotate-logs')) {
            $this->rotateAllLogs();
        }

        if ($usage >= $this->criticalThreshold) {
            $this->warn("DISCO CRÍTICO: {$usage}% - Executando limpeza emergencial!");
            $this->emergencyCleanup();
            Log::critical('Disk critical - emergency cleanup executed', ['usage' => "{$usage}%"]);
        } elseif ($usage >= $this->warningThreshold) {
            $this->warn("Disco em {$usage}% - executando limpeza preventiva!");
            $this->preventiveCleanup();
            Log::warning('Disk cleanup triggered', ['usage' => "{$usage}%"]);
        } else {
            $this->info("Disco OK ({$usage}%)");
        }

        return Command::SUCCESS;
    }

    private function preventiveCleanup(): void
    {
        shell_exec('docker system prune -af --volumes 2>/dev/null');
        shell_exec('sudo apt-get clean 2>/dev/null || sudo dnf clean all 2>/dev/null');
        shell_exec('sudo journalctl --vacuum-time=3d 2>/dev/null');

        $this->call('optimize:clear');
        $this->call('view:clear');

        $this->cleanLaravelLogs();
    }

    private function emergencyCleanup(): void
    {
        $this->preventiveCleanup();

        shell_exec('sudo journalctl --vacuum-size=100M 2>/dev/null');
        shell_exec('sudo rm -rf /tmp/* 2>/dev/null');
        shell_exec('docker rm $(docker ps -aq --filter status=exited) 2>/dev/null');
        shell_exec('docker rmi $(docker images --filter dangling=true -q) 2>/dev/null');

        $this->cleanLaravelLogs(true);
    }

    private function cleanLaravelLogs(bool $force = false): void
    {
        $logDir = storage_path('logs');
        if (!is_dir($logDir)) return;

        $maxSize = $force ? 10 : 50;

        foreach (glob("{$logDir}/*.log") as $logFile) {
            if (filesize($logFile) > $maxSize * 1024 * 1024) {
                $rotated = $logFile . '.' . date('Ymd-His');
                rename($logFile, $rotated);
                file_put_contents($logFile, '');
                $this->info("Log rotated: " . basename($logFile));
            }
        }

        $oldLogs = glob("{$logDir}/*.log.*");
        usort($oldLogs, fn($a, $b) => filemtime($a) - filemtime($b));

        $maxFiles = $force ? 3 : 7;
        while (count($oldLogs) > $maxFiles) {
            $oldest = array_shift($oldLogs);
            unlink($oldest);
            $this->info("Removed old log: " . basename($oldest));
        }
    }

    private function rotateHealthcheckLog(): void
    {
        $healthcheckLog = '/var/log/healthcheck.log';
        if (!file_exists($healthcheckLog)) return;

        $maxSize = 5 * 1024 * 1024;

        if (filesize($healthcheckLog) > $maxSize) {
            $rotated = $healthcheckLog . '.' . date('Ymd-His');
            copy($healthcheckLog, $rotated);
            file_put_contents($healthcheckLog, '');
            chmod($healthcheckLog, 0644);
            $this->info("Healthcheck log rotated: {$rotated}");
        }

        $backups = glob('/var/log/healthcheck.log.*');
        usort($backups, fn($a, $b) => filemtime($a) - filemtime($b));

        while (count($backups) > 5) {
            $oldest = array_shift($backups);
            if (is_writable($oldest) || is_file($oldest)) {
                unlink($oldest);
                $this->info("Removed old healthcheck log: " . basename($oldest));
            }
        }
    }

    private function rotateAllLogs(): void
    {
        $this->rotateHealthcheckLog();
        $this->cleanLaravelLogs();
        $this->info("Todos os logs foram rotacionados.");
    }
}
