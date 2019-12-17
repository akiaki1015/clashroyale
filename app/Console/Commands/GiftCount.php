<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class GiftCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GiftCount {tool}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '寄付数をカウントして結果を出力する tool = bot or lobi or discord';

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
        $toolList = ['bot','lobi', 'discord'];
        $tool = $this->argument('tool');
        if (!in_array($tool, $toolList, true)) {
            exit("$tool は許可されていない出力先です");
        }

        $client = new Client();
        $clash = new ClashRoyale($client);
        $messageList = $clash->get();

        switch($tool) {
            case 'bot':
                echo implode("\n", $messageList);
                break;
            case 'lobi':
                $lobiClient = new LobiApiClient();
                $lobiClient->giftMessage($messageList);
                break;

            case 'discord':
                $discordClient = new DiscordApiClient(new Client());
                $discordClient->giftMessage($messageList);
                break;
        }
    }
}
