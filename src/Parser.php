<?php

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

function parse(string $filePath): object
{
    // Проверка существования файла
    if (!file_exists($filePath)) {
        throw new Exception("File not found: {$filePath}");
    }

    $content = file_get_contents($filePath);

    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    switch ($extension) {
        case 'yml':
        case 'yaml':
            return Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);

        case 'json':
            $data = json_decode($content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON: " . json_last_error_msg());
            }
            return $data;

        default:
            throw new Exception("Unsupported format: .{$extension}");
    }
}