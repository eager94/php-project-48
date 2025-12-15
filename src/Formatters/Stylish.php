<?php

namespace Differ\Formatters\Stylish;

function format(array $ast): string
{
    return formatStylish($ast, 1);
}

function formatStylish(array $ast, int $depth): string
{
    $indent = str_repeat('    ', $depth - 1);
    $lines = array_map(function ($node) use ($depth, $indent) {
        switch ($node['type']) {
            case 'nested':
                $children = formatStylish($node['children'], $depth + 1);
                return "{$indent}    {$node['key']}: {\n{$children}\n{$indent}    }";

            case 'added':
                $value = formatValue($node['value'], $depth);
                return "{$indent}  + {$node['key']}: {$value}";

            case 'removed':
                $value = formatValue($node['value'], $depth);
                return "{$indent}  - {$node['key']}: {$value}";

            case 'unchanged':
                $value = formatValue($node['value'], $depth);
                return "{$indent}    {$node['key']}: {$value}";

            case 'changed':
                $oldValue = formatValue($node['oldValue'], $depth);
                $newValue = formatValue($node['newValue'], $depth);
                return "{$indent}  - {$node['key']}: {$oldValue}\n{$indent}  + {$node['key']}: {$newValue}";

            default:
                throw new \Exception("Unknown node type: {$node['type']}");
        }
    }, $ast);

    return implode("\n", $lines);
}

function formatValue($value, int $depth): string
{
    if (is_object($value)) {
        $indent = str_repeat('    ', $depth);
        $innerIndent = str_repeat('    ', $depth + 1);
        $lines = [];

        foreach ($value as $key => $val) {
            $lines[] = "{$innerIndent}{$key}: " . formatValue($val, $depth + 1);
        }

        return "{\n" . implode("\n", $lines) . "\n{$indent}}";
    }

    // Простые значения
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    return (string) $value;
}