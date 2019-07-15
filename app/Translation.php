<?php

namespace App;

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
}
