<?php

namespace App\Console\Commands;

use App\Member;
use danog\MadelineProto\API;
use Illuminate\Console\Command;

class ChannelMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channel:members';

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

        $members_count = $this->madeline->get_full_info('@lambreshop_uz')['full'][
            'participants_count'
        ];

        Member::create([
            'count' => $members_count
        ]);
    }
}
