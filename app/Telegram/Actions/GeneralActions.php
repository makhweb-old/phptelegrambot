<?php

namespace App\Telegram\Actions;

use App\Category;
use App\Product;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\InlineKeyboard;

trait GeneralActions
{
    protected function getInlineKeyboard(
        $type,
        $items,
        $data,
        $method,
        $backAction = null,
        $text = null,
        $keyboard = []
    ) {
        foreach ($items['data'] as $item) {
            $keyboard[][] = [
                "text" => $item['name'],
                'switch_inline_query_current_chat' => base64_encode($item['id'])
            ];
        }
        $className = "App\\" . ucfirst($type);
        $pagination = call_user_func($className . '::lastPage') !== 1;
        dump($pagination);
        $keyboard = array_merge(
            $keyboard,
            [
                [
                    [
                        "text" => 'Checkout 🔵',
                        "callback_data" => "show_checkout"
                    ],
                    ["text" => 'Basket 📥', "callback_data" => "show_basket"]
                ]
            ],
            $pagination
                ? $this->addPaginationButtons(
                    $items['current_page'],
                    $items['last_page'],
                    $type
                )
                : [],
            $backAction ? $this->addBackButton($backAction) : []
        );
        $keyboard = new InlineKeyboard(...$keyboard);

        $data['text'] = $text ?? "Choose:";
        $data['reply_markup'] = $keyboard;
        return $data;
    }

    protected function addBackButton($action)
    {
        return [[["text" => "Back", "callback_data" => "back.$action"]]];
    }

    protected function addPaginationButtons($current, $count, $model)
    {
        return [
            [
                [
                    "text" => "◀️",
                    "callback_data" => "page." . ($current - 1) . ".{$model}"
                ],
                [
                    "text" => "Page: {$current}/{$count}",
                    "callback_data" => "emptyResult"
                ],
                [
                    "text" => "▶️",
                    "callback_data" => "page." . ($current + 1) . ".{$model}"
                ]
            ]
        ];
    }

    protected function hideKeyboard()
    {
        $messageId = Request::sendMessage([
            'reply_markup' => Keyboard::remove(),
            'chat_id' => $this->chat_id,
            'text' => 'Loading... ℹ️'
        ])
            ->getResult()
            ->getMessageId();
        Request::deleteMessage([
            'chat_id' => $this->chat_id,
            'message_id' => $messageId
        ]);
    }

    protected function getCategoryListAction($data, $sendBody = false)
    {
        if ($sendBody) {
            $this->hideKeyboard();
        }
        $this->updateState('product_show');
        return $this->getInlineKeyboard(
            'category',
            Category::data(),
            $data,
            "product_list"
        );
    }

    protected function addCountProducts($id, $count = 1)
    {
        return [
            [
                "text" => "-",
                "callback_data" => "product_show.{$id}_" . ($count - 1)
            ],
            ["text" => $count, "callback_data" => "emptyResponse"],
            [
                "text" => "+",
                "callback_data" => "product_show.{$id}_" . ($count + 1)
            ]
        ];
    }

    protected function getPageAction($data)
    {
        $className = "App\\" . ucfirst($this->getModel());
        $lastPage = call_user_func($className . '::lastPage');

        if ($this->getArgument() && $this->getArgument() <= $lastPage) {
            $this->setCurrentPage($this->getArgument());
            return $this->runQueryAction(
                $this->getActionName("{$this->getModel()}_list")
            );
        }
    }

    protected function getBackAction($data)
    {
        $this->removeFirstElementCallbackData();
        return $this->runQueryAction($this->getActionName($this->getAction()));
    }
    protected function getInstantView($id)
    {
        return "https://t.me/iv?url=http://ce612341.ngrok.io/product/en/$id&rhash=873c4ac3fb2b6a";
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

    protected function getProductShowAction($data)
    {
        // arguments {product_id}.{product_count}
        $arguments = explode('_', $this->getArgument());
        $count = 1;

        if (count($arguments) > 1) {
            $id = $arguments[0];
            $count = $arguments[1];
        } else {
            $id = base64_decode($this->text);
        }

        if ($product = Product::find($id)) {
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

    protected function getToBasketAction($data)
    {
        $arguments = explode('_', $this->getArgument());
        $basketData = array_combine(['product_id', 'count'], $arguments);

        $this->setBasket($basketData);
        return $this->getInlineKeyboard(
            'category',
            Category::data(),
            $data,
            "product_list",
            null,
            "Appended😇\r\n\n Choose:"
        );
    }

    protected function showBasketText()
    {
        $total = 0;
        $out = "📥 Basket:\r\n\n";
        foreach ($this->getBasket() as $basket) {
            // issue
            // Product::find([]);
            // foreach -> for
            $product = Product::find($basket['product_id']);
            $total += $basket['count'] * $product->price;

            $out .= "*{$product->name}*\r\n";
            $out .=
                $basket['count'] .
                " x " .
                number_format($product->price, 0, '.', ' ') .
                " = " .
                number_format($basket['count'] * $product->price, 0, '.', ' ') .
                " sum \r\n";
        }
        $out .= "\r\n*Total: * " . number_format($total, 0, '.', ' ') . " sum";
        return $out;
    }

    protected function getShowBasketAction($data)
    {
        $data['text'] = $this->showBasketText();
        $data['parse_mode'] = 'markdown';
        $data['reply_markup'] = new InlineKeyboard(
            [
                [
                    "text" => 'Checkout 🔵',
                    "callback_data" => "show_checkout"
                ]
            ],
            ...$this->addBackButton('category_list')
        );
        return $data;
    }

    protected function getShowCheckoutAction($data)
    {
        $data['text'] = $this->showBasketText();
        $data['text'] .= "\r\n\n*Are you sure?*";
        $data['parse_mode'] = 'markdown';
        $data['reply_markup'] = new InlineKeyboard([
            [
                "text" => 'No',
                "callback_data" => "category_list"
            ],
            [
                "text" => 'Yes',
                "callback_data" => "checkout"
            ]
        ]);
        return $data;
    }

    protected function getCheckoutAction($data)
    {
        $this->deleteMessage($data);
        Request::sendMessage([
            'chat_id' => $this->chat_id,
            'text' => 'Share your location, press the button!',
            'reply_markup' => (new Keyboard(
                (new KeyboardButton('Share Location'))->setRequestLocation(true)
            ))
                ->setOneTimeKeyboard(true)
                ->setResizeKeyboard(true)
                ->setSelective(true)
        ]);
        $this->updateState('handle_location');
        return $data;
    }

    protected function deleteMessage($data)
    {
        Request::deleteMessage($data);
    }
}
