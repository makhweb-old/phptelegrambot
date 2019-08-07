<?php

namespace App\Helpers\Telegram\Core;

use Longman\TelegramBot\Entities\Keyboard as KB;
use Longman\TelegramBot\Entities\KeyboardButton;
use Illuminate\Support\Arr;

class Keyboard
{
    /**
     * @param array $payload
     *
     * @return Longman\TelegramBot\Entities\Keyboard
     */
    private function get(array $payload)
    {
        return (new KB(...$payload))
        ->setResizeKeyboard(true)
        ->setOneTimeKeyboard(true)
        ->setSelective(true);
    }

    /**
     * @param array $payloads
     *
     * @return Longman\TelegramBot\Entities\Keyboard
     */
    public function fromArray(array $payloads)
    {
        return $this->get($payloads);
    }

    /**
    * @param string $payloads
    *
    * @return Longman\TelegramBot\Entities\Keyboard
    */
    public function fromState(string $state)
    {
        return $this->getKeyboard($state);
    }

    /**
    * Force request button for contact number
    *
    * @param string $text
    *
    * @return Longman\TelegramBot\Entities\Keyboard
    */
    public function requestContact(string $text)
    {
        $keyboard = new KeyboardButton($text);
        $keyboard->setRequestContact(true);

        return $this->get(Arr::wrap($keyboard));
    }

    /**
    * Force request button for user location
    *
    * @param string $text
    *
    * @return Longman\TelegramBot\Entities\Keyboard
    */
    public function requestLocation(string $text)
    {
        $keyboard = new KeyboardButton($text);
        $keyboard->setRequestLocation(true);

        return $this->get(Arr::wrap($keyboard));
    }
}
