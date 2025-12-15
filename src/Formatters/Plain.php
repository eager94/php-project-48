<?php

namespace Differ\Formatters\Plain;

function format(array $ast): string
{
    $lines = buildLines($ast);
    return implode("\n", array_filter($lines));
}

function buildLines(array $ast, string $path = ''): array
{
    $lines = array_map(function ($node) use ($path) {
        $currentPath = $path ? "{$path}.{$node['key']}" : $node['key'];

        switch ($node['type']) {
            case 'nested':
                return buildLines($node['children'], $currentPath);

            case 'added':
                $value = formatValue($node['value']);
                return "Property '{$currentPath}' was added with value: {$value}";

            case 'removed':
                return "Property '{$currentPath}' was removed";

            case 'changed':
                $oldValue = formatValue($node['oldValue']);
                $newValue = formatValue($node['newValue']);
                return "Property '{$currentPath}' was updated. From {$oldValue} to {$newValue}";

            case 'unchanged':
                return '';

            default:
                throw new \Exception("Unknown node type: {$node['type']}");
        }
    }, $ast);

    return array_reduce($lines, function ($carry, $item) {
        if (is_array($item)) {
            return array_merge($carry, $item);
        }
        $carry[] = $item;
        return $carry;
    }, []);
}

function formatValue($value): string
{
    if (is_object($value) || is_array($value)) {
        return '[complex value]';
    }

    if (is_string($value)) {
        return "'{$value}'";
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    return (string) $value;
}
