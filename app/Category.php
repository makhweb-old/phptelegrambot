<?php

namespace App;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const PER_PAGE = 10;

    protected $guarded = ['id'];

    protected $hidden = ['updated_at', 'created_at', 'translations'];

    protected $appends = ['locales'];

    public function getLocalesAttribute()
    {
        $result = [];
        foreach ($this->translations as $translation) {
            $result[$translation['lang']] = $translation['name'];
        }
        return $result;
    }

    public function scopeData()
    {
        return self::paginate(self::PER_PAGE);
    }

    public static function lastPage()
    {
        return (int) ceil(self::count() / self::PER_PAGE);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productList()
    {
        return $this->products()
            ->paginate(Product::PER_PAGE)
            ->toArray();
    }

    /**
     * Get all of the tranlations.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            $category->translations()->delete();
        });
    }

    public static function createItems($items)
    {
        $category = self::create();

        foreach ($items['translations'] as $translation) {
            $category
                ->translations()
                ->create(Arr::only($translation, ['lang', 'name']));
        }

        if (!empty($items['products'])) {
            foreach ($items['products'] as $product) {
                $productModel = $category
                    ->products()
                    ->create(Arr::only($product, ['price', 'photo']));
                foreach ($product['translations'] as $productTranslation) {
                    $productModel
                        ->translations()
                        ->create(
                            Arr::only($productTranslation, [
                                'lang',
                                'name',
                                'description'
                            ])
                        );
                }
            }
        }
    }

    public static function updateItems($items)
    {
        Product::updateItems($items['products']);
        Translation::updateItems($items['translations']);
    }

    public function getWithTranslations($lang, $column = 'name')
    {
        return $this->translations()
            ->whereLang($lang)
            ->get()
            ->pluck($column)
            ->first();
    }
}
