<?php

namespace App\Console\Commands;

class DiscordApiClient
{
    private $giftChannel;
    private $warChannel;

    private $client;
    private $option;

    private $host = 'https://discordapp.com/api';

    private $resultOutputString = 'ランキング 発表';

    private $helloOutputString = '今週加入したクランメンバー';

    private $noWarString = 'クラン対戦してません';
    private $collectionString = '準備日の経過';
    private $warString = 'クラン対戦の経過';
    private $otherString = 'クラン対戦準備中';

    public function __construct($client)
    {
        $env = env('TEST_OR_PROD');
        $token = env('DISCORD_TOKEN_' . $env);

        $this->giftChannel = env('DISCORD_GIFT_CHANNEL_' . $env);
        $this->warChannel = env('DISCORD_WAR_CHANNEL_' . $env);

        $this->client = $client;
        $this->option = [
            'headers' => [
                'Authorization' => 'Bot ' . $token,
                'Content-type' => 'application/json',
            ],
            'http_errors' => false
        ];
    }


    public function giftMessage($messageList)
    {
        $message['embed'] =
            [
                'title' => $this->resultOutputString,
                'description' => implode("\n", $messageList)
            ];
        $this->option['json'] = $message;
        $url = $this->host . '/channels/' . $this->giftChannel . '/messages';
        $response = $this->client->request('POST', $url, $this->option);
        echo $response->getBody()->getcontents();
    }

    public function helloMessage($messageList)
    {
        $message['embed'] =
            [
                'title' => $this->helloOutputString,
                'description' => implode("\n", $messageList)
            ];
        $this->option['json'] = $message;
        $url = $this->host . '/channels/' . $this->giftChannel . '/messages';
        $response = $this->client->request('POST', $url, $this->option);
        echo $response->getBody()->getcontents();
    }

    public function warMessage($state, $messageList)
    {
        switch ($state) {
            case 'notInWar':
                $title = $this->noWarString;
                break;
            case 'collectionDay':
                $title = $this->collectionString;
                break;
            case 'warDay':
                $title = $this->warString;
                break;
            default:
                $title = $this->otherString;
                exit($state . ':投稿しない');
        }

        $message['embed'] =
            [
                'title' => $title,
                'description' => implode("\n", $messageList)
            ];
        $this->option['json'] = $message;
        $url = $this->host . '/channels/' . $this->warChannel . '/messages';
        $response = $this->client->request('POST', $url, $this->option);
        echo $response->getBody()->getcontents();
    }
}
