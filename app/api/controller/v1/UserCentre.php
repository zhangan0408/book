<?php
/**
 * Created by PhpStorm.
 * User: zhangan
 * Date: 2018/8/13
 * Time: 18:34
 */

namespace app\api\controller\v1;


use app\api\controller\Api;
use think\Db;

class UserCentre extends Api
{
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put|delete';

    public function account($uid)
    {
        if (!empty($uid)) {
            $info = Db::name('user')->where('id',$uid)->find();
            if ($info) {
                $this->returnmsg(200,'success',$info);
            } else {
                $this->sendError(404,'获取错误',404);
            }
        } else {
            $this->sendError(201,'获取信息错误',201);
        }
    }
}