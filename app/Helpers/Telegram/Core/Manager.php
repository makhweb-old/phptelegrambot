<?php

namespace App\Helpers\Telegram\Core;

use App\Helpers\Telegram\Core\Factory;

class Manager
{
    /**
     * @var App\Helpers\Telegram\Core\Factory
     */
    protected $factory;

    /**
     * @param App\Helpers\Telegram\Core\Factory $factory
     *
     * @return void
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return App\Helpers\Telegram\Core\Factory
     */
    public function factory()
    {
        return $this->factory;
    }
}
