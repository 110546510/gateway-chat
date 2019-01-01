<?php
/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-12
 * Time: 10:57
 */

namespace Applications\model;
use Applications\lib\Model;

class GroupHandle
{
    protected $model;

    protected $hidden = ['status'];

    protected $table = 'chat_group_info';

    protected $replace = ['head_path'=>['null'=>'/WechatServer/Source/Picture/group.jpg']];

    public function __construct()
    {
        $this->model = new Model($this->table,$this->hidden,$this->replace);
    }

    /**
     * 根据id获取详情
     * @param $where 群id
     * @return \Applications\lib\结果集
     */
    public function getGroupId($where)
    {
        return $this->model->selectOne(['group_id'=>$where,'status'=>'0']);
    }

    /**
     *模糊搜索
     * @param $groupname 群名
     * @return \Applications\lib\结果集
     */
    public function searchG($groupname)
    {
        return $this->model->selectOne(['name'=>['%'.$groupname.'%','like']]);
    }

    /**
     * 根据群名称获取详情
     * @param $where 群名
     * @return \Applications\lib\结果集
     */
    public function getGroupName($where)
    {
        return $this->selectOne(['name'=>$where,'status'=>'0']);
    }

    /**
     * 删除群
     * @param $where 群id
     * @return int
     */
    public function delGroup($where)
    {
        if(is_null($this->selectOne())){
            return -1;
        }
        return $this->update(['group_id'=>$where],['status'=>'1']);
    }

    /**
     * 新建群聊
     * @param $group_name 群聊名称
     * @param $group_main 群主
     * @return int
     */
    public function newGroup($group_name,$group_main)
    {
        if($this->getGroupName($group_name)){
            return -1;
        }
        return $this->model->insert(['name'=>$group_name,'main_id'=>$group_main,'group_user'=>$group_main]);
    }

    /**
     * 加入群
     * @param $where 群id
     * @param $user_id 用户id
     * @return int
     */
    public function joinGroup($where,$user_id)
    {
        $arr = $this->selectOne(['group_id'=>$where]);
        if($arr < 1){
            return -1;
        }
        return $this->model->update(['group_id'=>$where],['group_user'=>$arr[0]['group_user'].'|'.$user_id,'number'=>$arr[0]['number']+1]);
    }
}