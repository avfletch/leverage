<?php

use PHPUnit\Framework\TestCase;
use PrestigeDigital\Leverage\Lang\XString;

class XStringTest extends TestCase
{
    public function testDefaultConstructor()
    {
        $str = new XString();
        $this->assertEquals((string)$str, '');
    }

    public function testLength()
    {
        $str = new XString('abc');
        $this->assertEquals($str->length(), 3);
    }

    public function testZeroLength()
    {
        $str = new XString('');
        $this->assertEquals($str->length(), 0);
    }

    public function testContains()
    {
        $str = new XString('abcdefgh');
        $this->assertTrue($str->contains('abc'));
        $this->assertTrue($str->contains('cde'));
        $this->assertTrue($str->contains('fgh'));
    }

    public function testDoesNotContain()
    {
        $str = new XString('abcdefgh');
        $this->assertFalse($str->contains('qwerty'));
    }

    public function testSplit()
    {
        $str = new XString('a,b,c');
        $parts = $str->split(',');
        $this->assertEquals((string)$parts[0], 'a');
        $this->assertEquals((string)$parts[1], 'b');
        $this->assertEquals((string)$parts[2], 'c');
    }

    public function testExplode()
    {
        $str = new XString('a,b,c');
        $parts = $str->explode(',');
        $this->assertEquals((string)$parts[0], 'a');
        $this->assertEquals((string)$parts[1], 'b');
        $this->assertEquals((string)$parts[2], 'c');
    }

    public function testParseCsv()
    {
        $str = new XString('a, "bbb", c');
        $data = $str->parseCsv();
        $this->assertEquals((string)$data[0], 'a');
        $this->assertEquals((string)$data[1], 'bbb');
    }

    public function testStartsWith()
    {
        $str = new XString('abcdefgh');
        $this->assertTrue($str->startsWith('abc'));
        $this->assertFalse($str->startsWith('qwe'));
    }

    public function testToInt()
    {
        $str = new XString('10');
        $this->assertEquals($str->toInt(), 10);
    }

    public function testToLower()
    {
        $str = new XString('AbCdEfGh');
        $this->assertEquals((string)$str->toLower(), 'abcdefgh');
    }

    public function testToUpper()
    {
        $str = new XString('AbCdEfGh');
        $this->assertEquals((string)$str->toUpper(), 'ABCDEFGH');
    }

    public function testToTitleCase()
    {
        $str = new XString('hello world');
        $this->assertEquals((string)$str->toTitleCase(), 'Hello World');
    }

    public function testSubString()
    {
        $str = new XString('abcdefgh');
        $this->assertEquals((string)$str->subString(4), 'efgh');
        $this->assertEquals((string)$str->subString(4, 2), 'ef');
        $this->assertEquals((string)$str->subString(4, 8), 'efgh');
    }

    public function testReplace()
    {
        $str = new XString('abcdefbgh');
        $this->assertEquals((string)$str->replace('a', 'z'), 'zbcdefbgh');
        $this->assertEquals((string)$str->replace('b', 'y'), 'aycdefygh');
    }

    public function testTrim()
    {
        $str = new XString("  abcdefgh\n  ");
        $this->assertEquals((string)$str->trim(), 'abcdefgh');
    }

    public function testLeftTrim()
    {
        $str = new XString('  abcdefgh  ');
        $this->assertEquals((string)$str->leftTrim(), 'abcdefgh  ');
    }

    public function testRightTrim()
    {
        $str = new XString('  abcdefgh  ');
        $this->assertEquals((string)$str->rightTrim(), '  abcdefgh');
    }

    public function testOffsetGet()
    {
        $str = new XString('abcdefgh');
        $this->assertEquals((string)$str[0], 'a');
        $this->assertEquals((string)$str[1], 'b');
    }

    public function testIteration()
    {
        $str = new XString('abcdefgh');
        $i = 0;
        foreach ($str as $ch) {
            $this->assertEquals((string)$str[$i++], $ch);
        }
    }
}
