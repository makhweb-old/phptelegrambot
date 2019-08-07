<?php

namespace App\Helpers\Telegram\Store;

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Longman\TelegramBot\Conversation;

class Store
{
    use Getters, Setters, Actions;

    /**
     * @var Longman\TelegramBot\Conversation
     */
    private $store;

    /**
     * @param int $userId
     * @param int $chatId
     * @param string $commandName
     *
     * @return void
     */
    public function __construct(int $userId, int $chatId, string $commandName)
    {
        $this->store = new Conversation($userId, $chatId, $commandName);
    }

    /**
     * Getter for store
     *
     * @param string $property
     *
     * @return void
     */
    private function get(string $property)
    {
        if (Arr::has($property, $this->store->notes)) {
            return $this->store->notes[$property];
        }
    }

    /**
     * Setter for store instance
     *
     * @param string $property
     * @param mixed $payload
     *
     * @return void
     */
    private function set(string $property, $payload)
    {
        $this->store->notes[$property] = $payload;
        $this->save();
    }

    /**
     * Save state to database
     *
     * @return void
     */
    private function save()
    {
        $this->store->update();
    }
}
