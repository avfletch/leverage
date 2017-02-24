<?php

namespace PrestigeDigital\Leverage\Lang;

use Countable;
use IteratorAggregate;
use ArrayAccess;
use OutOfBoundsException;

class XString implements Countable, IteratorAggregate, ArrayAccess
{
    private $str;
    private $encoding;

    public function __construct($str = '', $encoding = null)
    {
        $this->str = (string)$str;
        $this->encoding = $encoding ?: \mb_internal_encoding();
    }

    public function length()
    {
        return \mb_strlen($this->str, $this->encoding);
    }

    public function chars()
    {
        $chars = new XArray();
        $len = $this->length();
        for ($i = 0; $i < $len; ++$i) {
            $chars[] = $this[$i];
        }

        return $chars;
    }

    public function contains($s)
    {
        return (\mb_strpos($this->str, $s, 0, $this->encoding) !== false);
    }

    public function split($pattern)
    {
        return $this->explode($pattern);
    }

    public function explode($pattern)
    {
        $parts = \mb_split($pattern, $this->str);
        return (new XArray($parts))->toStrings();
    }

    public function parseCsv()
    {
        $data = new XArray(str_getcsv($this->str));
        return $data->map(function($s) {
            return (new XString($s))->trim();
        });
    }

    public function startsWith($s)
    {
        $len = \mb_strlen($s, $this->encoding);
        return \mb_substr($this->str, 0, $len, $this->encoding) === $s;
    }

    public function toInt($base = 10)
    {
        return intval($this->str, $base);
    }

    public function toLower()
    {
        $str = \mb_strtolower($this->str, $this->encoding);
        return new XString($str, $this->encoding);
    }

    public function toUpper()
    {
        $str = \mb_strtoupper($this->str, $this->encoding);
        return new XString($str, $this->encoding);
    }

    public function toTitleCase()
    {
        $str = \mb_convert_case($this->str, \MB_CASE_TITLE, $this->encoding);
        return new XString($str, $this->encoding);
    }

    public function subString($start, $length = null) {
        $length = $length ?: $this->length();
        $str = \mb_substr($this->str, $start, $length, $this->encoding);
        return new XString($str, $this->encoding);
    }

    public function replace($search, $replace)
    {
        return $this->regexReplace(preg_quote($search), $replace);
    }

    public function trim($chars = '[:space:]')
    {
        return $this->regexReplace("^[$chars]+|[$chars]+\$", '');
    }

    public function leftTrim($chars = '[:space:]')
    {
        return $this->regexReplace("^[$chars]+", '');
    }

    public function rightTrim($chars = '[:space:]')
    {
        return $this->regexReplace("[$chars]+\$", '');
    }

    public function regexReplace(
        $pattern,
        $replacement,
        $options = 'msr'
    ) {
        mb_regex_encoding($this->encoding);
        return new XString(
            \mb_ereg_replace($pattern, $replacement, $this->str, $options)
        );
    }

    public function __toString()
    {
        return $this->str;
    }

    /**
     * Implementing count for Countable allows for count($foo)
     */
    public function count()
    {
        return $this->length();
    }

    /**
     * Implementing getIterator for IteratorAggregate allows for foreach ($str)
     */
    public function getIterator()
    {
        return $this->chars()->getIterator();
    }

    public function offsetExists($offset)
    {
        $length = $this->length();

        if ($offset >= 0) {
            return $length > $offset;
        }

        return $length >= abs($offset);
    }

    public function offsetGet($offset)
    {
        $length = $this->length();

        if (($offset >= 0 && $length <= $offset) || $length < abs($offset)) {
            throw new OutOfBoundsException('No character at index: ' . $offset);
        }

        return $this->subString($offset, 1);
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception('XStrings are immutable, no direct setting chars');
    }

    public function offsetUnset($offset)
    {
        throw new Exception('XStrings are immutable, no unsetting chars');
    }
}
