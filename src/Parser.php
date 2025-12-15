<?php

namespace Differ\Parser;

use Symfony\Component\Yaml\Yaml;


function parse(string $filePath): object
{
    // Проверка существования файла
    if (!file_exists($filePath)) {
        throw new \Exception("File not found: {$filePath}");
    }

    // Чтение содержимого
    $content = file_get_contents($filePath);
    if ($content === false) {
        throw new \Exception("Cannot read file: {$filePath}");
    }

    // Определение формата по расширению
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    switch ($extension) {
        case 'yml':
        case 'yaml':
            // Парсинг YAML с преобразованием ассоциативных массивов в объекты
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