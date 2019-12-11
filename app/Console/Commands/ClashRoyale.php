<?php

namespace App\Console\Commands;

class ClashRoyale
{
    private $client;
    private $option;
    private $clansUrl = 'https://api.clashroyale.com/v1/clans/%23PLY98RCJ';
    private $warUrl = 'https://api.clashroyale.com/v1/clans/%23PLY98RCJ/currentwar';

    public function __construct($client)
    {
        $this->client = $client;
        $this->option = [
            'headers' => [
                'authorization' => "Bearer " . env('CLASHROYALE_TOKEN'),
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
        ];
    }

    public function get()
    {
        $memberList = [];
        $response = $this->client->request('GET', $this->clansUrl, $this->option);
        if ($response->getStatusCode() != '200') {
            echo "clans url is not";
        } else {
            $results['clans'] = json_decode($response->getBody()->getContents());
            $memberList = $this->getMemberList($results['clans']->memberList);
        }

        return $this->getMessage($memberList);
    }

    public function getWar()
    {
        $currentwar = [];
        $response = $this->client->request('GET', $this->warUrl, $this->option);
        if ($response->getStatusCode() != '200') {
            echo "war url is not";
        } else {
            $results['war'] = json_decode($response->getBody()->getContents());
            $state = $results['war']->state;
            switch ($state) {
                case 'notInWar':
                    return [$state, []];
                case 'collectionDay':
                    $currentWar = $this->getCurrentWar($results['war']->participants);
                    return [$state, $this->getMessageCollectionDay($currentWar)];
                case 'warDay':
                    $currentWar = $this->getCurrentWar($results['war']->participants);
                    return [$state, $this->getMessageWarDay($currentWar)];
                default:
                    return [$state, []];

            }
        }
    }

    private function getMemberList($memberList = [])
    {
        if (empty($memberList)) {
            exit("データ取れない");
        }

        $output = [];
        $nowTime = new \DateTime('now');
        foreach ($memberList as $member) {
            $time = new \DateTime(substr($member->lastSeen, 0, -5));
            $output[$member->name] = [
                'donations' => $member->donations,
                'leaveDays' => $nowTime->diff($time)->days,
                'donationsReceived' => $member->donationsReceived,
            ];
        }
        return $output;
    }

    private function getCurrentWar($participants = [])
    {
        if (empty($participants)) {
            exit("データ取れない");
        }

        $output = [];
        foreach ($participants as $member) {
            $output[$member->name] = [
                'cardsEarned' => $member->cardsEarned ?? 0,
                'collectionDayBattlesPlayed' => $member->collectionDayBattlesPlayed ?? 0,
                'wins' => $member->wins ?? 0,
                'battlesPlayed' => $member->battlesPlayed ?? 0,
                'numberOfBattles' => $member->numberOfBattles ?? 0,
            ];
        }

        return $output;
    }


    private function getMessage($output)
    {
        // ソートするときの列を取り出す
        foreach ($output as $key => $row) {
            $donations[$key] = $row['donations'] ?? 0;
        }
        array_multisort($donations, SORT_DESC, $output);

        $outputMessageList[] = '順位 寄付した数 もらった数 放置日 プレイヤー名';
        $count = count($output);
        $beforeDonation = null;
        $i = 0;
        $j = 0; // ランク用
        foreach ($output as $member => $value) {
            $donation = $value['donations'] ?? 0;
            if ($donation != $beforeDonation) {
                $i++;
                if ($j > 0) {
                    $i += $j;
                    $j = 0;
                }
            } else {
                $j++;
            }
            $beforeDonation = $donation;

            $outputMessageList[] = sprintf('%02d位 %4d枚 %4d枚 %d日 %s',
                $i,
                $donation,
                $value['donationsReceived'],
                $value['leaveDays'],
                $member
            );
        }
        return $outputMessageList;
    }

    private function getMessageCollectionDay($output)
    {
        // ソートするときの列を取り出す
        foreach ($output as $key => $row) {
            $cardsEarned[$key] = $row['cardsEarned'];
            $collectionDayBattlesPlayed[$key] = $row['collectionDayBattlesPlayed'];
            $wins[$key] = $row['wins'];
        }
        array_multisort(
            $cardsEarned, SORT_DESC,
            $collectionDayBattlesPlayed, SORT_DESC,
            $wins, SORT_DESC,
            $output
        );
        $cardsEarned = '獲得数';
        $collectionDayBattlesPlayed = '準備対戦数';
        $wins = '勝利数';

        $outputMessageList[] = $cardsEarned . '/' . $collectionDayBattlesPlayed . '/' . $wins . ' プレイヤー名';
        foreach ($output as $member => $value) {
            $outputMessageList[] = sprintf("%4d/%d/%d %s",
                $value['cardsEarned'],
                $value['collectionDayBattlesPlayed'],
                $value['wins'],
                $member
            );
        }
        return $outputMessageList;
    }

    private function getMessageWarDay($output)
    {
        // ソートするときの列を取り出す
        foreach ($output as $key => $row) {
            $wins[$key] = $row['wins'];
            $battlesPlayed[$key] = $row['battlesPlayed'];
            $numberOfBattles[$key] = $row['numberOfBattles'];
        }
        array_multisort(
            $wins, SORT_DESC,
            $battlesPlayed, SORT_DESC,
            $numberOfBattles, SORT_DESC,
            $output
        );

        $outputMessageList[] = '勝利数/プレイ数/バトル数 プレイヤー名';
        foreach ($output as $member => $value) {
            $outputMessageList[] = sprintf("%d/%d/%d %s",
                $value['wins'],
                $value['battlesPlayed'],
                $value['numberOfBattles'],
                $member
            );
        }
        return $outputMessageList;
    }
}
