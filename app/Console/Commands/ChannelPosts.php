<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Post;
use danog\MadelineProto\API;
use Illuminate\Console\Command;

class ChannelPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channel:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->madeline = new API(
            storage_path('framework\sessions\TELEGRAM_SESSION_FILE')
        );
        $maxId = Post::max('id');
        $messages = $this->madeline->messages->getHistory([
            'peer' => config('app.telegram_channel'),
            'offset_id' => 0,
            'offset_date' => 0,
            'add_offset' => 0,
            'limit' => 100,
            'max_id' => 0,
            'min_id' => 0,
            'hash' => 0
        ]);

        $data = array_map(function ($message) use ($maxId) {
            if (array_key_exists('views', $message)) {
                if ($message['id'] <= $maxId) {
                    Post::find($message['id'])->update([
                        'views_count' => $message['views'],
                        'updated_at' => Carbon::now()->toDateTimeString()
                    ]);
                    return null;
                }
                return [
                    'id' => $message['id'],
                    'views_count' => $message['views'],
                    'text' => $message['message'],
                    'media' => $message['media']['_'] ?? null,
                    'created_at' => date('Y-m-d  H:i:s', $message['date']),
                    'updated_at' => Carbon::now()->toDateTimeString()
                ];
            }
        }, $messages['messages']);

        if (!empty($data)) {
            Post::insert(array_filter($data));
        }
    }
}
