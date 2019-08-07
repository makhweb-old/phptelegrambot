<?php

namespace App\Helpers\Telegram\Core;

use App\Helpers\Telegram\Core\Data;
use App\Helpers\Telegram\Core\Translator;
use App\Helpers\Telegram\Core\User;
use App\Helpers\Telegram\Core\Keyboard;
use App\Helpers\Telegram\Store\Store;
use App\Helpers\Telegram\Core\UpdatesManager;

class Factory
{
    /**
     * @var App\Helpers\Telegram\Core\Data
     */
    private $data;

    /**
     * @var App\Helpers\Telegram\Core\Translator
     */
    private $translator;

    /**
     * @var App\Helpers\Telegram\Core\User
     */
    private $user;

    /**
     * @var App\Helpers\Telegram\Core\Button
     */
    private $button;

    /**
     * @var App\Helpers\Telegram\Store\Store
     */
    private $store;

    /**
     * @param App\Helpers\Telegram\Core\Data $data
     *
     * @return void
     */
    public function __construct(UpdatesManager $update)
    {
        $this->data = new Data;
        $this->user = new User($update->getUserId());

        if ($this->user->exists()) {
            $lang = $this->user->getLanguage();
        } else {
            $lang = Translator::DEFAULT_LANG;
        }

        $this->translator = new Translator($lang);
        $this->keyboard = new Keyboard;

        $this->store = new Store(
            $message->getUserId(),
            $message->getChatId(),
            "start"
        );
    }

    /**
     * @return App\Helpers\Telegram\Core\Data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return App\Helpers\Telegram\Core\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return App\Helpers\Telegram\Core\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return App\Helpers\Telegram\Core\Data
     */
    public function getKeyboard()
    {
        return $this->keyboard;
    }

    /**
     * @return App\Helpers\Telegram\Store\Store
     */
    public function getStore()
    {
        return $this->store;
    }
}
