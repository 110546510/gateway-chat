<?php
/**
 * Created by PhpStorm.
 * UserInfo: 龙
 * Date: 2018-12-09
 * Time: 17:25
 */

namespace Applications\model;
use Applications\lib\ChatCache as ModeCache;

class ChatCache
{
    /*
     * 标识永久性存储
     */
    const  PMSL = 'permanent/';

    /*
     * 标识临时存储
     */
    const  TPRR = 'temporary/';

    /**
     * 聊天对象（个人）
     */
    const  USER = 'user/';

    /**
     * 聊天对象（群体）
     */
    const GRUOP = 'group/';

    /**
     * @param $data 保存数据
     * @param string $mode 存储的方式：永久/临时（该项指目录，不是具体操作）
     * @param string $who 一对一聊天或群聊（该项指目录，不是具体操作）
     * @param $custom 自定义目录
     * @return bool
     */
    public static function saveChatCaches($data, $who,$mode,$custom)
    {
        if($custom){
            if(file_exists(APP."/Source/ChatCache/".$mode.$who.$custom) < 1){
                mkdir(APP."/Source/ChatCache/".$mode.$who.$custom);
            }
        }
        $path = APP."/Source/ChatCache/".$mode.$who.$custom;
        $cache = new ModeCache($path);
        return $cache->saveChat($data);
    }

    /**
     * @param string $mode 存储的方式：永久/临时（该项指目录，不是具体操作）
     * @param string $who 一对一聊天或群聊（该项指目录，不是具体操作）
     * @param $custom 自定义目录
     * @return bool|string 失败返回false
     */
    public static function getChatCaches($who,$mode,$custom){
        $cache = new ModeCache(__DIR__.'/../Source/ChatCache/'+$mode+$who+$custom);
        return $cache->getChat();
    }

}