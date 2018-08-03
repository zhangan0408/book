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
class Forward extends Model
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

    //查找提现记录
    public function forwardList($where){
        if($where['user_id'] == '-1'){
            unset($where['user_id']);     
        }
        $forward = Db::name('forward')->where($where)->order('create_time desc')->paginate(10);

        $data = $forward->toArray()['data'];
        foreach ($data as $key => $value) {
            if($value['user_type'] == 1){ //代表作者
                $data[$key]['nickname'] = Db::name('admin')->where('id',$value['user_id'])->value('nickname');
                $data[$key]['user_type'] = '作者';
            }else{
                $data[$key]['nickname'] = '无名氏';
                $data[$key]['user_type'] = '读者';
            }
        }

        return array('data' => $data, 'forward' => $forward);
    }

    
}
