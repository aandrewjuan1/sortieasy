<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ForecastService
{
    public function runForecasts()
    {
        $pythonPath = base_path('PythonML/venv/Scripts/python.exe');
        $scriptPath = base_path('PythonML/demand_forecasting.py');

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

        Cache::forget('demand_forecasts:page:1:per_page:10:sort:forecast_date:dir:DESC:search::product::date_range:');
        Cache::forget('restocking_recommendations:page:1:per_page:10:sort:name:dir:ASC:search:');

        return true;
    }
}
