<?php

namespace App;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $guarded = ['id'];

    protected $hidden = [
        'translatable_type',
        'translatable_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the owning translatable model.
     */

    public function translatable()
    {
        return $this->morphTo();
    }

    public static function updateItems($items)
    {
        foreach ($items as $item) {
            self::find($item['id'])->update(
                Arr::only($item, ['name', 'description', 'lang'])
            );
        }
    }
}
