<?php

namespace PrestigeDigital\Leverage\Lang;

class X
{
    public static function scandir(...$params)
    {
        $names = call_user_func_array('scandir', $params);
        return (new XArray($names))->map(function($name) {
            return new XString($name);
        });
    }

    public static function file_get_contents($filename)
    {
        return new XString(file_get_contents($filename));
    }
}
