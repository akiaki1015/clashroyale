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

    // 自分のテスト環境
    //protected $uid = 'c435f11fdbbdf9069209aa3f6e5507c20036f00f';
    
    // アニメ倶楽部
    protected $uid = 'f165dfb2dbeed7d4155d2e739629ba7e87ec3c57';

    private $resultOutputString = '寄付数ランキング　発表';

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
        //print_r($messageList);
        $this->lobi($messageList);
    }

    private function lobi($messageList)
    {
        print_r($messageList);
        $messageChunk = array_chunk($messageList, 20);

        $api = new LobiAPI();
        $mail = env('LOBI_ACCOUNT');
        $password = env('LOBI_PASSWORD');
        $api->Login($mail, $password);
        $api->MakeThread($this->uid, $this->resultOutputString);
        $getThreads = $api->GetThreads($this->uid, 3);
        foreach ( $getThreads as $thread ) {
            if ( $thread->message == $this->resultOutputString) {
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
