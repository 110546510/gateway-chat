<?php
/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-16
 * Time: 16:52
 */

namespace Applications\logic;
use Applications\model\ChatList;
use Applications\model\GroupHandle;
use Applications\model\messageHandle;
use Applications\model\Respson;
use Applications\model\SocketHandle;
use Applications\model\UserHandle;
use GatewayWorker\Gateway;

/**
 * 业务逻辑处理
 * Class Logic
 * @package Applications\logic
 */
class Logic
{
    private $user;

    private $list;

    private $group;

    private $message;

    public function __construct()
    {
        $this->user = new UserHandle();
        $this->list = new ChatList();
        $this->group = new GroupHandle();
        $this->message = new messageHandle();
    }

    /**
     * 登陆全部操作 //login
     * @param $request 请求数据
     * @param $client_id 设备id
     * @return string
     */
    public function loginU($request,$client_id)
    {
        $result = $this->user->loginU($request['send'],$request['content']);
        if($result){
            $arr = $this->list->getGroupId($result[0]['userid']);
            SocketHandle::authaccess($result[0]['userid']);
            SocketHandle::bind($client_id,$result[0]['userid'],$arr);
            return Respson::resultJson($result,1,'login');
        }else{
            $res = $this->user->newUser(['username'=>$request['send'],'password'=>$request['content'],'name'=>$request['send']]);
            if($res == 0) return Respson::resultJson('用户名已存在',0,'login');
            $use =$this->user->trueU(['username'=>$request['send']]);
                return Respson::resultJson($use,1,'login');
        }
//        return Respson::resultJson('用户名或密码错误',0,'login');
    }

    /**
     * 发送信息 //personal/group
     * @param $result 请求数据
     * @param $mode 单人/群聊
     * @return string
     */
    public function send($result,$mode){
        if(!$this->list->isFirend($result['send'],$result['to'],$result['arrangement'])) {
            return Respson::resultJson('没有好友', 0, 'chat');
        }
        if($mode == 1){
//            ChatMessage::oneToOne($result);
            $id = $this->message->sendM($result);
            $result['id'] = $id;
            SocketHandle::sendUid($result['to'],json_encode($result));
        }else{
            $id = $this->message->sendM($result);
            $result['id'] = $id;
//            ChatMessage::oneTogroup($result);
            SocketHandle::sendGroup($result['to'],json_encode($result));
        }
        return Respson::resultJson('发送成功',1,'chat',$id);
    }

    /**
     * 消息发送成功
     * @param $result
     * @return array|bool
     */
    public function yesM($result)
    {
        $id = explode(',',$result['content']);
        $error = [];
        for($i = 0 ; $i < count($id) ; $i++){
            $error[$i] = $this->message->yesM($result['mess_id']);
        }
        return ($error)?$error:true;
    }

    public function getNoOnLine($result)
    {
        $res = $this->message->getM($result['send']);
        if($res){
            return $res;
        }else{
            return 0;
        }
    }

    /**
     * 正常下线操作 //logout
     * @param $client_id 设备id
     * @return string
     */
    public function exitU($client_id)
    {
        $user_id = Gateway::getUidByClientId($client_id);
        $this->user->exitTime($user_id);
        return Respson::resultJson('下线成功',1,'logout');
    }

    /**
     * 获取用户列表 //getfirendlist
     * @return string
     */
    public function friendList($request)
    {
        $friend = $this->list->allFirend($request['send']);
        if($friend){
            $res = [];
            for ($i = 0 ; $i < count($friend) ; $i++){
                if($friend[$i]['arrangement'] == 'N') {
                    $arr = $this->user->trueU($friend[$i]['chat_id']);
                    $res[$i] = array_merge($arr[0],$friend[$i]);
                }else{
                    $arr = $this->group->getGroupId($friend[$i]['chat_id']);
                    $res[$i] = array_merge($arr[0],$friend[$i]);
                }
            }
        }
        return Respson::resultJson($res,1,'getfriendlist');
    }

    /**
     * 加入群 //join
     * @param $result
     * @return string
     */
    public function joinGroup($result)
    {
        $re = $this->group->joinGroup($result['to'],$result['send']);
        if($re == -1){
            return Respson::resultJson('该群不存在',0,'join');
        }else if($re > 0 ){
            SocketHandle::joinGroup($result['send'],$result['to']);
            return Respson::resultJson('欢迎加入',1,'join');
        }else{
            return Respson::resultJson('数据异常',0,'join');
        }
    }

    /**
     * 新建群聊 //newGroup
     * @param $result
     * @return string
     */
    public function newGroup($result)
    {
        $res = $this->group->newGroup($result['content'],$result['send']);
        if($res){
            $arrw = $this->group->getGroupName(['name'=>$result['content']]);
            if($arrw)
                SocketHandle::joinGroup($result['send'],$arrw['group_id']);
            return Respson::resultJson($arrw,1,'newGroup');
        }else if($res == -1){
            return Respson::resultJson('名称重复',0,'newGroup');
        }
        return Respson::resultJson('新建失败',0,'newGroup');
    }

    /**
     * 删除聊天 //delete
     * @param $request
     * @return string
     */
    public function deleteUser($request)
    {
        $res = $this->list->delFriend($request['send'],$request['to'],$request['arrangement']);
        if($res == -1){
            return Respson::resultJson('用户不存在',0,'delete');
        }else if($res > 0){
            return Respson::resultJson('删除成功',1,'delete');
        }else{
            return Respson::resultJson('无效操作',0,'delete');
        }
    }

    /**
     * 搜索群 //search
     * @param $groupname 群名
     * @return string
     */
    public function searchGroup($groupname)
    {
        $arr = $this->group->searchG($groupname);
        return ($arr)?Respson::resultJson($arr,1,'search'):Respson::resultJson('该群不存在',0,'search');
    }

    /**
     * 搜索用户 //search
     * @param $username 用户名
     * @return string
     */
    public function searchUser($username)
    {
        $arr = $this->user->searchU($username);
        return ($arr)?Respson::resultJson('用户不存在',0,'search'):Respson::resultJson($arr,1,'search');
    }

    /**
     * 添加好友 //join
     * @param $request
     * @return string
     */
    public function requestUser($request)
    {
        if($this->list->applyFriend($request['send'],$request['to'],$request['arrangement']) == true ){
            SocketHandle::sendUid($request['to'],$request);
            return Respson::resultJson('好友申请成功',1,'join');
        }else{
            return Respson::resultJson('好友申请失败',0,'join');
        }
    }

    /**
     * 同意好友申请 //agree
     * @param $request
     * @return string
     */
    public function agreeToApply($request)
    {
        return ($this->list->agreeFriend($request['send'],$request['to'],$request['arrangement']) == 1)?Respson::resultJson('申请通过',1,'agree'):Respson::resultJson('非法操作',0,'agree');
    }

    /**
     * 好友状态设置（黑名单）
     * @param $request
     * @return string
     */
    public function firendStatus($request)
    {
        switch ($this->list->statusFirend($request['send'],$request['to'],$request['content'],$request['arrangement'])){
            case '-2':
                return Respson::resultJson('好友不存在',0,'');
                break;
            case '-1':
                return Respson::resultJson('好友认证未通过',0,'');
                break;
            case '1':
                return Respson::resultJson('修改成功',1,'');
                break;
            default:
                return Respson::resultJson('数据异常',0,'');
                break;
        }
    }
}