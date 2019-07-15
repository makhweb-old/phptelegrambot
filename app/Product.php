<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const PER_PAGE = 10;

    public function scopeData()
    {
        return self::paginate(self::PER_PAGE);
    }

    public static function lastPage()
    {
        return (int) ceil(self::count() / self::PER_PAGE);
    }

    /**
     * Get all of the tranlations.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
