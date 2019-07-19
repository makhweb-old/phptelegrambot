<?php

namespace App\Telegram\Actions;

use App\Product;
use App\TelegramUser;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\InlineKeyboard;

trait MainActions
{
    protected function getReplyMenuAction($data, $sendBody)
    {
        if ($sendBody) {
            $data['reply_markup'] = $this->getButtons($this->getState());
            $data['text'] = 'Choose:';

            return $data;
        }
    }

    protected function getMainMenuAction($data, $sendBody)
    {
        $data['text'] = 'Great! Let’s place an order together? 😃';
        $data['reply_markup'] = $this->getButtons($this->getState());

        return $data;
    }

    protected function getOrderMenuAction($data, $sendBody)
    {
        $data['text'] = 'Okay, take away or delivery?';
        $data['reply_markup'] = $this->getButtons($this->getState());

        return $data;
    }

    protected function getCashbackMenuAction($data, $sendBody)
    {
        $data['reply_markup'] = $this->getButtons($this->getState());

        $data['text'] = "Card number: {$this->phone_number}\n";
        $data['text'] .= "Balance: 0 sum";

        return $data;
    }

    protected function getFeedbackAction($data, $sendBody)
    {
        if ($sendBody) {
            $data['text'] = "Thank you for choosing LES AILES!
            We will be happy if you help to improve the quality of our service!
            Rate our work on a 5 point scale.\n\nEnter your feedback, please!";
            return $data;
        }
        if (!$this->text) {
            $data['text'] = "Enter your feedback, please!🥺";
            return $data;
        }

        $data['text'] = "Thank you😘";
        Request::sendMessage($data);
        //saving!!!
        return $this->runAction(self::MENU_ACTION, true);
    }

    protected function getInformAction($data, $sendBody)
    {
        
    }

    protected function getSettingsAction($data, $sendBody)
    {
        $data['text'] = 'Hello From Settings!!!';
        $data['reply_markup'] = $this->getButtons($this->getState());
        return $data;
    }

    protected function getChangeLangAction($data, $sendBody)
    {
        if ($sendBody) {
            $data['text'] = 'Select your language:';
            $data['reply_markup'] = $this->getButtons('select_lang');

            return $data;
        }

        $lang = $this->getValue('select_lang', $this->text);

        if (!$lang) {
            $data['text'] = 'Select your language:';
            $data['reply_markup'] = $this->getButtons('select_lang');

            return $data;
        }

        $this->language = $lang;

        TelegramUser::find($this->user_id)->update([
            'selected_language' => $this->language
        ]);
        
        return $this->runAction('settings', true);
    }

    private function getProductText($product, $count)
    {
        $out = "*{$product->name}*\r\n\n";
        $out .=
            "[🛍Description](" . $this->getInstantView($product->id) . ")\r\n\n";
        $out .= "📌*Price:* {$product->price} som\r\n";
        if ($count > 1) {
            $out .=
                "📌*Total:* {$count}x - *" . $product->price * $count . " som*";
        }
        return $out;
    }

    protected function getHandleProductAction($data, $sendBody)
    {
        if ($product = Product::find(base64_decode($this->text))) {
            $count = 1;
            $data['text'] = $this->getProductText($product, $count);
            $data['reply_markup'] = new InlineKeyboard(
                $this->addCountProducts($product->id, $count),
                [
                    [
                        'text' => 'To basket 📥',
                        'callback_data' => "to_basket.{$product->id}_{$count}"
                    ]
                ]
            );
            $data['parse_mode'] = 'markdown';
        } else {
            $data['text'] = 'Product not found!';
        }

        return $data;
    }

    protected function getHandleLocationAction($data)
    {
        if ($this->message->getLocation() != null) {
            /*
            $notes['longitude'] = $message->getLocation()->getLongitude();
            $notes['latitude']  = $message->getLocation()->getLatitude();
            */

            $data['text'] = "Success✅";
            Request::sendMessage($data);
            //saving!!!
            $this->setBasket([]);
            return $this->runAction(self::MENU_ACTION, true);
        }
    }
}
