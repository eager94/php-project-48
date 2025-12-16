<?php

namespace Differ;

use function Funct\Collection\sortBy;
use function Differ\Parser\parse;
use function Differ\FormatFactory\format;

function genDiff($file1, $file2, string $format = 'stylish'): string
{
    $data1 = parse($file1);
    $data2 = parse($file2);

    $ast = buildDiff($data1, $data2);
    return format($ast, $format);
}

function buildDiff(object $data1, object $data2): array
{
    $keys1 = array_keys(get_object_vars($data1));
    $keys2 = array_keys(get_object_vars($data2));
    $allKeys = array_unique(array_merge($keys1, $keys2));
    $sortedKeys = sortBy($allKeys, fn($key) => $key);

    $result = [];

    foreach ($sortedKeys as $key) {
        $hasInFirst = property_exists($data1, $key);
        $hasInSecond = property_exists($data2, $key);
        $value1 = $hasInFirst ? $data1->$key : null;
        $value2 = $hasInSecond ? $data2->$key : null;

        if ($hasInFirst && $hasInSecond && is_object($value1) && is_object($value2)) {
            $result[] = [
                'key' => $key,
                'type' => 'nested',
                'children' => buildDiff($value1, $value2)
            ];
            continue;
        }

        if (!$hasInSecond) {
            $result[] = [
                'key' => $key,
                'type' => 'removed',
                'value' => $value1
            ];
            continue;
        }

        if (!$hasInFirst) {
            $result[] = [
                'key' => $key,
                'type' => 'added',
                'value' => $value2
            ];
            continue;
        }

        if ($value1 === $value2) {
            $result[] = [
                'key' => $key,
                'type' => 'unchanged',
                'value' => $value1
            ];
            continue;
        }

        // Если значения разные
        $result[] = [
            'key' => $key,
            'type' => 'changed',
            'oldValue' => $value1,
            'newValue' => $value2
        ];
    }

    return $result;
}
