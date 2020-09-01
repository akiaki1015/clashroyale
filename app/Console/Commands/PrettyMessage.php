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

    public function createMemberAndTag($memberList) {

        // tagでソートしておく
        foreach ($memberList as $key => $row) {
            $tag[$key] = $row['tag'];
        }
        array_multisort($tag, SORT_ASC, $memberList);
        $outputMessageList = [];
        foreach ($memberList as $member => $value) {
            $outputMessageList[$value['tag']] = $member;
        }
        return $outputMessageList;
    }

    public function helloMember($helloMemberList) {
        $outputMessageList = [];
        foreach ($helloMemberList as $tag => $member) {
            $outputMessageList[] = sprintf("%s %s", $member, $tag);
        }
        return $outputMessageList;
    }
}
