<?php

namespace App\Helpers\Telegram\Core;

use App\TelegramUser;

class User
{
    /**
     * @var App\TelegramUser
     */
    private $user;

    /**
     * @param int $userId
     * Telegram user's id
     * 
     * @return void
     */
    public function __construct(int $userId)
    {
        $this->user = TelegramUser::find($userId);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->user->exists();
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->user->selected_language;
    }

    /**
     * @return int
     */
    public function getPhoneNumber()
    {
        return $this->user->phone_number;
    }
}
