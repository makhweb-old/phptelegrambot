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
use App\TelegramUser;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineQuery\InlineQueryResultArticle;
use Longman\TelegramBot\Entities\InputMessageContent\InputTextMessageContent;
use Longman\TelegramBot\Request;

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
    public function execute()
    {
        $this->inline_query = $this->getInlineQuery();
        $this->query = $this->inline_query->getQuery();
        $this->userId = $this->inline_query->getFrom()->getId();
        $this->userLanguage = TelegramUser::find(
            $this->userId
        )->selected_language;
        $this->data = ['inline_query_id' => $this->inline_query->getId()];
        $this->results = [];
        $this->category = Category::find(base64_decode($this->query));
        
        if ($this->category) {
            $articles = $this->category->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' =>
                        $product->getWithTranslations(
                            'name',
                            $this->userLanguage
                        ) .
                        ' - ' .
                        $product->price,
                    'description' => $product->getWithTranslations(
                        'description',
                        $this->userLanguage
                    ),
                    'input_message_content' => new InputTextMessageContent([
                        'message_text' => base64_encode($product->id)
                    ]),
                    'thumb_url' =>
                        'https://s82079.cdn.ngenix.net/Ktb3tEibfVFeZLr71uqeLqWg.png'
                ];
            });

            foreach ($articles as $article) {
                $results[] = new InlineQueryResultArticle($article);
            }

            $this->data['results'] = '[' . implode(',', $results) . ']';
        } else {
            $this->data['results'] = '[]';
        }

        return Request::answerInlineQuery($this->data);
    }

    protected function define()
    {
    }
}
