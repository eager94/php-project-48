<?php

namespace Differ\FormatFactory;

use function Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Formatters\Plain\format as formatPlain;
use function Differ\Formatters\Json\format as formatJson;

function format(array $ast, string $formatName): string
{
    switch ($formatName) {
        case 'stylish':
            return "{\n" . formatStylish($ast) . "\n}";
        case 'plain':
            return formatPlain($ast);
        case 'json':
            return formatJson($ast);
        default:
            throw new \Exception("Unknown format: {$formatName}");
    }
}
