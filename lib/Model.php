<?php
/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-13
 * Time: 11:13
 */

namespace Applications\lib;

require_once __DIR__.'/../config.php';

/**
 * 数据库模型
 * Class Model
 * @package Applications\lib
 */
class Model
{
    /**
     * @var MMysql 数据库连接
     */
    private $conn;

    /**
     * @var 结果集
     */
    private $result;

    private $sourcere;

    /**
     * @var array 隐藏属性
     */
    protected $hidden = [];

    /**
     * @var array 替换字段
     */
    protected $replace = [];

    public function __construct($table,$hidden,$replace)
    {
        $this->conn = new MMysql();
        $this->conn->setTable($table);
        $this->hidden = $hidden;
        $this->replace = $replace;
    }

    /**
     * 隐藏属性
     * @return $this 当前类
     */
    public function hiddenData()
    {
        if(!$this->hidden){
            return $this;
        }
        if(!empty($this->hidden)){
            foreach ($this->hidden as $value){
                foreach ($this->result as $key => $valus){
                    unset($this->result[$key][$value]);
                }
            }
        }
        return $this;
    }

    /**
     * 替换属性
     * @return $this 当前类
     */
    public function replaceData()
    {
        if(!$this->replace){
            return $this;
        }
        foreach ($this->replace as $key =>$value){
            foreach ($this->result as $keys => $values){
                    $this->result[$keys][$key] = $value[$this->result[$keys][$key]];
            }
        }
        return $this;
    }

    /**
     * 查询所有
     * @return 结果集
     */
    public function selectAll()
    {
        $this->sourcere = $this->result = $this->conn->select();
        return $this->getResult();
    }

    /**
     * 获取未处理过的原有数据
     * @return mixed
     */
    public function getSoruceResult()
    {
        return $this->sourcere;
    }

    /**
     * 隐藏处理返回结果
     * @return 结果集
     */
    public function getResult()
    {
        return $this->hiddenData()->replaceData()->result;
    }

    /**
     * 删除表数据
     * @param $where 制定
     * @return int
     */
    public function deleteTable($where)
    {
        return $this->conn->where($where)->delete();
    }

    /**
     * 更新数据
     * @param $where 指定
     * @param $data 更新数据
     * @return int 更新条目
     */
    public function update($where,$data){
        return $this->conn->where($where)->update($data);
    }

    /**
     * 查询指定数据
     * @param $where 指定项
     * @return 结果集
     */
    public function selectOne($where)
    {
        $this->result = $this->conn->where($where)->select();
        return $this->getResult();
    }

    /**
     * 返回底层数据处理类
     * @return MMysql
     */
    public function sourcePDO()
    {
        return $this->conn;
    }

    /**
     * 插入数据
     * @param $data 数据
     * @return int
     */
    public function insert($data)
    {
        return $this->conn->insert($data);
    }

    public function getLastId()
    {
        return $this->conn->getLastIndex();
    }
}