<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parse(string $filePath): object
{
    if (!file_exists($filePath)) {
        throw new \Exception("File not found: {$filePath}");
    }

    $content = file_get_contents($filePath);
    if ($content === false) {
        throw new \Exception("Cannot read file: {$filePath}");
    }

    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    switch ($extension) {
        case 'yml':
        case 'yaml':
            $data = Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
            break;

        case 'json':
            $data = json_decode($content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(
                    "Invalid JSON in file '{$filePath}': " . json_last_error_msg()
                );
            }
            break;

        default:
            throw new \Exception("Unsupported file format: .{$extension}");
    }

    if (!is_object($data)) {
        $data = (object) $data;
    }

    return $data;
}
