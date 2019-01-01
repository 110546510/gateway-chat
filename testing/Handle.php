<?php
namespace Applications\testing;
use Applications\model\ChatList;
use Applications\model\GroupHandle;
use Applications\model\SocketHandle;
use Applications\model\UserHandle;

/**
 * Created by PhpStorm.
 * User: 龙
 * Date: 2018-12-12
 * Time: 16:13
 */
require_once __DIR__.'/../config.php';

//require_once __DIR__.'/../../vendor/GatewayClient/Gateway.php';

class Handle
{
    public $users = '';
    public  $chatlist = '';

    public function __construct()
    {
        $this->users = new UserHandle();
        $this->chatlist = new ChatList();
    }

    public function selelist(){
        $this->chatlist->setT(19);
        $arr = $this->chatlist->getGroupId();
        return $arr;
    }

    public function seleUnit()
    {

        return $this->users->AllUser();

    }

    public function seleOneUnit(){
        return $this->users->loginUser(110546510,123456);
    }

    public function insertUnit()
    {
        $arr = ['username'=>'110546510','password'=>'123456','name'=>'saf22lj','Sex'=>'Y'];
        return $this->users->insertUser($arr);
    }

    public function updateUnit()
    {
        $where = 8723833992;
        $data = (['name'=>'33dfsd484','Sex'=>'Y']);
        echo $this->users->updateUser($where,$data);
    }
    public function lookClient(){
        return $sock = SocketHandle::getOnlineUid();
    }
}

$test = new Handle();
print_r($test->lookClient());
//print_r($test->seleUnit());
//print_r($test->seleOneUnit());
//print_r($test->selelist());
//echo "<br/>";
//echo $test->insertUnit();
//echo $test->updateUnit();