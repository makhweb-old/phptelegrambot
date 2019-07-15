<?php

namespace App\Http\Controllers;

use App\TelegramUser;
use PhpTelegramBot\Laravel\PhpTelegramBotContract;
use Longman\TelegramBot\Request;

class TelegramController extends Controller
{
    public function sendMessage(PhpTelegramBotContract $telegramBot)
    {
        $sendTo = request()->get('sendTo');
        $total = 0;
        $failed = 0;

        if ($sendTo['channels']) {
            $result = Request::sendMessage([
                'text' => request()->get('text'),
                'parse_mode' => 'markdown',
                'chat_id' => config('app.telegram_channel')
            ]);
            if ($result->isOk()) {
                ++$total;
            } else {
                ++$failed;
            }
        }
        if ($sendTo['users']) {
            $results = Request::sendToActiveChats(
                'sendMessage',
                ['text' => request()->get('text'), 'parse_mode' => 'markdown'],
                [
                    'users' => $sendTo['users'],
                    'groups' => false,
                    'supergroups' => false,
                    'channels' => false
                ]
            );

            foreach ($results as $result) {
                if ($result->isOk()) {
                    ++$total;
                } else {
                    ++$failed;
                }
            }
        }

        return response()->json([
            'delivered' => $total,
            'failed' => $failed
        ]);
    }

    public function usersIndex()
    {
        return TelegramUser::where('is_bot', false)->get();
    }
}
