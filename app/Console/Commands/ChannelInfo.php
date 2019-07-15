<?php

namespace App\Console\Commands;

use File;
use danog\MadelineProto\API;
use Illuminate\Console\Command;

class ChannelInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channel:info';

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

    private function downloadPhoto($photo)
    {
        return $this->madeline->download_to_dir($photo, public_path('photos'));
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

        $info = $this->madeline->get_full_info('@lambreshop_uz');
        $path = $this->downloadPhoto(
            $this->madeline->get_download_info($info['full']['chat_photo'])
        );
        $name = explode('\\', $path);
        $filename = end($name);

        $data = [
            'title' => $info['Chat']['title'],
            'photo' => $filename,
            'username' => $info['Chat']['username'],
            'about' => $info['full']['about']
        ];
        File::put(
            storage_path('telegram/channel.json'),
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }
}
