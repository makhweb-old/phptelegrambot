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
