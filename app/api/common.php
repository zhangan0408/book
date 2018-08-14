<?php
/**
 * Created by PhpStorm.
 * User: zhangan
 * Date: 2018/8/13
 * Time: 15:27
 */

//api公共函数

function readLog($uid,$bid,$cid)
{
    if (!empty($uid) && !empty($bid) && !empty($cid)) {
        $data= [
            'user_id'=>$uid,
            'book_id'=>$bid,
            'chapter_id'=>$cid,
            'create_time'=>time(),
        ];
        \think\Db::name('readlogs')->insert($data);
    }
}