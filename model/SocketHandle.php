<?php
/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-15
 * Time: 12:43
 */

namespace Applications\model;

use \GatewayWorker\Lib\Gateway;
use Workerman\Lib\Timer;

class SocketHandle
{
    /**
     * 用户未认证定时器
     * @param $client_id 自动生成的id
     */
    public static function authTime($client_id)
    {
        $auth_timer_id = Timer::add(50, function($client_id){
            Gateway::closeClient($client_id);
        }, array($client_id), false);
        Gateway::updateSession($client_id, array('auth_timer_id' => $auth_timer_id));
    }

    /**
     * 用户认证成功删除定时器
     */
    public static function authaccess($id)
    {
        $_SESSION['uid'] = $id;
        Timer::del($_SESSION['auth_timer_id']);
    }

    /**
     * 用户绑定client_id，并且加入群组
     * @param $client_id 设备id
     * @param $id 用户名id
     * @param $group_id 群组id
     */
    public static function bind($client_id,$id,$group_id)
    {
        Gateway::bindUid($client_id,$id);
        if(!isset($group_id)){
            foreach ($group_id as $key){
                Gateway::joinGroup($client_id,$key['friend_id']);
            }
        }
    }

    /**
     * 加入群（用于群创建后)
     * @param $Client_id
     * @param $group_id
     */
    public static function joinGroup($Client_id,$group_id)
    {
        Gateway::joinGroup($Client_id,$group_id);
    }

    /**
     * 指定发送信息
     * @param $id 用户id
     * @param $message 信息
     */
    public static function sendUid($id,$message)
    {
        SocketHandle::onlyClientid($id);
        Gateway::sendToUid($id,$message);
    }

    /**
     * 群发消息
     * @param $group_id 用户id
     * @param $message 发送信息
     */
    public static function sendGroup($group_id,$message)
    {
        Gateway::sendToGroup($group_id,$message);
    }

    /**
     * 防止用户多设备id
     * @param $id 用户id
     */
    public static function onlyClientid($id){
        $arr = Gateway::getClientIdByUid($id);
        for ($i = 0 ; $i < count($arr)-2;$i++){
            Gateway::unbindUid($arr[$i],$id);
        }
    }

    /**
     * 用户意外断开连接时的操作
     * @param $client_id
     */
    public static function exitClient($client_id)
    {
        $user_id = Gateway::getUidByClientId($client_id);
        if($user_id){
            $user = new UserHandle();
            $user->exitTime($user_id);
        }
    }

    /**
     * 根据设备id发送信息
     * @param $client_id 设备id
     * @param $message 信息
     */
    public static function sendClientId($client_id,$message)
    {
        Gateway::sendToClient($client_id,$message);
    }

    public static function getOnlineUid()
    {
        return Gateway::getAllClientIdList();
    }
}