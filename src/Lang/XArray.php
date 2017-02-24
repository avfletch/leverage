<?php

namespace PrestigeDigital\Leverage\Lang;

use BadMethodCallException;
use ArrayAccess;
use IteratorAggregate;
use Countable;
use ArrayIterator;

class XArray implements ArrayAccess, IteratorAggregate, Countable
{
    const STRICT_EQUAL = 0;
    const LESS_THAN = 1;
    const LESS_THAN_EQUAL = 2;
    const EQUAL = 3;
    const GREATER_THAN_EQUAL = 4;
    const GREATER_THAN = 5;
    const NOT_EQUAL = 6;

    protected $array;

    public static function range($start, $end, $step = 1)
    {
        return new XArray(range($start, $end, $step));
    }

    public function __construct($array = [])
    {
        reset($array);
        $this->array = $array;
    }

    public function getArray()
    {
        return $this->array;
    }

    public function toStrings()
    {
        return $this->map(function($s) {
            return new XString($s);
        });
    }

    public function each($callback)
    {
        foreach ($this->array as $key => $val) {
            $callback($key, $val);
        }
        return $this;
    }

    public function walk($callback, $userData = null)
    {
        return array_walk($this->array, $callback, $userData);
    }

    public function map($callback, ...$params)
    {
        return new XArray(array_map($callback, $this->array));
    }

    public function mapOffset($offset)
    {
        return $this->map(function($x) use ($offset) {
            return $x[$offset];
        });
    }

    public function mapProp($name)
    {
        return $this->map(function($obj) use ($name) {
            return $obj->$name;
        });
    }

    public function mapGet($name)
    {
        return $this->mapMethod('get' . $name);
    }

    public function mapMethod($name, ...$args)
    {
        return $this->map(function($obj) use ($name, $args) {
            return $obj->$name(...$args);
        });
    }

    public function reduce($initial, $callback)
    {
        return array_reduce($this->array, $callback, $initial);
    }

    public function sum()
    {
        return array_sum($this->array);
    }

    public function filter($callback)
    {
        return new XArray(array_filter($this->array, $callback));
    }

    public function filterOffset($index, $val, $mode = self::STRICT_EQUAL)
    {
        return $this->filter(function($x) use ($index, $val, $mode) {
            return $this->compare($x[$index], $val, $mode);
        });
    }

    public function filterProp($name, $val, $mode = self::STRICT_EQUAL)
    {
        return $this->filter(function($obj) use ($name, $val, $mode) {
            return $this->compare($obj->$name, $val, $mode);
        });
    }

    public function filterMethod($name, $val, $mode = self::STRICT_EQUAL)
    {
        return $this->filter(function($obj) use ($name, $val, $mode) {
            return $this->compare($obj->$name(), $val, $mode);
        });
    }

    public function filterGet($name, $val, $mode = self::STRICT_EQUAL)
    {
        return $this->filterMethod('get' . $name, $val, $mode);
    }

    private function compare($lhs, $rhs, $mode)
    {
        switch ($mode)
        {
            case self::STRICT_EQUAL: return $lhs === $rhs;
            case self::LESS_THAN: return $lhs < $rhs;
            case self::LESS_THAN_EQUAL: return $lhs <= $rhs;
            case self::EQUAL: return $lhs == $rhs;
            case self::GREATER_THAN_EQUAL: return $lhs >= $rhs;
            case self::GREATER_THAN: return $lhs > $rhs;
            case self::NOT_EQUAL: return $lhs != $rhs;
        }

        throw Exception('Unknown compare mode: ' . $mode);
    }

    public function implode($glue = '')
    {
        return new XString(implode($glue, $this->array));
    }

    public function merge(...$xs)
    {
        $arr = $this->array;
        foreach ($xs as $x) {
            if (is_array($x)) {
                $arr = array_merge($arr, $x);
            } else {
                $arr = array_merge($arr, $x->array);
            }
        }

        return new XArray($arr);
    }

    public function collapse()
    {
        return $this->reduce(new XArray(), function($c, $x) {
            return $c->merge($x);
        });
    }

    public function slice($offset, $length = null, $preserve_keys = false)
    {
        return new XArray(array_slice(
            $this->array, $offset, $length, $preserve_keys
        ));
    }

    public function sort($flags = null)
    {
        $arr = $this->array;
        sort($arr, $flags);
        return new XArray($arr);
    }

    public function ksort($flags = null)
    {
        $arr = $this->array;
        ksort($arr, $flags);
        return new XArray($arr);
    }

    public function rsort($flags = null)
    {
        $arr = $this->array;
        rsort($arr, $flags);
        return new XArray($arr);
    }

    public function asort($flags = null)
    {
        $arr = $this->array;
        asort($arr, $flags);
        return new XArray($arr);
    }

    public function usort($callback)
    {
        $arr = $this->array;
        usort($arr, $callback);
        return new XArray($arr);
    }

    public function xsort()
    {
        $arr = $this->array;
        usort($arr, function($lhs, $rhs) {
            return $lhs->compare($rhs);
        });
        return new XArray($arr);
    }

    public function xrsort()
    {
        $arr = $this->array;
        usort($arr, function($lhs, $rhs) {
            return $rhs->compare($lhs);
        });
        return new XArray($arr);
    }

    public function reverse($preserve_keys = false)
    {
        return new XArray(array_reverse($this->array, $preserve_keys));
    }

    public function search($needle, $strict = false)
    {
        return array_search($needle, $this->array, $strict);
    }

    public function contains($needle)
    {
        return in_array($needle, $this->array);
    }

    public function __toString()
    {
        return 'X' . print_r($this->array, true);
    }

    public function keyExists($key)
    {
        return $this->offsetExists($key);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->array);
    }

    public function offsetGet($offset)
    {
        return $this->array[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $arr = $this->array;

        if ($offset == null) {
            $arr[] = $value;
            return;
        }

        $arr[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
        return $this;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->array);
    }

    public function count()
    {
        return count($this->array);
    }
}
