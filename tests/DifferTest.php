<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;

class DifferTest extends TestCase
{
    public function testYamlStylish(): void
    {
        $expected = <<<EXPECTED
{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: 
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}
EXPECTED;

        $actual = genDiff(
            __DIR__ . '/fixtures/file1.yml',
            __DIR__ . '/fixtures/file2.yml',
            'stylish'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testYamlPlain(): void
    {
        $expected = <<<EXPECTED
Property 'common.follow' was added with value: false
Property 'common.setting2' was removed
Property 'common.setting3' was updated. From true to null
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: [complex value]
Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
Property 'common.setting6.ops' was added with value: 'vops'
Property 'group1.baz' was updated. From 'bas' to 'bars'
Property 'group1.nest' was updated. From [complex value] to 'str'
Property 'group2' was removed
Property 'group3' was added with value: [complex value]
EXPECTED;

        $actual = genDiff(
            __DIR__ . '/fixtures/file1.yml',
            __DIR__ . '/fixtures/file2.yml',
            'plain'
        );

        $this->assertEquals($expected, $actual);
    }


    public function testJsonStylish(): void
    {
        $expected = <<<EXPECTED
{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: 
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
}
EXPECTED;

        $actual = genDiff(
            __DIR__ . '/fixtures/file1.json',
            __DIR__ . '/fixtures/file2.json',
            'stylish'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testJsonPlain(): void
    {
        $actual = genDiff(
            __DIR__ . '/fixtures/file1.json',
            __DIR__ . '/fixtures/file2.json',
            'plain'
        );

        $this->assertStringContainsString("Property 'common.follow' was added with value: false", $actual);
        $this->assertStringContainsString("Property 'common.setting2' was removed", $actual);
        $this->assertStringContainsString("Property 'group2' was removed", $actual);
        $this->assertStringContainsString("Property 'group3' was added with value: [complex value]", $actual);
    }

    public function testMixedFormats(): void
    {
        $actual = genDiff(
            __DIR__ . '/fixtures/file1.yml',
            __DIR__ . '/fixtures/file1.json'  // одинаковое содержание
        );

        $this->assertStringNotContainsString('  +', $actual);
        $this->assertStringNotContainsString('  -', $actual);
        $this->assertStringContainsString('common: {', $actual);
        $this->assertStringContainsString('group1: {', $actual);
    }

    public function testDefaultFormatIsStylish(): void
    {
        $actualWithoutFormat = genDiff(
            __DIR__ . '/fixtures/file1.yml',
            __DIR__ . '/fixtures/file2.yml'
        );

        $actualWithStylish = genDiff(
            __DIR__ . '/fixtures/file1.yml',
            __DIR__ . '/fixtures/file2.yml',
            'stylish'
        );

        $this->assertEquals($actualWithoutFormat, $actualWithStylish);
    }

    public function testIdenticalFiles(): void
    {
        $actual = genDiff(
            __DIR__ . '/fixtures/file1.yml',
            __DIR__ . '/fixtures/file1.yml'
        );

        $this->assertStringNotContainsString('  +', $actual);
        $this->assertStringNotContainsString('  -', $actual);
        $this->assertStringContainsString('common: {', $actual);
        $this->assertStringContainsString('setting1: Value 1', $actual);
    }
}