<?php
/**
 * Created by PhpStorm.
 * User: zhangan
 * Date: 2018/8/9
 * Time: 11:13
 */

namespace app\api\controller\v1;
use app\api\controller\Api;
use think\Db;

class Index extends Api
{
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put|delete';

    public function save()
    {
        if ($this->request->isPost()) {
            
            //查找今日免费书籍数据
            $freebook = Db::name('book')->where('td_free',1)->order('id desc')->limit(4)->select();
            //书架书籍
            $shelf = Db::name('user')->where()->select();
        }

    }
}