<?php

use PHPUnit\Framework\TestCase;
use PrestigeDigital\Leverage\Lang\XArray;

class XArrayTestObject
{
    public $foo;

    public function __construct($foo)
    {
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }
}

class XArrayTest extends TestCase
{
    public function testDefaultConstructor()
    {
        $arr = new XArray();
        $this->assertEquals(count($arr), 0);
    }

    public function testRange()
    {
        $arr = XArray::range(0, 5);
        for ($i = 0; $i <= 5; ++$i) {
            $this->assertEquals($i, $arr[$i]);
        }
    }

    public function testRangeStep()
    {
        $arr = XArray::range(0, 5, 2);
        for ($i = 0; $i < 3; ++$i) {
            $this->assertEquals($i * 2, $arr[$i]);
        }
        $this->assertEquals(count($arr), 3);
    }

    public function testCont()
    {
        $arr = new XArray([1, 2, 3]);
        $this->assertEquals(count($arr), 3);
    }

    public function testToStrings()
    {
        $arr = new XArray(['abc', 'def', 'ghi']);
        $this->assertEquals($arr->toStrings()[0][0], 'a');
    }

    public function testEach()
    {
        $arr = new XArray([0, 1, 2]);
        $i = 0;
        $arr->each(function($x) use (&$i) {
            $this->assertEquals($x, $i++);
        });
    }

    public function testMap()
    {
        $arr = new XArray([0, 1, 2]);
        $this->assertEquals(1, $arr->map(function($x) {
            return $x + 1;
        })[0]);
    }

    public function testMapOffset()
    {
        $arr = new XArray([
            new XArray([0, 1, 2]),
            new XArray([0, 1, 2]),
            new XArray([0, 1, 2]),
        ]);
        $this->assertEquals(0, $arr->mapOffset(0)[0]);
        $this->assertEquals(0, $arr->mapOffset(0)[1]);
        $this->assertEquals(0, $arr->mapOffset(0)[2]);
    }

    public function testMapProp()
    {
        $arr = new XArray([
            new XArrayTestObject(0),
            new XArrayTestObject(1),
            new XArrayTestObject(2),
        ]);
        $this->assertEquals(0, $arr->mapProp('foo')[0]);
        $this->assertEquals(1, $arr->mapProp('foo')[1]);
        $this->assertEquals(2, $arr->mapProp('foo')[2]);
    }

    public function testMapGet()
    {
        $arr = new XArray([
            new XArrayTestObject(0),
            new XArrayTestObject(1),
            new XArrayTestObject(2),
        ]);
        $this->assertEquals(0, $arr->mapGet('foo')[0]);
        $this->assertEquals(1, $arr->mapGet('foo')[1]);
        $this->assertEquals(2, $arr->mapGet('foo')[2]);
    }

    public function testMapMethod()
    {
        $arr = new XArray([
            new XArrayTestObject(0),
            new XArrayTestObject(1),
            new XArrayTestObject(2),
        ]);
        $this->assertEquals(0, $arr->mapMethod('getFoo')[0]);
        $this->assertEquals(1, $arr->mapMethod('getFoo')[1]);
        $this->assertEquals(2, $arr->mapMethod('getFoo')[2]);
    }

    public function testReduce()
    {
        $arr = new XArray([1, 2, 3]);
        $this->assertEquals(6, $arr->reduce(0, function($a, $b) {
            return $a + $b;
        }));
    }

    public function testSum()
    {
        $arr = new XArray([1, 2, 3]);
        $this->assertEquals(6, $arr->sum());
    }

    public function testFilter()
    {
        $arr = new XArray([0, 1, 2, 3]);
        $filtered = $arr->filter(function($x) {
            return ($x % 2) == 1;
        });

        $this->assertEquals($filtered[1], 1);
        $this->assertEquals($filtered[3], 3);
        $this->assertEquals(count($filtered), 2);
    }

    public function testFilterOffset()
    {
        $arr = new XArray([
            new XArray([1, 2, 3]),
            new XArray([4, 5, 6]),
            new XArray([1, 2, 3]),
        ]);
        $filtered = $arr->filterOffset(0, 1);
        $this->assertEquals(count($filtered), 2);
        $this->assertEquals($filtered[2][0], 1);
    }

    public function testFilterOffsetCompare()
    {
        $arr = new XArray([
            new XArray([1, 2, 3]),
            new XArray([4, 5, 6]),
            new XArray([1, 2, 3]),
        ]);
        $filtered = $arr->filterOffset(0, 2, XArray::LESS_THAN);
        $this->assertEquals(count($filtered), 2);
        $this->assertEquals($filtered[2][0], 1);
    }

    public function testFilterProp()
    {
        $arr = new XArray([
            new XArrayTestObject(1),
            new XArrayTestObject(4),
            new XArrayTestObject(1),
        ]);
        $filtered = $arr->filterProp('foo', 1);
        $this->assertEquals(count($filtered), 2);
        $this->assertEquals($filtered[2]->foo, 1);
    }

    public function testFilterPropCompare()
    {
        $arr = new XArray([
            new XArrayTestObject(1),
            new XArrayTestObject(4),
            new XArrayTestObject(1),
        ]);
        $filtered = $arr->filterProp('foo', 2, XArray::LESS_THAN);
        $this->assertEquals(count($filtered), 2);
        $this->assertEquals($filtered[2]->foo, 1);
    }

    public function testFilterMethod()
    {
        $arr = new XArray([
            new XArrayTestObject(1),
            new XArrayTestObject(4),
            new XArrayTestObject(1),
        ]);
        $filtered = $arr->filterMethod('getFoo', 1);
        $this->assertEquals(count($filtered), 2);
        $this->assertEquals($filtered[2]->foo, 1);
    }

    public function testFilterMethodCompare()
    {
        $arr = new XArray([
            new XArrayTestObject(1),
            new XArrayTestObject(4),
            new XArrayTestObject(1),
        ]);
        $filtered = $arr->filterMethod('getFoo', 2, XArray::LESS_THAN);
        $this->assertEquals(count($filtered), 2);
        $this->assertEquals($filtered[2]->foo, 1);
    }

    public function testFilterGet()
    {
        $arr = new XArray([
            new XArrayTestObject(1),
            new XArrayTestObject(4),
            new XArrayTestObject(1),
        ]);
        $filtered = $arr->filterGet('foo', 1);
        $this->assertEquals(count($filtered), 2);
        $this->assertEquals($filtered[2]->foo, 1);
    }

    public function testFilterGetCompare()
    {
        $arr = new XArray([
            new XArrayTestObject(1),
            new XArrayTestObject(4),
            new XArrayTestObject(1),
        ]);
        $filtered = $arr->filterGet('foo', 2, XArray::LESS_THAN);
        $this->assertEquals(count($filtered), 2);
        $this->assertEquals($filtered[2]->foo, 1);
    }

    public function testImplode()
    {
        $arr = new XArray(['a', 'b', 'c']);
        $this->assertEquals((string)$arr->implode(), 'abc');
    }

    public function testSort()
    {
        $arr = new XArray([3, 1, 2]);
        $this->assertEquals($arr->sort()[0], 1);
        $this->assertEquals($arr[0], 3);
    }

    public function testMerge()
    {
        $arr = new XArray([0, 1, 2]);
        $arr = $arr->merge([3, 4, 5], [6, 7, 6]);
        $this->assertEquals(count($arr), 9);
    }

    public function testCollapse()
    {
        $arr = new XArray([
            new XArray([0, 1, 2]),
            new XArray([3, 4, 5]),
            new XArray([6, 7, 8]),
        ]);
        $arr = $arr->collapse();
        $this->assertEquals(count($arr), 9);
    }

    public function testSlice()
    {
        $arr = new XArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $this->assertEquals($arr->slice(2, 3)[0], 2);
        $this->assertEquals(count($arr->slice(2, 3)), 3);
    }

    public function testSliceEnd()
    {
        $arr = new XArray([0, 1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $this->assertEquals($arr->slice(8, 5)[0], 8);
        $this->assertEquals(count($arr->slice(8, 5)), 2);
    }

    public function testOffsetSet()
    {
        $arr = new XArray([0, 1, 2]);
        $arr = ($arr[1] = 2);
        echo $arr;
    }
}
