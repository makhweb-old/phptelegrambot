<?php

namespace App;

use App\Product;
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
        return self::paginate(self::PER_PAGE)->toArray();
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
}
