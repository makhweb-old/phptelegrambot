<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    //Bitta post o'rtacha nechta ko'rilishi soni
    public static function avgPostReach()
    {
        $posts = self::where(
            'created_at',
            '>=',
            Carbon::now()
                ->subDays(7)
                ->toDateTimeString()
        );
        return (int) round($posts->sum('views_count') / $posts->count());
    }

    //O'rtacha kunlik ko'rishlar
    public static function dailyReach()
    {
        $days = self::where(
            'created_at',
            '>=',
            Carbon::now()
                ->subDays(7)
                ->toDateTimeString()
        )
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('d');
            });
        $totalViews = 0;
        foreach ($days as $key => $models) {
            foreach ($models as $model) {
                $totalViews += $model->views_count;
            }
        }
        
        return (int) round($totalViews / $days->count());
    }

    //Kuniga o'rtacha nechta post
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

    public static function getData()
    {
        if (self::exists()) {
            return [
                'avgPostReach' => self::avgPostReach(),
                'dailyReach' => self::dailyReach(),
                'dailyPostsCount' => self::dailyPostsCount(),
                'getErr' => self::getErr()
            ];
        }
    }
}
