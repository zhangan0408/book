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
class Author extends Model
{
	public function cate()
    {
        //关联分类表
        return $this->belongsTo('AuthorCate');
    }

    public function admin()
    {
        //关联角色表
        return $this->belongsTo('Admin');
    }


    //查找所有书籍
    public function selectBook($where){
        $join = [
            ['tplay_admin b','a.author_id=b.id'],
            ['tplay_attachment c','a.avatar=c.id'],
        ];
        $field = 'a.id,a.title,a.intr,a.price,a.type,a.status,a.is_free,a.is_chapter,a.create_time,b.nickname,c.filepath';
        if($where['a.author_id'] == '-1'){
            unset($where['a.author_id']);     
        }
        return Db::name('book')->alias('a')->field($field)->where($where)->join($join)->order('a.create_time desc')->paginate(10);
    }


    //阅读数据量统计
    public function readcount($where){
        if($where['a.author_id'] == '-1'){
            unset($where['a.author_id']);
        }

        $join = [
            ['tplay_admin b','a.author_id=b.id'],
        ];
        $field = 'a.id,a.title,b.nickname';
        $book = Db::name('book')->alias('a')->field($field)->where($where)->join($join)->paginate(10);
        $data = $book->toArray()['data'];
        
        foreach ($data as $key => $value) {
            $data[$key]['readCount'] = Db::name('readlogs')->where('book_id',$value['id'])->count();
        }

        return array('data' => $data, 'book' => $book);
    }
}
