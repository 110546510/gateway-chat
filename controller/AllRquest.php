<?php
namespace Applications\controller;
use Applications\logic\Logic;
use Applications\model\Respson;

/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-16
 * Time: 15:02
 */

class AllRquest
{
    public static function typeHandle($requests,$client_id)
    {
        $logic = new Logic();
        $request = json_decode($requests,true);
        if(!$request['type']){
            return Respson::resultJson('非法用户1',0,0);
        }

        if(!isset($_SESSION['uid'])){
            if($request['type'] != 'login'){
                return Respson::resultJson('未登录2',0,0);
            }
        }

        switch ($request['type']){
            case 'login':
                return $logic->loginU($request,$client_id);
                break;
            case 'chat':
                $arrang = ($request['arrangement'] == 'Y')?0:1;
                return $logic->send($request,$arrang);
                break;
            case 'online':
                $logic->yesM($request);
                return '';
                break;
            case 'join':
                return ($request['arrangement'] == 'Y')?$logic->joinGroup($request):$logic->requestUser($request);
                break;
            case 'logout':
                return $logic->exitU($client_id);
                break;
            case 'getfriendlist':
                return $logic->friendList($request);
                break;
            case 'agree':
                return $logic->agreeToApply($request);
                break;
            case 'search':
                return ($request['arrangement'] == 'Y')?$logic->searchGroup($request['content']):$logic->searchUser($request['content']);
                break;
            case 'newgroup':
                return $logic->newGroup($request);
                break;
            case 'delete':
                return $logic->deleteUser($request);
                break;
            default:
                return Respson::resultJson('未知操作3',0,0);
                break;
        }
    }
}