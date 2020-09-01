<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class BattleWar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BattleWar {tool}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'クラン対戦の結果を出力する tool = bot or lobi or discord';

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
        $toolList = ['bot', 'discord'];
        $tool = $this->argument('tool');
        if (!in_array($tool, $toolList, true)) {
            exit("$tool は許可されていない出力先です");
        }

        $clash = new ClashRoyale(new Client());
        [$state, $messageList] = $clash->getWar();
        print_r($messageList);

        switch($tool) {
            case 'bot':
                echo implode("\n", $messageList);
                break;
            case 'discord':
                $discordClient = new DiscordApiClient(new Client());
                $discordClient->warMessage($state, $messageList);
                break;
        }
    }
}
