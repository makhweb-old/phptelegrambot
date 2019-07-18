<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $guarded = [];

    public static function getData()
    {
        $members = self::where(
            'created_at',
            '<=',
            Carbon::now()->toDateTimeString()
        )
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            });
        return $members
            ->map(function ($item, $key) {
                return [date('Y, F, d', strtotime($key)) => $item->first()->count];
            })
            ->collapse();
    }
}
