<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    protected $table = "user";

    protected $guarded = [];

    protected $hidden = ['is_bot','updated_at'];

    public static function getData($id)
    {
        return self::whereId($id)
            ->get(['selected_language', 'phone_number'])
            ->first();
    }
}
