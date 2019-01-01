<?php
/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-14
 * Time: 0:08
 */

namespace Applications\model;
use Applications\lib\Model;

class ChatList
{
    protected $model;

    protected $hidden = ['status'];

    protected $replace = [];

    protected $table = 'chat_list';

    public function __construct()
    {
        $this->model = new Model($this->table,$this->hidden,$this->replace);
    }

    /**
     * 好友申请
     * @param $main_id 用户id
     * @param $fiend_id 好友id
     * @param $arrangement 好友类型
     */
    public function applyFriend($main_id,$fiend_id,$arrangement)
    {
        return $this->model->insert(['main_id'=>$main_id,'friend_id'=>$fiend_id,'arrangement'=>$arrangement,'status'=>'1']);
    }

    /**
     * 同意好友申请
     * @param $main_id 用户id
     * @param $fiend_id 好友id
     * @param $arrangement 用户名
     * @return int -1不是好友
     */
    public function agreeFriend($main_id,$fiend_id,$arrangement)
    {
        if($this->model->selectOne(['main_id'=>$main_id,'friend_id'=>$fiend_id,'arrangement'=>$arrangement,'status'=>'1'])){
            return -1;
        }
        return $this->model->update(['main_id'=>$main_id,'friend_id'=>$fiend_id,'arrangement'=>$arrangement,'status'=>'3']);
    }

    /**
     * 好友状态处理
     * @param $main_id 用户id
     * @param $friend_id 好友id
     * @param $status 状态
     * @return int -2没有好友，-1还不是好友
     */
    public function statusFirend($main_id,$friend_id,$status,$arrangement)
    {
        $arr = $this->isFirend($main_id,$friend_id,$arrangement);
        if(!$arr){
            return -2;
        }
        else if($arr[0]['status'] == 1){
            return -1;
        }
        return $this->model->update(['main_id'=>$arr[0]['main_id'],'friend_id'=>$arr[0]['friend_id']],['status'=>$status]);
    }

    /**
     * 是否有好友
     * @param $main_id 用户id
     * @param $friend_id 好友id
     * @return \Applications\lib\结果集
     */
    public function isFirend($main_id,$friend_id,$arrangement)
    {
        return $this->model->selectOne('(main_id = \''.$main_id.'\' and friend_id = \''.$friend_id.'\' and arrangement = \''.$arrangement.'\' ) or (main_id = \''.$friend_id.'\' and friend_id = \''.$main_id.'\' and arrangement = \''.$arrangement.'\' )');
    }

    /**
     * 获取好友列表
     * @param $main_id 用户id
     * @return \Applications\lib\结果集
     */
    public function allFirend($main_id){
        $arr = $this->model->selectOne('(main_id = \''.$main_id.'\' or friend_id = \''.$main_id.'\' ) AND status = 3');
        return $this->listFomat($arr,$main_id);
//        return $arr;
    }

    /**
     * 转换格式
     * @param $arr
     * @param $id
     * @return array
     */
    public function listFomat($arr,$id)
    {
        $result = [];
        for ($i = 0; $i < count($arr) ; $i++){
            if($arr[$i]['main_id'] == $id){
                $result[$i]['chat_id'] = $arr[$i]['friend_id'];
                $result[$i]['arrangement'] = $arr[$i]['arrangement'];
            }else{
                $result[$i]['chat_id'] = $arr[$i]['main_id'];
                $result[$i]['arrangement'] = $arr[$i]['arrangement'];
            }
        }
        return $result;
    }

    /**
     * 转换格式
     * @param $arr
     * @return array
     */
    public function agreeFomat($arr)
    {
        $result = [];
        for ($i = 0 ; $i < count($arr) ; $i++){
            $result[$i]['chat_id'] = $arr[$i]['main_id'];
            $result[$i]['arrangement'] = $arr[$i]['arrangement'];
        }
        return $result;
    }

    /**
     * 获取聊天中群组
     * @param $main_id 用户id
     * @return \Applications\lib\结果集
     */
    public function getGroupId($main_id)
    {
        return $this->agreeFomat($this->model->selectOne(['main_id'=>$main_id,'arrangement'=>'Y','status'=>'3']));
    }

    /**
     * 查看好友申请列表
     * @return \Applications\lib\结果集
     */
    public function lookApply($main_id)
    {
        return $this->model->selectOne(['friend_id'=>$main_id,'status'=>'1']);
    }

    /**
     * 删除好友
     * @param $main_id 用户id
     * @param $friend_id 好友id
     * @param $arrang 个人/群
     * @return int
     */
    public function delFriend($main_id,$friend_id,$arrangement)
    {
        $arr = $this->isFirend($main_id,$friend_id,$arrangement);
        if($arr){
            return -2;
        }
        else if($arr[0]['status'] == 1){
            return -1;
        }
        return $this->model->update(['main_id'=>$arr[0]['main_id'],'friend_id'=>$arr[0]['friend_id'],'arrangement'=>$arr[0]['arrangement']],['status'=>5]);
    }
}