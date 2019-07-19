<?php

namespace App\Helpers\Telegram;

use File;
use Longman\TelegramBot\Commands\SystemCommand;
use Illuminate\Support\Str;

class TelegramBotCore extends SystemCommand
{
    protected function get($key)
    {
        if (array_key_exists($key, $this->notes)) {
            return $this->notes[$key];
        }
    }

    protected function set($key, $payload)
    {
        $this->notes[$key] = $payload;
        $this->conversation->update();
    }

    protected function getAction($type, $text)
    {
        return $this->getKey('action', $type, $text) ?? false;
    }

    protected function getState()
    {
        return $this->get('state');
    }

    protected function setState($payload)
    {
        $this->set('state', $payload);
    }

    protected function __($word, $reverse = false)
    {
        $words = $this->getTexts();
        if ($reverse) {
            $words = array_flip($this->getTexts());
        }

        return $words[$word] ?? $word;
    }

    protected function updateState($state)
    {
        $this->setState($state);
        $this->conversation->update();
    }

    protected function getTexts()
    {
        if (!$this->language) {
            return [];
        }

        return json_decode(
            File::get(
                storage_path("telegram/config/lang/{$this->language}.json")
            ),
            true
        );
    }

    protected function getMethodName($name)
    {
        return "get" . Str::studly($name) . "Action";
    }
}
