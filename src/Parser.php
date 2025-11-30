<?php

namespace Differ\Parser;

function parse($filePath)
{
    $content = file_get_contents($filePath);
    return json_decode($content);
}