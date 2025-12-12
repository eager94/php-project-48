<?php

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class DifferTest extends TestCase
{
    public function testFlatYamlComparison(): void
    {
        $expected = <<<EXPECTED
{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}
EXPECTED;

        $actual = genDiff(
            __DIR__ . '/fixtures/file1.yml',
            __DIR__ . '/fixtures/file2.yml'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testMixedFormats(): void
    {
        // Сравнение JSON и YAML с одинаковыми данными
        $actual = genDiff(
            __DIR__ . '/fixtures/file1.json',
            __DIR__ . '/fixtures/file1.yml'  // одинаковое содержание
        );

        // Все ключи должны быть unchanged (без + или -)
        $this->assertStringNotContainsString('  +', $actual);
        $this->assertStringNotContainsString('  -', $actual);
        $this->assertStringContainsString('    host:', $actual);
        $this->assertStringContainsString('    timeout:', $actual);
    }
}