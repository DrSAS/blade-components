<?php

namespace DrSAS\BladeComponents\Support;

class Str
{
    public static function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }
}