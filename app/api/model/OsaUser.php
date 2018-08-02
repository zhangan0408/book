<?php

/**
 * Created by PhpStorm.
 * User: zhangan
 * Date: 2018/7/19
 * Time: 17:19
 */
namespace app\api\controller\v1;
use think\Db;
use think\Model;
class OsaUser extends Model
{
    public function getUser1()
    {
        $re = $this->select();
        return $re;
    }
}