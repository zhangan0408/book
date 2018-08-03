<?php
/**
 * Created by PhpStorm.
 * User: zhangan
 * Date: 2018/7/18
 * Time: 15:19
 */

namespace app\api\controller\v1;
//use app\api\auth\BasicAuth;

class text extends Base
{
    public function index()
    {
       return db('admin')->select()->toArray();
    }
}