<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    public static function avgPostReach()
    {
        return (int) round(self::sum('views_count') / self::count());
    }

    public static function dailyReach()
    {
        $days = self::where(
            'created_at',
            '<=',
            \Carbon\Carbon::now()
                ->subDays(1)
                ->toDateTimeString()
        )
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m-d');
            });
        $totalViews = 0;
        foreach ($days as $key => $models) {
            foreach ($models as $model) {
                $totalViews += $model->views_count;
            }
        }

        return (int) round($totalViews / $days->count());
    }

    public static function dailyPostsCount()
    {
        $days = self::get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('m-d');
        });

        $total = 0;
        foreach ($days as $key => $models) {
            $total += $models->count();
        }

        return (int) round($total / $days->count());
    }

    public static function getErr()
    {
        return (int) round(
            (self::avgPostReach() /
                Member::latest()
                    ->pluck('count')
                    ->first()) *
                100,
            1
        );
    }
}
