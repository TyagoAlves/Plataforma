<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorDisk extends Command
{
    protected $signature = 'disk:monitor';
    protected $description = 'Monitora uso de disco e limpa cache se necessário';

    public function handle(): int
    {
        $output = shell_exec('df -h / | tail -1');
        $this->info($output);

        $parts = preg_split('/\s+/', trim($output));
        $usagePercent = $parts[4] ?? '0%';
        $usage = (int) str_replace('%', '', $usagePercent);

        $this->info("Uso do disco: {$usage}%");

        if ($usage >= 85) {
            $this->warn("Disco em {$usage}% - executando limpeza!");

            shell_exec('docker system prune -af --volumes 2>/dev/null');
            shell_exec('sudo apt-get clean 2>/dev/null || sudo dnf clean all 2>/dev/null');
            shell_exec('sudo journalctl --vacuum-time=3d 2>/dev/null');

            $this->call('optimize:clear');
            $this->call('view:clear');

            Log::warning('Disk cleanup triggered', ['usage' => "{$usage}%"]);
        }

        return Command::SUCCESS;
    }
}
