<?php
/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-24
 * Time: 10:01
 */

namespace Applications\model;
use Applications\lib\Model;

class messageHandle
{
    protected $model;

    protected $hidden = ['status','to_id','arrangement'];

    protected $replace = [];

    protected $table = 'message';

    public function __construct()
    {
        $this->model = new Model($this->table,$this->hidden,$this->replace);
    }

    /**
     *发送数据记录
     * @param $arr
     * @return mixed
     */
    public function sendM($arr)
    {
        $data = [
            'mess_content'=>json_encode($arr),
            'to_id'=>$arr['to'],
            'arrangement'=>$arr['arrangement'],
        ];
        $this->model->insert($data);
        return $this->model->getLastId();
    }

    /**
     * 成功发送数据
     * @param $id 用户id
     * @return int
     */
    public function yesM($id)
    {
        return $this->model->update(['mess_id'=>$id],['status'=>'1']);
    }

    public function getM($id)
    {
        return $this->model->selectOne(['to_id'=>$id,'arrangement'=>'N']);
    }
}