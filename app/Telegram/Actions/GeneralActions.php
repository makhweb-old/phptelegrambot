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
    protected function getCategoryListAction($data, $sendBody = false)
    {
        if ($sendBody) {
            $this->hideKeyboard();
        }
        $this->updateState('product_show');
        return $this->getInlineKeyboard('category', Category::data(), $data);
    }

    protected function getPageAction($data)
    {
        $className = "App\\" . ucfirst($this->getModel());
        $lastPage = call_user_func($className . '::lastPage');

        if ($this->getArgument() && $this->getArgument() <= $lastPage) {
            $this->setCurrentPage($this->getArgument());
            return $this->runQueryAction(
                ("{$this->getModel()}_list")
            );
        }
    }

    protected function getEmptyResponseAction($data)
    {
        return $data;
    }

    protected function getBackAction($data)
    {
        return $this->runQueryAction($this->getArgument());
    }

    protected function getProductShowAction($data)
    {
        // arguments {product_id}.{product_count}
        $arguments = explode('_', $this->getArgument());

        if (count($arguments) > 1) {
            [$id, $count] = $arguments;
        } else {
            $id = base64_decode(trim($this->text, "#"));
            $count = 1;
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
        $this->appendToBasket($basketData);

        return $this->getInlineKeyboard(
            'category',
            Category::data(),
            $data,
            "Appended😇\r\n\nChoose:"
        );
    }

    protected function showBasketText()
    {
        $total = 0;
        $out = "📥 Basket:\r\n\n";

        foreach ($this->getBasket() as $basket) {
            $product = Product::find($basket['product_id']);
            $total += $basket['count'] * $product->price;

            $out .=
                "*" .
                $product->getWithTranslations('name', $this->language) .
                "*\r\n";
            $out .=
                $basket['count'] .
                " x " .
                number_format($product->price, 0, '.', ' ') .
                " = " .
                number_format($basket['count'] * $product->price, 0, '.', ' ') .
                " sum \r\n";
            $buttons[] = [
                [
                    "text" =>
                        "❌" .
                        $product->getWithTranslations('name', $this->language),
                    "callback_data" => "remove.{$product->id}"
                ]
            ];
        }
        $out .= "\r\n*Total: * " . number_format($total, 0, '.', ' ') . " sum";
        return [$out, $buttons];
    }

    protected function getShowBasketAction($data)
    {
        if (!$this->getBasket()) {
            $this->answerCallback('Your basket is empty!');
            return $this->runQueryAction('category_list');
        }

        [$out, $buttons] = $this->showBasketText();

        $data['text'] = $out;
        $data['parse_mode'] = 'markdown';
        $data['reply_markup'] = new InlineKeyboard(
            ...$this->addCheckoutButton(),
            ...$buttons,
            ...$this->addWipeButton(),
            ...$this->addBackButton('category_list')
        );

        return $data;
    }

    protected function getRemoveAction($data)
    {
        $this->setBasket(
            array_filter($this->getBasket(), function ($item) {
                return $item['product_id'] != $this->getArgument();
            })
        );

        return $this->runQueryAction('show_basket');
    }

    protected function getWipeAction($data)
    {
        $this->setBasket([]);

        return $this->runQueryAction('category_list');
    }

    protected function getShowCheckoutAction($data)
    {
        $data['text'] = $this->showBasketText()[0];
        $data['text'] .= "\r\n\n*Are you sure?*";
        $data['parse_mode'] = 'markdown';
        $data['reply_markup'] = new InlineKeyboard($this->addConfirmButtons());
        return $data;
    }

    protected function getCheckoutAction($data)
    {
        $this->deleteMessage($data);

        $this->sendMessage(
            'Share your location, press the button!',
            $this->requestLocation()
        );
        $this->updateState('handle_location');
        return $data;
    }

    /**
     * Helper methods
     */

    protected function getInlineKeyboard($type, $items, $data, $text = null)
    {
        $className = "App\\" . ucfirst($type);
        $pagination = call_user_func($className . '::lastPage') !== 1;

        foreach ($items as $item) {
            $keyboard[][] = [
                "text" => $item->getWithTranslations(
                    $this->user->selected_language
                ),
                'switch_inline_query_current_chat' =>
                    "#" . base64_encode($item['id'])
            ];
        }

        $keyboard = array_merge(
            $keyboard,
            [
                [
                    [
                        'text' => 'Search 🔍',
                        'switch_inline_query_current_chat' => ''
                    ]
                ],
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
                : []
        );

        $keyboard = new InlineKeyboard(...$keyboard);

        $data['text'] = $text ?? "Choose:";
        $data['reply_markup'] = $keyboard;
        return $data;
    }

    protected function requestLocation()
    {
        return (new Keyboard(
            (new KeyboardButton('Share Location'))->setRequestLocation(true)
        ))
            ->setOneTimeKeyboard(true)
            ->setResizeKeyboard(true)
            ->setSelective(true);
    }

    protected function sendMessage($text, $replyMarkup)
    {
        return Request::sendMessage([
            'chat_id' => $this->chat_id,
            'text' => $text,
            'reply_markup' => $replyMarkup
        ]);
    }

    protected function deleteMessage($data)
    {
        Request::deleteMessage($data);
    }

    private function getProductText($product, $count)
    {
        $out =
            "*" .
            $product->getWithTranslations('name', $this->language) .
            "*\r\n\n";

        $out .=
            "📌*Price:* " .
            number_format($product->price, 0, '.', ' ') .
            " som\r\n";
        if ($count > 1) {
            $out .=
                "📌*Total:* {$count}x - *" .
                number_format($product->price * $count, 0, '.', ' ') .
                " som*";
        }
        $out .=
            "\r\n\n[🛍Description](" .
            $this->getInstantView($product->id) .
            ")";

        return $out;
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

    protected function getInstantView($id)
    {
        return "https://t.me/iv?url=" .
            route('product', [$this->language, $id]) .
            "&rhash=873c4ac3fb2b6a";
    }

    /**
     * Button methods
     */

    protected function addBackButton($action)
    {
        return [[["text" => "Back", "callback_data" => "back.$action"]]];
    }

    protected function addCheckoutButton()
    {
        return [
            [
                [
                    "text" => 'Checkout 🔵',
                    "callback_data" => "show_checkout"
                ]
            ]
        ];
    }

    protected function addWipeButton()
    {
        return [
            [
                [
                    "text" => 'Wipe 🔵',
                    "callback_data" => "wipe"
                ]
            ]
        ];
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
                    "callback_data" => "emptyResponse"
                ],
                [
                    "text" => "▶️",
                    "callback_data" => "page." . ($current + 1) . ".{$model}"
                ]
            ]
        ];
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

    protected function addConfirmButtons()
    {
        return [
            [
                "text" => 'No',
                "callback_data" => "category_list"
            ],
            [
                "text" => 'Yes',
                "callback_data" => "checkout"
            ]
        ];
    }
}
