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


namespace app\admin\model;

use \think\Model;
use think\Db;
class Catelog extends Model
{
	// public function cate()
 //    {
 //        //关联分类表
 //        return $this->belongsTo('AuthorCate');
 //    }

 //    public function admin()
 //    {
 //        //关联角色表
 //        return $this->belongsTo('Admin');
 //    }


    //查找所有书籍
    public function selectLog($where){
        // $join = [
        //     ['tplay_admin b','a.author_id=b.id'],
        //     ['tplay_attachment c','a.avatar=c.id'],
        // ];
        // $field = 'a.id,a.title,a.intr,a.price,a.type,a.status,a.is_free,a.is_chapter,a.create_time,b.nickname,c.filepath';
        // if($where['a.author_id'] == '-1'){
        //     return Db::name('book')->alias('a')->field($field)->join($join)->order('a.create_time desc')->paginate(10);     
        // }else{
            return Db::name('book_content')->where($where)->order('sort asc')->paginate(10);
        // }
    }
}
