<?php
/** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

namespace Differ;

use function Funct\Collection\sortBy;
use function Differ\Parser\parse;


function genDiff($file1, $file2): string
{
    $data1 = Parser\parse($file1);
    $data2 = Parser\parse($file2);

    $diff = buildDiff($data1, $data2);
    return formatDiff($diff);
}

function buildDiff(object $data1, object $data2): array
{
    $keys1 = array_keys(get_object_vars($data1));
    $keys2 = array_keys(get_object_vars($data2));
    $allKeys = array_unique(array_merge($keys1, $keys2));
    $sortedKeys = sortBy($allKeys, fn($key) => $key);

    return array_map(function($key) use ($data1, $data2) {
        $hasInFirst = property_exists($data1, $key);
        $hasInSecond = property_exists($data2, $key);
        $value1 = $hasInFirst ? $data1->$key : null;
        $value2 = $hasInSecond ? $data2->$key : null;

        if (!$hasInSecond) {
            return ['key' => $key, 'type' => 'removed', 'value' => $value1];
        }

        if (!$hasInFirst) {
            return ['key' => $key, 'type' => 'added', 'value' => $value2];
        }

        if ($value1 === $value2) {
            return ['key' => $key, 'type' => 'unchanged', 'value' => $value1];
        }

        return [
            'key' => $key,
            'type' => 'changed',
            'oldValue' => $value1,
            'newValue' => $value2
        ];
    }, $sortedKeys);
}
function formatDiff(array $diff): string
{
    $formatLine = function($item) {
        $type = $item['type'];
        $key = $item['key'];

        $formatters = [
            'added' => fn($item) => "  + {$key}: {$item['value']}",
            'removed' => fn($item) => "  - {$key}: {$item['value']}",
            'unchanged' => fn($item) => "    {$key}: {$item['value']}",
            'changed' => fn($item) => "  - {$key}: {$item['oldValue']}\n  + {$key}: {$item['newValue']}"
        ];

        return $formatters[$type]($item);
    };

    $lines = array_map($formatLine, $diff);
    return "{\n" . implode("\n", $lines) . "\n}";
}