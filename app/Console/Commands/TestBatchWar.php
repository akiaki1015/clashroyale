<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class TestBatchWar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch:test:war';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'asdfasdf';

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
        $clash = new ClashRoyale(new Client());
        list($state, $messageList) = $clash->getWar();
        print_r($messageList);

        $lobiClient = new LobiApiClient();
        $lobiClient->warMessage($state, $messageList);
    }
}
