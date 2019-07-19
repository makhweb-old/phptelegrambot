<?php

namespace App\Helpers\Telegram;

use File;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\TelegramUser;
use App\Telegram\Actions\DefaultActions;
use App\Telegram\Actions\GeneralActions;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\KeyboardButton;

class Command extends SystemCommand
{
    use TelegramBotCore, DefaultActions, GeneralActions;

    const LANGUAGE_ACTION = "select_lang";

    const PHONE_NUMBER_ACTION = "phone_number";

    protected function getValue($type, $text)
    {
        return $this->getKey('value', $type, $text);
    }

    protected function getSelectedProduct()
    {
        return $this->get('selected_product');
    }

    protected function setSelectedProduct($payload)
    {
        return $this->set('selected_product', $payload);
    }

    protected function baseFolder()
    {
        return $this->language ?? 'default';
    }

    protected function getFilePath($filename)
    {
        return storage_path("telegram/{$this->baseFolder()}/{$filename}.json");
    }

    protected function getButtonsFromJson($type)
    {
        $json = json_decode(
            File::get(storage_path("telegram/config/buttons.json")),
            true
        );

        return Arr::get($json, $type);
    }

    protected function getKeyboard($type, $arrays = null)
    {
        $json = $this->getButtonsFromJson($type);
        $cycle = is_array($arrays);
        $arrays = $arrays ?? $json;

        foreach ($arrays as $array) {
            if (Arr::has($array, 'text')) {
                $text = $this->__($array['text']);
                $main[] = $cycle ? $text : Arr::wrap($text);
            } else {
                $main[] = $this->getKeyboard($type, $array, true);
            }
        }
        return $main;
    }

    protected function loadArray($key, $payloads, $data = [])
    {
        if ($payloads && array_check_key($payloads, $key)) {
            foreach ($payloads as $payload) {
                if (array_key_exists($key, $payload)) {
                    $data[] = $payload;
                } else {
                    $data = $this->loadArray($key, $payload, $data);
                }
            }

            return $data;
        }

        return [];
    }

    protected function getKey($key, $type, $text, $arrays = null)
    {
        $json = $this->loadArray($key, $this->getButtonsFromJson($type));
        $arrays = $arrays ?? $json;

        foreach ($arrays as $array) {
            if ($array['text'] == $text) {
                return $array[$key];
            }
        }

        return false;
    }

    protected function getButtons($buttons)
    {
        $keyboard = is_array($buttons)
            ? $buttons
            : $this->getKeyboard($buttons);

        return (new Keyboard(...$keyboard))
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(true);
    }

    protected function requestContact()
    {
        return (new Keyboard(
            (new KeyboardButton('Share Contact'))->setRequestContact(true)
        ))
            ->setOneTimeKeyboard(true)
            ->setResizeKeyboard(true)
            ->setSelective(true);
    }

    protected function defineVariables()
    {
        $this->update = $this->getUpdate();
        $this->callback_query = $this->update->getCallbackQuery();
        $this->message = $this->getMessage();

        $this->chat = $this->message->getChat();
        $this->user = $this->message->getFrom();
        $this->chat_id = $this->chat->getId();
        $this->user_id = $this->user->getId();

        $this->data = [
            'chat_id' => $this->chat_id
        ];

        if ($this->chat->isGroupChat() || $this->chat->isSuperGroup()) {
            return Request::emptyResponse();
        }

        //Conversation start
        $this->conversation = new Conversation(
            $this->user_id,
            $this->chat_id,
            $this->getName()
        );
        $this->notes = &$this->conversation->notes;

        $this->user = TelegramUser::getData($this->user_id);
        $this->language = $this->user->selected_language;
        $this->phone_number = $this->user->phone_number;

        //All data is loaded
        $this->text = $this->__(trim($this->message->getText(true)), true);
    }

    protected function getData()
    {
        $this->defineVariables();
        if ($this->message->getText() == $this->usage) {
            return $this->getStartMethod();
        }
        if ($this->getAction($this->getState(), $this->text)) {
            return $this->runAction(
                $this->getAction($this->getState(), $this->text),
                true
            );
        }
        return $this->runAction(
            $this->getState(),
            !isset($this->notes['state'])
        );
    }

    protected function getStartMethod()
    {
        $action = static::MENU_ACTION;

        if (!$this->language) {
            $action = self::LANGUAGE_ACTION;
        } elseif (static::NEED_PHONE_NUMBER && !$this->phone_number) {
            $action = self::PHONE_NUMBER_ACTION;
        }

        return $this->runAction($action, true);
    }

    protected function destroy()
    {
        $this->data = [
            'chat_id' => $this->chat_id
        ];
    }

    protected function requestData()
    {
        return [
            'chat_id' => $this->chat_id,
            'reply_markup' => Keyboard::remove(['selective' => true])
        ];
    }

    protected function runAction($action, $sendBody = false)
    {
        $this->updateState($action);

        return $this->{$this->getMethodName($action)}(
            $this->requestData(),
            $sendBody
        );
    }

    protected function logError($error)
    {
        $errorData = [
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine()
        ];

        dump($errorData);

        return Request::sendMessage([
            'chat_id' => $this->chat_id,
            'text' => "Whoops looks like something went wrong :("
        ]);
    }

    protected function run()
    {
        try {
            return Request::sendMessage($this->getData());
        } catch (\ErrorException $e) {
            if (static::IS_LOCAL) {
                $this->logError($e);
            }
        }
    }
}
