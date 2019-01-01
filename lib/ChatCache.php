<?php
/**
 * Created by PhpStorm.
 * UserInfo: 龙
 * Date: 2018-12-08
 * Time: 21:00
 */

namespace Applications\lib;

date_default_timezone_set("PRC");
/**
 * Class ChatCache
 * 聊天记录缓存
 * 1.记录保存格式json：{'message':{'send':'cd','information','','type':'text',filepath:'',filename:'','to':'jack'},'time':'1342540593204'}
 * 2.自动生成半小时聊天缓存（注：为确保是半小时记录，系统自动生成文件带时间（格式：hi例如：6：30 = 630），根据聊天时间建立当前半小时区间记录：例如：6：28 = 6：00 = 600）
 */
class ChatCache
{
    /**
     * @var 文件保存路径
     */
    private  $filepath;

    /**
     * @var 文件资源
     */
    private $links;

    /**
     * 初始化
     * ChatCache constructor.
     * @param $name 保存路径，路径自定义
     */
    public function __construct($name)
    {
        $this->filepath = $name;
    }

    /**
     * 保存缓存信息
     * @param $message 写入保存的数据
     * @return bool
     */
    public function saveChat($message){
        if(file_exists($this->filepath.'/'.date('Ymd')) < 1){
            mkdir($this->filepath.'/'.date('Ymd'));
        }
        $this->filepath = $this->filepath.'/'.date('Ymd').'/'.$this->fileName().'.chatson';
        $this->links = fopen($this->filepath,'a+');
        $result = fwrite($this->links,$this->formatArray($message));
        fwrite($this->links,"\r\n");
        $this->closeLink();
        return ($result == false)?false:true;
    }

    public function saveCache($message){
        if(file_exists($this->filepath.'/'.date('Ymd')) < 1){
            mkdir($this->filepath.'/'.date('Ymd'));
        }
        $this->filepath = $this->filepath.'/'.date('Ymd').'/'.$this->fileName().'.chatson';
        $this->links = fopen($this->filepath,'a+');
        $result = fwrite($this->links,$this->formatArray($message));
        fwrite($this->links,"\r\n");
        $this->closeLink();
        return ($result == false)?false:true;
    }

    /**
     * 获取指定文件缓存信息
     * @param $names 文件名称
     * @return bool|string
     */
    public function getChat($names){
        $this->links = fopen($this->filepath = $this->filepath.date('Ymd').'/'.$names,'a+');
        $result = fread($this->links,filesize($this->filepath.date('Ymd').'/'.$names));
        $this->closeLink();
        return ($result == false)?false:json_encode($result);
    }

    public function getChatAll()
    {
        
    }
    
    /**
     * @return 文件保存路径
     */
    public function getFilePath()
    {
        return $this->filepath;
    }

    /**
     * 文件命名方式
     * @return string 文件名
     */
    private function fileName(){
        $times = date('i');
        $is = ($times < 30)?'00':'30';
        return date('H').'-'.$is;
    }

    /**
     * 数据格式化处理：json：{..原来数据.,'time':'1342540593204'}
     * @param $message 数据
     * @return string 格式化结果
     */
    private function formatArray($message){
        if(is_array($message)){
            $message['time'] = $this->getMillisecond();
            return json_encode($message);
        }else{
            $arr = json_decode($message,true);
            $arr['time'] = $this->getMillisecond();
            return json_encode($arr);
        }
    }

    /**
     * 获取时间毫秒值
     * @return bool|string 毫秒值
     */
    private function getMillisecond(){
        list($msec, $sec) = explode(' ', microtime());
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectimes = substr($msectime,0,13);
    }

    /**
     * 关闭文件资源
     */
    public function closeLink(){
        fclose($this->links);
    }
}