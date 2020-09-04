
<?php

namespace App\Console\Commands;


class PrettyMessage
{

    public function createClanMember($memberList)
    {
        foreach ($memberList as $key => $row) {
            $clanRank[$key] = $row['clanRank'];
        }
        array_multisort($clanRank, SORT_ASC, $memberList);

        $outputMessageList[] = 'クラン順位 寄付した数 もらった数 放置日 プレイヤー名';
        foreach ($memberList as $member => $value) {
            $outputMessageList[] = sprintf('%02d位 %4d枚 %4d枚 %d日 %s',
                $value['clanRank'],
                $value['donations'],
                $value['donationsReceived'],
                $value['leaveDays'],
                $member
            );
        }
        return $outputMessageList;
    }

    public function createMemberAndTag($memberList)
    {

        // tagでソートしておく
        foreach ($memberList as $key => $row) {
            $tag[$key] = $row['tag'];
        }
        array_multisort($tag, SORT_ASC, $memberList);
        $outputMessageList = [];
        foreach ($memberList as $value) {
            $outputMessageList[] = $value;
        }
        return $outputMessageList;
    }



    public function noPlayMember($memberList)
    {
        $leaveDays = 5;
        $outputMessageList = [];
        foreach ($memberList as $value) {
            if ($value['leaveDays'] >= $leaveDays) {
                $outputMessageList[] = $value;
            }
        }
        return $outputMessageList;
    }

    public function noGiftMember($memberList)
    {
        $giftCount = 100;
        $outputMessageList = [];
        foreach ($memberList as $value) {
            if ($value['donations'] < $giftCount) {
                $outputMessageList[] = $value;
            }
        }
        return $outputMessageList;
    }

    public function createNewMessage($memberList)
    {
        $outputMessageList = [];
        foreach ($memberList as $value) {
            $outputMessageList[] = sprintf("%s", $value);
        }
        if (!empty($outputMessageList)) {
            array_unshift($outputMessageList,
                sprintf('%s','名前')
            );
        }

        return $outputMessageList;
    }

    public function createNoPlayGoodByeMessage($memberList)
    {
        $outputMessageList = [];
        foreach ($memberList as $value) {
            $outputMessageList[] = sprintf("%-6s %-12s", $value['leaveDays'], $value['name']);
        }
        if (!empty($outputMessageList)) {
            array_unshift($outputMessageList,
                sprintf('%-6s %-12s','放置日', '名前')
            );
        }

        return $outputMessageList;
    }

    public function createGoodByeMessage($memberList)
    {
        $outputMessageList = [];
        foreach ($memberList as $value) {
            $outputMessageList[] = sprintf("%5d %-12s", $value['donations'], $value['name']);
        }
        if (!empty($outputMessageList)) {
            array_unshift($outputMessageList,
                sprintf('%5s %-12s','寄付数', '名前')
            );
        }

        return $outputMessageList;
    }
}
