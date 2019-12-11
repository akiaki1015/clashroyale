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

    // 自分のテスト環境
    //protected $uid = 'c435f11fdbbdf9069209aa3f6e5507c20036f00f';
    // アニメ倶楽部
    protected $uid = 'f165dfb2dbeed7d4155d2e739629ba7e87ec3c57';

    private $noWarString = 'クラン対戦してません';
    private $collectionString = '準備日の経過';
    private $warString = 'クラン対戦の経過';
    private $otherString = 'クラン対戦準備中';

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
        //print_r($messageList);
        //exit;
        $this->lobi($state, $messageList);
    }

    private function lobi($state, $messageList)
    {
        print_r($messageList);
        switch($state) {
            case 'notInWar':
                $outputThreadTitle = $this->noWarString;
                exit($state.':投稿しない');
                break;
            case 'collectionDay':
                $outputThreadTitle = $this->collectionString;
                break;
            case 'warDay':
                $outputThreadTitle = $this->warString;
                break;
            default:
                $outputThreadTitle = $this->otherString;
                exit($state.':投稿しない');
        }
        $messageChunk = array_chunk($messageList, 20);

        $api = new LobiAPI();
        $mail = env('LOBI_ACCOUNT');
        $password = env('LOBI_PASSWORD');
        $api->Login($mail, $password);
        $api->MakeThread($this->uid, $outputThreadTitle);
        $getThreads = $api->GetThreads($this->uid, 3);
        foreach ( $getThreads as $thread ) {
            if ( $thread->message == $outputThreadTitle) {
                $thread_id = $thread->id;
                break;
            }
            continue;
        }

        if (isset($thread_id)) {
            foreach ($messageChunk as $message) {
                sleep(5);
                $api = new LobiAPI();
                $mail = env('LOBI_ACCOUNT');
                $password = env('LOBI_PASSWORD');
                if($api->Login($mail, $password)) {
                    echo "スレッド投稿\n";
                    $api->Reply($this->uid, $thread_id, join($message, "\n"));
                } else { 
                    echo "NG";
                }
            }
        }
    }
}
