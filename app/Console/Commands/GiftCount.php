<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

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
    protected $description = '寄付数をカウントして結果を出力する tool = bot or discord';

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
        $toolList = ['bot', 'discord', 'sunday', 'noPlayCheck', 'test'];
        $tool = $this->argument('tool');
        if (!in_array($tool, $toolList, true)) {
            exit("$tool は許可されていない出力先です");
        }

        $client = new Client();
        $clash = new ClashRoyale($client);
        $messageList = $clash->get();

        $prettyMessage = new PrettyMessage();

        switch ($tool) {
            case 'test':
                print_r($messageList);
                print_r($prettyMessage->createMemberAndTag($messageList));
                break;
            case 'bot':
                $outputMessage = $prettyMessage->createClanMember($messageList);
                echo implode("\n", $outputMessage);
                break;

            case 'discord':
                $outputMessage = $prettyMessage->createClanMember($messageList);
                $discordClient = new DiscordApiClient(new Client());
                $discordClient->giftMessage($outputMessage);
                break;

            case 'noPlayCheck':
                $noPlayMemberList = $prettyMessage->noPlayMember($messageList);

                $goodByeMessage = $prettyMessage->createNoPlayGoodByeMessage($noPlayMemberList);
                $discordClient = new DiscordApiClient(new Client());
                $discordClient->noPlayMessage($goodByeMessage);
                break;

            case 'sunday':
                $nowMemberList = $prettyMessage->createMemberAndTag($messageList);

                $nowTagMemberList = array_combine(
                    array_column($nowMemberList, 'tag'),
                    array_column($nowMemberList, 'name')
                );

                print_r($nowTagMemberList);

                $lastTagMemberList = json_decode(
                    Storage::disk('local')->get('lastTagMemberList'), true
                );

                $newMemberList = array_diff($nowTagMemberList, $lastTagMemberList);
                Storage::disk('local')->put('lastTagMemberList', json_encode($nowTagMemberList));

                // 寄付が基準以下のメンバー
                $noGiftMemberList = $prettyMessage->noGiftMember($messageList);

                // 新しいメンバーは除外
                foreach($newMemberList as $tag => $value) {
                    foreach ($noGiftMemberList as $key => $value2) {
                        if ($tag === $value2['tag']) {
                            unset($noGiftMemberList[$key]);
                        }
                    }
                }
                $helloMessage = $prettyMessage->createNewMessage($newMemberList);

                $goodByeMessage = $prettyMessage->createGoodByeMessage($noGiftMemberList);

                $discordClient = new DiscordApiClient(new Client());
                $discordClient->helloMessage($helloMessage);
                $discordClient->noGiftMessage(($goodByeMessage));

                break;
        }
    }
}
