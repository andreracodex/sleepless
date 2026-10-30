<?php

namespace App\Support;

class PythonBinary
{
    /**
     * Resolve the Python binary executable path.
     * Checks:
     * 1. env('PYTHON_BINARY')
     * 2. Laragon's bundled Python installations (C:/Laragon/bin/python/...)
     * 3. Common Windows Python paths
     * 4. System PATH 'python'
     */
    public static function getPath(): string
    {
        // 1. Explicitly configured in env
        $envPath = env('PYTHON_BINARY');
        if (!empty($envPath) && (file_exists($envPath) || is_executable($envPath))) {
            return $envPath;
        }

        // 2. Laragon auto-detection
        $laragonPythons = glob('C:/Laragon/bin/python/python*/python.exe');
        if (!empty($laragonPythons) && file_exists($laragonPythons[0])) {
            return str_replace('/', DIRECTORY_SEPARATOR, $laragonPythons[0]);
        }

        $altLaragons = glob('c:/laragon/bin/python/python*/python.exe');
        if (!empty($altLaragons) && file_exists($altLaragons[0])) {
            return str_replace('/', DIRECTORY_SEPARATOR, $altLaragons[0]);
        }

        // 3. Common Windows Python installations
        $localAppData = getenv('LOCALAPPDATA');
        $commonPaths = [
            'C:/Python313/python.exe',
            'C:/Python312/python.exe',
            'C:/Python311/python.exe',
            $localAppData ? $localAppData . '/Programs/Python/Python313/python.exe' : null,
            $localAppData ? $localAppData . '/Programs/Python/Python312/python.exe' : null,
        ];

        foreach ($commonPaths as $path) {
            if (!empty($path) && file_exists($path)) {
                return str_replace('/', DIRECTORY_SEPARATOR, $path);
            }
        }

        // 4. Default to system PATH
        return 'python';
    }
}

