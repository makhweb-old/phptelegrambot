<?php

namespace App\Telegram\Actions;

use App\TelegramUser;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;

trait DefaultActions
{
    protected function getSelectLangAction($data, $sendBody)
    {
        if ($sendBody) {
            $data['text'] = 'Select your language:';
            $data['reply_markup'] = $this->getButtons($this->getState());

            return $data;
        }

        $lang = $this->getValue($this->getState(), $this->text);

        if (!$lang) {
            $data['text'] = 'Select your language:';
            $data['reply_markup'] = $this->getButtons($this->getState());

            return $data;
        }

        $this->language = $lang;

        TelegramUser::find($this->user_id)->update([
            'selected_language' => $this->language
        ]);

        return $this->runAction(
            static::NEED_PHONE_NUMBER
                ? self::PHONE_NUMBER_ACTION
                : static::MENU_ACTION,
            true
        );
    }

    protected function getPhoneNumberAction($data, $sendBody)
    {
        if ($sendBody) {
            $data['text'] = 'Send your number:';
            $data['reply_markup'] = $this->requestContact();

            return $data;
        }

        if ($this->message->getContact() === null) {
            $data['text'] = 'Send your number:';
            $data['reply_markup'] = $this->requestContact();

            return $data;
        }

        //Validating
        if ($this->message->getContact()->getUserId() !== $this->user_id) {
            $data['text'] = 'Send your number plz:';
            $data['reply_markup'] = $this->requestContact();

            return $data;
        }
        $this->phone_number = $this->message->getContact()->getPhoneNumber();

        TelegramUser::find($this->user_id)->update([
            'phone_number' => trim($this->phone_number, '+')
        ]);

        return $this->runAction(static::MENU_ACTION, true);
    }
}
