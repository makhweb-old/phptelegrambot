<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use App\Category;
use App\Product;
use App\TelegramUser;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Request;
use Illuminate\Support\Str;

/**
 * Inline query command
 *
 * Command that handles inline queries.
 */
class InlinequeryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'inlinequery';

    /**
     * @var string
     */
    protected $description = 'Reply to inline query';

    /**
     * @var string
     */
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    protected function getProducts()
    {
        if (Str::startsWith($this->query, "#")) {
            return $this->result =
                Category::find(base64_decode($this->query))->products ?? [];
        } else {
            return $this->result = Product::findWithTranslation(
                $this->userLanguage,
                $this->query
            );
        }
    }

    protected function define()
    {
        $this->inline_query = $this->getInlineQuery();
        $this->query = $this->inline_query->getQuery();
        $this->userId = $this->inline_query->getFrom()->getId();
        $this->userLanguage = TelegramUser::find(
            $this->userId
        )->selected_language;
        $this->data = ['inline_query_id' => $this->inline_query->getId()];
        $this->results = [];
    }

    protected function getList()
    {
        return $this->result->map(function ($product) {
            return [
                'id' => $product->id,
                'title' =>
                    $product->getWithTranslations('name', $this->userLanguage) .
                    ' - ' .
                    $product->price,
                'description' => $product->getWithTranslations(
                    'description',
                    $this->userLanguage
                ),
                'input_message_content' => new InputTextMessageContent([
                    'message_text' => "#" . base64_encode($product->id)
                ]),
                'thumb_url' =>
                    'https://s82079.cdn.ngenix.net/Ktb3tEibfVFeZLr71uqeLqWg.png'
            ];
        });
    }

    public function execute()
    {
        $this->define();

        if (
            mb_strlen($this->query) > 2 &&
            ($this->result = $this->getProducts())
        ) {
            foreach ($this->getList() as $article) {
                $this->results[] = new InlineQueryResultArticle($article);
            }

            $this->data['results'] = '[' . implode(',', $this->results) . ']';
        } else {
            $this->data['results'] = '[]';
        }

        return Request::answerInlineQuery($this->data);
    }
}
