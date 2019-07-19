<?php

use Illuminate\Support\Str;

function clean($string)
{
    $string = str_replace(' ', '_', $string);

    return Str::lower(preg_replace('/[^A-Za-z0-9\_]/', '', $string));
}

function snakeCaseToCamelCase($string)
{
    return ucfirst(Str::camel($string));
}

function array_check_key($multidimensionalArray, $arrKey)
{
    $recursive = new RecursiveIteratorIterator(
        new RecursiveArrayIterator($multidimensionalArray)
    );
    foreach ($recursive as $key => $value) {
        if ($key == $arrKey) {
            return true;
        }
    }

    return false;
}
