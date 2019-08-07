<?php

namespace App\Helpers\Telegram\Store;

trait Setters
{
    /**
     * @param mixed $payload
     */
    public function setSelectedProduct($payload)
    {
        $this->set('selected_product', $payload);
    }
    
    /**
     * @param mixed $payload
     */
    public function setState($payload)
    {
        $this->set('state', $payload);
    }

    /**
     * @param mixed $payload
     */
    protected function setBasket($payload)
    {
        return $this->set('basket', $payload);
    }
}
