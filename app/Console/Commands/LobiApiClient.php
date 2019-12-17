<?php

namespace App\Console\Commands;

class LobiApiClient
{
    private $uid;
    private $mail;
    private $password;

    private $resultOutputString = '寄付数ランキング　発表';

    private $noWarString = 'クラン対戦してません';
    private $collectionString = '準備日の経過';
    private $warString = 'クラン対戦の経過';
    private $otherString = 'クラン対戦準備中';

    public function __construct()
    {
        $env = env('TEST_OR_PROD');
        $this->uid = env('LOBI_UID_' . $env);

        $this->mail = env('LOBI_ACCOUNT');
        $this->password = env('LOBI_PASSWORD');
    }

    private function getNewConnection()
    {
        return new LobiAPI();
    }

    public function giftMessage($messageList)
    {
        $messageChunk = array_chunk($messageList, 20);

        $api = $this->getNewConnection();
        $api->Login($this->mail, $this->password);
        $api->MakeThread($this->uid, $this->resultOutputString);
        $getThreads = $api->GetThreads($this->uid, 3);
        foreach ($getThreads as $thread) {
            if ($thread->message == $this->resultOutputString) {
                $thread_id = $thread->id;
                break;
            }
            continue;
        }

        if (isset($thread_id)) {
            foreach ($messageChunk as $message) {
                sleep(5);
                $api = $this->getNewConnection();
                $api->Login($this->mail, $this->password);
                echo "スレッド投稿\n";
                $api->Reply($this->uid, $thread_id, join($message, "\n"));
            }
        }
    }

    public function warMessage($state, $messageList)
    {
        switch ($state) {
            case 'notInWar':
                $outputThreadTitle = $this->noWarString;
                break;
            case 'collectionDay':
                $outputThreadTitle = $this->collectionString;
                break;
            case 'warDay':
                $outputThreadTitle = $this->warString;
                break;
            default:
                $outputThreadTitle = $this->otherString;
                exit($state . ':投稿しない');
        }
        $messageChunk = array_chunk($messageList, 20);

        $api = $this->getNewConnection();
        $api->Login($this->mail, $this->password);
        $api->MakeThread($this->uid, $outputThreadTitle);
        $getThreads = $api->GetThreads($this->uid, 3);
        foreach ($getThreads as $thread) {
            if ($thread->message == $outputThreadTitle) {
                $thread_id = $thread->id;
                break;
            }
            continue;
        }

        if (isset($thread_id)) {
            foreach ($messageChunk as $message) {
                sleep(5);
                $api = $this->getNewConnection();
                $api->Login($this->mail, $this->password);
                echo "スレッド投稿\n";
                $api->Reply($this->uid, $thread_id, join($message, "\n"));
            }
        }
    }
}
