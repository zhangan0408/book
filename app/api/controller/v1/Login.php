<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/26 13:26
// +----------------------------------------------------------------------
// | TITLE: 用户接口
// +----------------------------------------------------------------------

namespace app\api\controller\v1;

use think\Db;
use think\Request;
use think\Session;

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
    /**
     * 用户注册
     * @param Request $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function register(Request $request)
    {
        $data = $request->post();
        $phone = $data['phone'];
        $code = $data['yzm'];
        if ($code == Session::get('yzm')) {
            $userInfo = Db::name('user')->where('phone',$phone)->find();
            if ($userInfo->isEmpty()) {

                $id = Db::name('user')->insert(['phone'=>$phone]);
                if ($id) {
                    $msg = '注册成功!';
                    $this->sendSuccess($msg);
                } else {
                    $msg = '注册失败';
                    $this->sendError($msg);
                }
            } else {
                $msg = '手机号已存在';
                $this->sendError($msg);
            }
        } else {
            $msg = '验证码错误';
            $this->sendError($msg);
        }
    }

    public function login(Request $request) {
        $data = $request->param();
        $type = $data['type'];

        switch ($type) {
            case 'phone':
                $phone = $data['phone'];
                $yzm = $data['yzm'];
                if ($yzm ==Session::get('loginYzm')) {
                    $re = Db::name('user')->where('phone',$phone)->find();
                    if ($re->isEmpty()) {
                        $msg = '手机号未注册';
                        $this->sendError($msg);
                    } else {
                        $msg = '登录成功';
                        $this->sendSuccess($msg);
                    }
                } else {
                    $msg = '验证码错误';
                    $this->sendError($msg);
                }
                break;
            case 'pass':
                $phone = $data['phone'];
                $pass = $data['pwd'];
                $re = DB::name('user')->where('phone',$phone)->find();
                if ($re->isEmptu()) {
                    $msg = '手机号未注册';
                    $this->sendError($msg);
                } else {
                    $re1 = Db::name('user')->where(['phone'=>$phone,'pass'=>$pass])->find();
                    if ($re1) {
                        $msg = '登录成功!';
                        $this->sendSuccess($msg);
                    } else {
                        $msg = '密码错误!';
                        $this->sendSuccess($msg);
                    }
                }

        }

    }
}