<?php

namespace App\Helpers\Telegram;

use App\TelegramUser;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use Illuminate\Pagination\Paginator;
use App\Telegram\Actions\GeneralActions;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\InlineKeyboard;

class InlineCommand extends SystemCommand
{
    use TelegramBotCore, GeneralActions;

    protected function setCurrentPage($page)
    {
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
    }

    protected function defineVariables()
    {
        $this->callback_query = $this->getCallbackQuery();
        $this->callback_query_id = $this->callback_query->getId();
        $this->message_id = $this->callback_query->getMessage()->getMessageId();
        $this->chat_id = $this->callback_query
            ->getMessage()
            ->getChat()
            ->getId();
        $this->user = $this->callback_query->getMessage()->getFrom();

        //chat_id == user_id
        $this->conversation = new Conversation(
            $this->chat_id,
            $this->chat_id,
            "start"
        );
        $this->user = TelegramUser::getData($this->chat_id);
        $this->language = $this->user->selected_language;
        $this->phone_number = $this->user->phone_number;
        $this->notes = &$this->conversation->notes;
        $this->callback_data = $this->callback_query->getData();
    }

    protected function getSelectedProduct()
    {
        return $this->get('selected_product');
    }

    protected function setSelectedProduct($payload)
    {
        return $this->set('selected_product', $payload);
    }

    protected function getBasket()
    {
        return $this->get('basket');
    }

    protected function setBasket($payload)
    {
        return $this->set('basket', $payload);
    }

    protected function appendToBasket($payload)
    {
        $items = $this->getBasket() ?? [];
        $index = 0;
        $exists = false;
        foreach ($items as $item) {
            if ($item['product_id'] == $payload['product_id']) {
                $items[$index] = $payload;
                $exists = true;
            }
            $index++;
        }
        if (!$exists) {
            $items[] = $payload;
        }
        return $this->set('basket', $items);
    }

    protected function splitActionMethods()
    {
        return explode(".", $this->callback_data);
    }

    protected function getAction()
    {
        return ucfirst($this->splitActionMethods()[0]);
    }

    protected function getArgument()
    {
        return $this->splitActionMethods()[1];
    }

    protected function getModel()
    {
        return $this->splitActionMethods()[2];
    }

    protected function requestData()
    {
        return [
            'message_id' => $this->message_id,
            'chat_id' => $this->chat_id
        ];
    }

    protected function getActionName($name)
    {
        return "get" . snakeCaseToCamelCase($name, true) . "Action";
    }

    protected function getData()
    {
        //run method😏

        $this->defineVariables();

        if (method_exists($this, $this->getActionName($this->getAction()))) {
            return $this->runQueryAction(
                $this->getActionName($this->getAction())
            );
        }
    }

    protected function runQueryAction($action)
    {
        return $this->{$action}($this->requestData());
    }

    protected function removeFirstElementCallbackData()
    {
        $cbData = explode('.', $this->callback_data);
        //We need delete first element
        array_shift($cbData);
        //cb_data = new_data :)
        $this->callback_data = implode('.', $cbData);
    }

    protected function run()
    {
        try {
            return Request::editMessageText($this->getData());
        } catch (\Throwable $e) {
            dump($e->getMessage());
            $this->updateState('main_menu');
        }
    }

    protected function answerCallback($text)
    {
        $data = [
            'callback_query_id' => $this->callback_query_id,
            'show_alert' => true,
            'cache_time' => 5,
            'text' => $text
        ];
        return Request::answerCallbackQuery($data);
    }
}
