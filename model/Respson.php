<?php
/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-15
 * Time: 10:29
 */

namespace Applications\model;

date_default_timezone_set('PRC');

class Respson
{
    /**
     * 格式
     * @var array
     */
    private static $format = ['result'=>'','type'=>'','date'=>'','content'=>'','id'=>'','arrangement'=>'','send'=>'','to'=>''];

//    private static $chat_format = ['result'=>'','type'=>'','send'=>'','content'=>'','to'=>'','time'=>''];

    /**
     * json 返回处理
     * @param $message
     * @param $result
     * @return string
     */
    public static function resultJson($message = '',$result,$type,$id='',$arrangement = '')
    {
        self::$format['arrangement'] = $arrangement;
        self::$format['id'] = $id;
        self::$format['content'] = $message;
        self::$format['type'] = $type;
        self::$format['result'] = $result;
        self::$format['date'] = date('Y-m-d h:i:s', time());
        $rs = json_encode(self::$format)."\n";
        return $rs;
    }
}