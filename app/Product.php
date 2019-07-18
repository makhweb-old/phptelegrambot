<?php

namespace App;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const PER_PAGE = 10;

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'category_id'];

    protected $with = ['translations'];

    public function scopeData()
    {
        return self::paginate(self::PER_PAGE);
    }

    public static function lastPage()
    {
        return (int) ceil(self::count() / self::PER_PAGE);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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

        static::deleting(function ($product) {
            $product->translations()->delete();
        });
    }

    public static function updateItems($items)
    {
        foreach ($items as $item) {
            self::find($item['id'])->update(
                Arr::only($item, ['photo', 'price'])
            );
            Translation::updateItems($item['translations']);
        }
    }

    public function getWithTranslations($column, $lang)
    {
        return $this->translations()
            ->whereLang($lang)
            ->get()
            ->pluck($column)
            ->first();
    }
}
