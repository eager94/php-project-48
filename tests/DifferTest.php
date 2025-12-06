<?php

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class DifferTest extends TestCase
{
    public function testFlatJsonComparison(): void
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
            __DIR__ . '/fixtures/file1.json',
            __DIR__ . '/fixtures/file2.json'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testIdenticalFiles(): void
    {
        $expected = <<<EXPECTED
{
    follow: false
    host: hexlet.io
    proxy: 123.234.53.22
    timeout: 50
}
EXPECTED;

        $actual = genDiff(
            __DIR__ . '/fixtures/file1.json',
            __DIR__ . '/fixtures/file1.json'
        );

        $this->assertEquals($expected, $actual);
    }

}