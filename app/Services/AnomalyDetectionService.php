<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AnomalyDetectionService
{
    public function runAnomalyDetection()
    {
        $pythonPath = base_path('PythonML/venv/Scripts/python.exe');
        $scriptPath = base_path('PythonML/anomaly_detection.py');

        // Wrap paths in quotes to handle spaces
        $command = "\"$pythonPath\" \"$scriptPath\"";

        $process = Process::fromShellCommandline($command);

        $process->setTimeout(3600); // 1 hour timeout

        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return true;
    }
}
