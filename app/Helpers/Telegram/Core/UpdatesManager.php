<?php

namespace App\Helpers\Telegram\Core;

class UpdatesManager
{
    /**
     * Instance of any command
     *
     * @var Longman\TelegramBot\Commands\SystemCommands\AnyCommand
     */
    protected $update;

    /**
     * @var bool
     */
    protected $isInlineCommand;

    /**
     * @param Longman\TelegramBot\Commands\SystemCommands\AnyCommand
     *
     * @return void
     */
    public function __construct($commandInstance)
    {
        if ($commandInstance->getCallbackQuery()) {
            $this->update = $commandInstance->getCallbackQuery();
            $this->isInlineCommand = true;
        } else {
            $this->update = $commandInstance;
        }
    }

    /**
     * Check command is inline
     *
     * @return bool
     */
    public function isInline()
    {
        return $this->isInlineCommand;
    }

    /**
     * @return Longman\TelegramBot\Entities\Message
     */
    private function getMessage()
    {
        return $this->update->getMessage();
    }

    /**
     * Get user's id
     *
     * @return int
     */
    public function getUserId()
    {
        // When we use inline command, user_id equals to bot id and we need to fix this!
        if ($this->isInlineCommand) {
            return $this->getChatId();
        }

        return $this->getMessage()
                    ->getFrom()
                    ->getId();
    }

    /**
     * Get chat id
     *
     * @return int
     */
    public function getChatId()
    {
        return $this->getMessage()
                    ->getChat()
                    ->getId();
    }

    /**
     * Get message id
     *
     * @return int
     */
    public function getMessageId()
    {
        return $this->getMessage()->getMessageId();
    }
}
