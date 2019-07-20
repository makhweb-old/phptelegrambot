<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\AdminCommands;

use Longman\TelegramBot\Commands\AdminCommand;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

/**
 * Admin "/sendtoall" command
 */
class KillProcessCommand extends AdminCommand
{
    /**
     * @var string
     */
    protected $name = 'killprocess';

    /**
     * @var string
     */
    protected $description = 'Kill this bot :(';

    /**
     * @var string
     */
    protected $usage = '/killprocess';

    /**
     * @var string
     */
    protected $version = '1.5.0';

    /**
     * Execute command
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

        $data = [
            'chat_id' => $chat_id,
            'text'    => 'GOOD_NIGHT!',
        ];

        Request::sendMessage($data);
        exit;
    }
}
