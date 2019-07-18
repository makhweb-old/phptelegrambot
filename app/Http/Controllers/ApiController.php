<?php

namespace App\Http\Controllers;

use App\Member;
use App\Post;
use File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image'
        ]);

        $photo = $request->file('file');

        $extension = $photo->getClientOriginalExtension();
        $photoName = Str::random(15) . "." . Str::lower($extension);

        $photo->move(public_path('photos'), $photoName);

        return response()->json([
            'url' => url("photos/$photoName")
        ]);
    }

    public function stats()
    {
        $info = json_decode(
            File::get(storage_path('telegram/channel.json')),
            true
        );

        return response()->json([
            'members' => Member::getData(),
            'posts' => Post::getData(),
            'info' => $info
        ]);
    }
}
