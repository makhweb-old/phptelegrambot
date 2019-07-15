<?php

namespace App\Helpers\Telegram;

use File;
use App\TelegramUser;
use App\Telegram\Actions\DefaultActions;
use App\Telegram\Actions\GeneralActions;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Commands\SystemCommand;

class Command extends SystemCommand
{
    use DefaultActions, GeneralActions;

    const LANGUAGE_ACTION = "select_lang";

    const PHONE_NUMBER_ACTION = "phone_number";

    protected function baseFolder()
    {
        return $this->language ?? 'default';
    }

    protected function get($key)
    {
        if(array_key_exists($key,$this->notes)){
            return $this->notes[$key];
        };
    }

    protected function set($key, $payload)
    {
        $this->notes[$key] = $payload;
    }

    protected function getFilePath($filename)
    {
        return storage_path("telegram/{$this->baseFolder()}/{$filename}.json");
    }

    protected function getJson(string $filename)
    {
        return json_decode(File::get($this->getFilePath($filename)), true);
    }

    protected function getButtonsFromJson($type)
    {
        return $this->getJson('buttons')[$type] ?? [];
    }

    protected function getKeyboard(
        $type,
        $arrays = null,
        $cycle = false,
        $main = []
    ) {
        $arrays = $arrays ?? $this->getButtonsFromJson($type);

        foreach ($arrays as $array) {
            if (array_key_exists('text', $array)) {
                $main[] = $cycle ? $array['text'] : array($array['text']);
            } else {
                $main[] = $this->getKeyboard($type, $array, true);
            }
        }
        return $main;
    }

    protected function getOnlyArray($key, $payloads, $data = [])
    {
        foreach ($payloads as $payload) {
            if (array_key_exists($key, $payload)) {
                $data[] = $payload;
            } else {
                $data = $this->getOnlyArray($key, $payload, $data);
            }
        }

        return $data;
    }

    protected function getKey($key, $type, $text, $arrays = null)
    {
        $json = $this->getOnlyArray($key, $this->getButtonsFromJson($type));
        $arrays = $arrays ?? $json;

        foreach ($arrays as $array) {
            if ($array['text'] == $text) {
                return $array[$key];
            }
        }

        return false;
    }

    protected function getAction($type, $text)
    {
        return $this->getKey('action', $type, $text) ?? false;
    }

    protected function getValue($type, $text)
    {
        return $this->getKey('value', $type, $text);
    }

    protected function getState()
    {
        return $this->get('state');
    }

    protected function setState($payload)
    {
        $this->set('state', $payload);
    }

    protected function getSelectedProduct()
    {
        return $this->get('selected_product');
    }

    protected function setSelectedProduct($payload)
    {
        return $this->set('selected_product', $payload);
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

    protected function translate($word)
    {
        return $this->getJson('texts')[$word] ?? $word;
    }

    protected function defineVariables()
    {
        $this->update = $this->getUpdate();
        $this->callback_query = $this->update->getCallbackQuery();
        $this->message = $this->getMessage();
        $this->chat = $this->message->getChat();
        $this->user = $this->message->getFrom();
        $this->text = trim($this->message->getText(true));
        $this->chat_id = $this->chat->getId();
        $this->user_id = $this->user->getId();

        $this->data = [
            'chat_id' => $this->chat_id
        ];

        if ($this->chat->isGroupChat() || $this->chat->isSuperGroup()) {
            $this->data['reply_markup'] = Keyboard::forceReply([
                'selective' => true
            ]);
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

    protected function updateState($state)
    {
        $this->setState($state);
        $this->conversation->update();
    }

    protected function getMethodName($name)
    {
        return "get" . snakeCaseToCamelCase($name, true) . "Action";
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
