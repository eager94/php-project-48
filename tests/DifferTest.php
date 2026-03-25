<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use function Differ\genDiff;
use function Differ\Parser\parse;

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

    public function testJsonFormat(): void
    {
        $expected = <<<JSON
[
    {
        "key": "common",
        "type": "nested",
        "children": [
            {
                "key": "follow",
                "type": "added",
                "value": false
            },
            {
                "key": "setting1",
                "type": "unchanged",
                "value": "Value 1"
            },
            {
                "key": "setting2",
                "type": "removed",
                "value": 200
            },
            {
                "key": "setting3",
                "type": "changed",
                "oldValue": true,
                "newValue": null
            },
            {
                "key": "setting4",
                "type": "added",
                "value": "blah blah"
            },
            {
                "key": "setting5",
                "type": "added",
                "value": {
                    "key5": "value5"
                }
            },
            {
                "key": "setting6",
                "type": "nested",
                "children": [
                    {
                        "key": "doge",
                        "type": "nested",
                        "children": [
                            {
                                "key": "wow",
                                "type": "changed",
                                "oldValue": "",
                                "newValue": "so much"
                            }
                        ]
                    },
                    {
                        "key": "key",
                        "type": "unchanged",
                        "value": "value"
                    },
                    {
                        "key": "ops",
                        "type": "added",
                        "value": "vops"
                    }
                ]
            }
        ]
    },
    {
        "key": "group1",
        "type": "nested",
        "children": [
            {
                "key": "baz",
                "type": "changed",
                "oldValue": "bas",
                "newValue": "bars"
            },
            {
                "key": "foo",
                "type": "unchanged",
                "value": "bar"
            },
            {
                "key": "nest",
                "type": "changed",
                "oldValue": {
                    "key": "value"
                },
                "newValue": "str"
            }
        ]
    },
    {
        "key": "group2",
        "type": "removed",
        "value": {
            "abc": 12345,
            "deep": {
                "id": 45
            }
        }
    },
    {
        "key": "group3",
        "type": "added",
        "value": {
            "deep": {
                "id": {
                    "number": 45
                }
            },
            "fee": 100500
        }
    }
]
JSON;

        $actual = genDiff(
            __DIR__ . '/fixtures/file1.json',
            __DIR__ . '/fixtures/file2.json',
            'json'
        );

        $this->assertEquals($expected, $actual);
    }

    public function testUnknownFormatThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Unknown format/');

        genDiff(
            __DIR__ . '/fixtures/file1.json',
            __DIR__ . '/fixtures/file2.json',
            'unknown'
        );
    }
    public function testParseNonExistentFileThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/File not found/');

        parse(__DIR__ . '/fixtures/non_existent.json');
    }
}