<?php
/**
 * Created by PhpStorm.
 * User: zhangan
 * Date: 2018/7/30
 * Time: 13:45
 */

namespace app\admin\controller;

use app\admin\model\cost as model;
use think\Db;

class Cost extends Permissions
{
    public function index()
    {
        $model = new model();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['bname'] = ['like', '%' . $post['bname'] . '%'];
        }

        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['ctime'] = [['>=',$min_time],['<=',$max_time]];
        }

        $cost = empty($where) ? $model->order('ctime desc')->paginate(20) : $model->where($where)->order('ctime desc')->paginate(20,false,['query'=>$this->request->param()]);
        $this->assign('cost',$cost);
        return $this->fetch();
    }

    public function publish()
    {
        $model = new model();
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        if ($id >0) {
            if($this->request->isPost()) {
                $post = $this->request->post();
                $name = $model->where('id',$id)->find()['bname'];
                $bid = Db::name('book')->where('title',$name)->find()['id'];

                //查找多少章
                $chapter = Db::name('book_content')->where('book_id',$bid)->count();

                //总字节数
                $count_byte = Db::name('book_content')->where('book_id',$bid)->sum('by_count');

                //整书价格(元)
                $bmoney = ((intval($count_byte)/1000) * $post['byte_money'])/10;

                //每章价格
                $cha_money = intval($bmoney)/intval($chapter);
                if (false == Db::name('cost')->where('id',$id)->update(['b_money'=>$bmoney,'cha_money'=>$cha_money,'byte_money'=>$post['byte_money']])) {
                    return $this->error('提交失败');
                } else {
                    return $this->error('提交成功');
                }
            } else {
                $cost = $model->where('id',$id)->find();
                $this->assign('cost',$cost);
                return $this->fetch();
            }
        } else {
            return $this->error('id不正确');
        }
    }
}