<?php

namespace Differ\Formatters\Json;

function format(array $ast): string
{
    $result = json_encode($ast, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception("JSON encoding error: " . json_last_error_msg());
    }

    return $result;
}
