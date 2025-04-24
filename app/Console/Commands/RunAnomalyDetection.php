<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RunAnomalyDetection extends Command
{
    protected $signature = 'anomaly:detect';
    protected $description = 'Run the anomaly detection Python script for inventory transactions';

    public function handle()
    {
        $this->info('🚀 Starting Anomaly Detection...');

        $pythonPath = base_path('PythonML/venv/Scripts/python.exe');
        $scriptPath = base_path('PythonML/anomaly_detection.py');

        // Wrap paths in quotes to handle spaces or special characters
        $command = "\"$pythonPath\" \"$scriptPath\"";

        $process = Process::fromShellCommandline($command);

        $process->setTimeout(3600); // 1 hour timeout — adjust if needed

        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info('✅ Anomaly Detection Completed!');
    }
}
