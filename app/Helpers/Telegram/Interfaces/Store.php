<?php

namespace App\Helpers\Telegram\Interfaces;

interface Store
{
    public function get(string $property) : void;

    public function set(string $property, $payload) :void;
}
