<?php

namespace Jackiedo\LogReader;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Jackiedo\LogReader\Exceptions\UnableToRetrieveLogFilesException;

class LogReader
{
    protected string $logPath;

    public function __construct(?string $logPath = null)
    {
        $this->logPath = $logPath ?: storage_path('logs');
    }

    public function setLogPath(string $path): self
    {
        $this->logPath = $path;

        return $this;
    }

    /**
     * Return parsed logs in a shape compatible with Modules\Logs expectations.
     *
     * @return \Illuminate\Support\Collection<int, array{id:string,date:string,environment:string,level:string,file_path:string,context:string}>
     */
    public function get(): Collection
    {
        if (!is_dir($this->logPath)) {
            throw new UnableToRetrieveLogFilesException(sprintf('Log path "%s" does not exist.', $this->logPath));
        }

        $files = glob(rtrim($this->logPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.log') ?: [];
        rsort($files);

        $rows = collect();
        $counter = 1;

        foreach ($files as $filePath) {
            foreach ($this->cachedEntries((string) $filePath) as $entry) {
                $rows->push([
                    'id' => (string) $counter++,
                    'date' => (string) ($entry['date'] ?? ''),
                    'environment' => (string) ($entry['environment'] ?? 'local'),
                    'level' => (string) ($entry['level'] ?? 'info'),
                    'file_path' => str_replace('\\', '/', (string) $filePath),
                    'context' => (string) ($entry['context'] ?? ''),
                ]);
            }
        }

        return $rows;
    }

    /**
     * @return array<int, array{date:string,environment:string,level:string,context:string}>
     */
    protected function cachedEntries(string $filePath): array
    {
        $cacheKey = 'log-reader:file:' . md5($filePath);
        $lastModified = @filemtime($filePath) ?: 0;
        $fileSize = @filesize($filePath) ?: 0;
        $cached = Cache::get($cacheKey);

        if (
            is_array($cached)
            && ($cached['last_modified'] ?? null) === $lastModified
            && ($cached['file_size'] ?? null) === $fileSize
        ) {
            return $cached['entries'] ?? [];
        }

        $entries = $this->parseFile($filePath);

        Cache::put($cacheKey, [
            'last_modified' => $lastModified,
            'file_size' => $fileSize,
            'entries' => $entries,
        ], now()->addMinutes(10));

        return $entries;
    }

    /**
     * @return array<int, array{date:string,environment:string,level:string,context:string}>
     */
    protected function parseFile(string $filePath): array
    {
        $content = @file_get_contents($filePath);
        if ($content === false || $content === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\n|\r/', $content) ?: [];
        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[(?<date>[^\]]+)\]\s+(?<environment>[A-Za-z0-9_.-]+)\.(?<level>[A-Z]+):\s*(?<message>.*)$/', $line, $matches)) {
                if ($current !== null) {
                    $entries[] = $current;
                }

                $current = [
                    'date' => $this->toIsoDate((string) $matches['date']),
                    'environment' => strtolower((string) $matches['environment']),
                    'level' => strtolower((string) $matches['level']),
                    'context' => trim((string) $matches['message']),
                ];
                continue;
            }

            if ($current !== null) {
                $current['context'] = trim($current['context'] . PHP_EOL . $line);
            }
        }

        if ($current !== null) {
            $entries[] = $current;
        }

        return $entries;
    }

    protected function toIsoDate(string $raw): string
    {
        try {
            return Carbon::parse($raw)->toIso8601String();
        } catch (\Throwable $e) {
            return $raw;
        }
    }
}
