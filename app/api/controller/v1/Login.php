<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/26 13:26
// +----------------------------------------------------------------------
// | TITLE: 用户接口
// +----------------------------------------------------------------------

namespace app\api\controller\v1;

use think\Request;
// use app\api\auth\OauthAuth;
/**
 * Class User
 * @title 用户注册/登录 第三方登录
 * @url  http://dawn-api.com/v1/user
 * @desc  
 * @version 1.0
 * @readme /doc/md/user.md
 */
class Login extends Base
{
    //是否开启授权认证
    public $apiAuth = true;
    // //附加方法
    // protected $extraActionList = ['sendCode'];
    // //跳过鉴权的方法
    // protected $skipAuthActionList = ['sendCode'];
    /**
     * @title 发送CODE
     * @readme /doc/md/method.md
     */
    public function sendCode()
    {
        //send message
        return $this->sendSuccess(['code' => 123]);
    }

    public function QQ_login(Request $request)
    {
        //判断是否传参

        echo 111;die;
    }
}