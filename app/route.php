<?php
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://tplay.pengyichen.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------

use think\Route;

//url美化 例：Route::rule('blog/:id','index/blog/read');
$url = \think\Db::name("urlconfig")->where(['status' => 1])->column('aliases,url');
foreach ($url as $k => $val) {
	Route::rule($k,$val);
}
Route::resource(':version/user','api/:version.User');   //注册一个资源路由，对应restful各个方法,.为目录
Route::rule(':version/user/:id/fans','api/:version.User/fans'); //restful方法中除restful api外的其他方法路由
//Route::rule(':version/token/wechat','api/:version.Token/wechat');
Route::rule(':version/token/mobile','api/:version.Token/mobile');
//首页展示
Route::resource(':version/index','api/:version.Index');
//小说详细信息
Route::rule(':version/index/:id/:uid/bookInfo','api/:version.Index/bookInfo');
//我的账户
Route::rule(':version/userCentre/:uid/account','api/:version.UserCentre/account');


return [
    // api版本路由
    //'api/:version/:controller'=>'api/:version.:controller/index',// 省略方法名时
    //'api/:version/:controller/:function'=>'api/:version.:controller/:function',// 有方法名时
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];


