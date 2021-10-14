<?php

namespace Bulla\helper;

class Output
{
    public static function print_ln($text = '', $error = false)
    {
        $color = $error ? 'red' : 'black';
        echo "<p style='color: {$color}'>{$text}</p>";
    }

    public static function print_array($array = [])
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

    public static function print_log($text = '', $type = "[log]", $error = false)
    {
        echo $error ? "\e[0;31m{$type}\e[0m {$text}\n" : "{$type} {$text}\n";
    }
}