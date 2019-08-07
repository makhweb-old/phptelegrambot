<?php

namespace App\Helpers\Telegram\Store;

use Illuminate\Support\Collection;

trait Actions
{
    /**
     * @param array $payload
     * 
     * @return void
     */
    public function appendToBasket(array $payload)
    {
        $products = Collection::make($this->getBasket());

        $products->transform(function ($item) use ($payload) {
            if ($item['product_id'] === $payload['product_id']) {
                return $payload;
            }
            return $item;
        });
        
        $products->whenEmpty(function ($collection) use ($payload) {
            $collection->push($payload);
        });

        return $this->setBasket($collection->all());
    }
}
