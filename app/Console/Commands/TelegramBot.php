<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;
use PhpTelegramBot\Laravel\PhpTelegramBotContract;

class TelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:run';

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
    public function handle(PhpTelegramBotContract $telegram_bot)
    {
        while (true) {
            try {
                $telegram_bot->handleGetUpdates();
            } catch (\Throwable $e) {
            }
        }
    }
}
