<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RunForecast extends Command
{
    protected $signature = 'forecast:generate';
    protected $description = 'Run the demand forecasting Python script';

    public function handle()
    {
        $this->info('🚀 Starting Demand Forecasting...');

        $pythonPath = base_path('PythonML/venv/Scripts/python.exe');
        $scriptPath = base_path('PythonML/demand_forecasting.py');

        // Wrap paths in quotes to handle spaces
        $command = "\"$pythonPath\" \"$scriptPath\"";

        $process = Process::fromShellCommandline($command);

        $process->setTimeout(3600);

        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->info('✅ Demand Forecasting Completed!');
    }
}
