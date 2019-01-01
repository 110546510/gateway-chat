<?php
/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-12
 * Time: 10:57
 */

namespace Applications\model;
use Applications\lib\Model;

class UserHandle
{
    protected $model;

    protected $hidden = ['username','status','password','End_time'];

    protected $replace = ['Headpath'=>['null'=>'/WechatServer/Source/Picture/header001.jpg']];

    protected $table = 'chat_user_info';

    public function __construct()
    {
        $this->model = new Model($this->table,$this->hidden,$this->replace);
    }

    /**
     * 登陆验证
     * @param $name 姓名
     * @param $pwd 密码
     * @return \Applications\lib\结果集
     */
    public function loginU($name,$pwd)
    {
        return $this->model->selectOne(['username'=>$name,'password'=>$pwd,'status'=>'0']);
    }

    /**
     * 模糊用户查找
     * @param $name 用户名
     * @return \Applications\lib\结果集
     */
    public function searchU($name)
    {
        $arr = $this->model->selectOne(['username'=>['%'.$name.'%','like']]);
        return ($arr)?0:$arr;
    }

    /**
     * 查找所有用户
     * @return \Applications\lib\结果集
     */
    public function AllU()
    {
        return $this->model->selectOne(['status'=>'0']);
    }

    /**
     * 更新用户信息
     * @param $where 用户id
     * @param $data 更新数据
     * @return int 更新条目
     */
    public function updateU($where,$data)
    {
        return $this->model->update(['userid'=>$where],$data);
    }

    /**
     * 删除用户
     * @param $where 用户id
     * @return int 更新条目
     */
    public function delUser($where)
    {
        return $this->model->update(['userid'=>$where],['status'=>'1']);
    }

    /**
     * 新增用户
     * @param array $data 更新数据
     * @return int|mixed 更新条目或已存在（0）
     */
    public function newUser(array $data)
    {
        if($this->model->selectOne(['username'=>[$data['username'],'=']])){
            return 0;
        }
            return $this->model->insert(['username'=>$data['username'],'password'=>$data['password'],'name'=>$data['name'],'Sex'=>'Y']);
    }

    /**
     * 指定用户查找
     * @param $name
     * @return \Applications\lib\结果集|int
     */
    public function trueU($name)
    {
        $arr = $this->model->selectOne(['userid'=>$name]);
        return (!$arr)?0:$arr;
    }

    /**
     * 下线时间
     * @param $where 用户id
     */
    public function exitTime($where)
    {
        $this->model->update(['userid'=>$where],['End_time'=>date('Y-m-d h:i:s',time())]);
    }
}