<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class TestBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch:test';

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
        $client = new Client();
        $clash = new ClashRoyale($client);
        $messageList = $clash->get();
        print_r($messageList);

        $lobiClient = new LobiApiClient();
        $lobiClient->giftMessage($messageList);
    }
}
