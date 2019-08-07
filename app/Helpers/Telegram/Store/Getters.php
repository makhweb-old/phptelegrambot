<?php

namespace App\Helpers\Telegram\Store;

trait Getters
{
    /**
     * @return mixed
     */
    public function getSelectedProduct()
    {
        return $this->get('selected_product');
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->get('state');
    }

    /**
     * @return array
     */
    public function getBasket()
    {
        return $this->get('basket');
    }
}
