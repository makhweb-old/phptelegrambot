<?php

namespace App\Helpers\Telegram\Core;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Data
{
    /**
     * @var string
     */
    const BUTTONS_PATH = "telegram/config/buttons.json";

    /**
     * @param string $type
     */
    public function getDataFromJson(string $type)
    {
        $json = json_decode(
            File::get(storage_path(self::BUTTONS_PATH)),
            true
        );

        return Arr::get($json, $type);
    }

    /**
     * Generates array for telegram keyboard
     *
     * @param array $payloads
     *
     * @return array
     */
    protected function getKeyboard(array $payloads)
    {
        foreach ($payloads as $payload) {
            if (Arr::has($payload, 'text')) {
                $main[] = $payload['text'];
            } else {
                foreach ($payload as $secPayload) {
                    $secData[] = Arr::wrap($secPayload['text']);
                }
                $main[] = $secData;
            }
        }
        return $main;
    }

    /**
     * Returns all items of array where $key exists
     *
     * @param string $key
     * @param array $payloads
     */
    private function filterBy(string $key, array $payloads)
    {
        if (!array_check_key($payloads, $key)) {
            return [];
        }

        foreach ($payloads as $payload) {
            if (Arr::has($payload, $key)) {
                $data[] = $payload;
            } else {
                $data = $this->__METHOD__($key, $payload);
            }
        }

        return $data;
    }

    /**
     * @param string $key
     * @param string $type
     *
     * @return array
     */
    private function load($key, $type)
    {
        return $this->filterBy(
            $key,
            $this->getDataFromJson($type)
        );
    }

    /**
     * Returns value of item by key
     *
     * @param string $key
     * @param string $type
     * @param string $text
     *
     * @return mixed
     */
    public function get($key, $type, $text)
    {
        foreach ($this->load($key, $type) as $array) {
            if ($array['text'] == $text) {
                return $array[$key];
            }
        }
    }
}
