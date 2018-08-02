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



//url美化 例：Route::rule('blog/:id','index/blog/read');
$url = \think\Db::name("urlconfig")->where(['status' => 1])->column('aliases,url');
foreach ($url as $k => $val) {
	\think\Route::rule($k,$val);
}


return [
    // api版本路由
    'api/:version/:controller'=>'api/:version.:controller/index',// 省略方法名时
    'api/:version/:controller/:function'=>'api/:version.:controller/:function',// 有方法名时

    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];


