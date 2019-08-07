<?php

namespace App\Helpers\Telegram\Core;

use Illuminate\Support\Arr;

class Translator
{
    const DEFAULT_LANG = "ru";
    
    /**
     * @var string
     */
    private $language;

    /**
     * @param string language
     */
    public function __construct(string $language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $newLanguage
     *
     * @return void
     */
    public function setLanguage(string $newLanguage)
    {
        $this->language = $newLanguage;
    }

    /**
     * @return array
     */
    private function getWords()
    {
        return json_decode(
            File::get(
                storage_path("telegram/config/lang/{$this->language}.json")
            ),
            true
        );
    }

    /**
     * @param string $word
     * @param bool $reverse
     *
     * @return array
     */
    public function make(string $word, bool $reverse = false)
    {
        $words = $this->getWords();

        if ($reverse) {
            $words = array_flip($words);
        }
        return Arr::get($words, $word, $word);
    }
}
