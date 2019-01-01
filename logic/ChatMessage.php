<?php

namespace Applications\logic;
use Applications\model\ChatCache;

/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-15
 * Time: 21:37
 */

class ChatMessage
{
    public static function oneToOne($result)
    {
        ChatCache::saveChatCaches($result,ChatCache::USER,ChatCache::PMSL,($result['send'] > $result['to'])?$result['send'].'-'.$result['to']:$result['to'].'-'.$result['send']);
    }

    public static function oneTogroup($result)
    {
        ChatCache::saveChatCaches($result,'group/','permanent/',$result['to']);
    }
}