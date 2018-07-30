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


namespace app\admin\controller;

use \think\Controller;
use think\Db;
use app\admin\controller\Permissions;
use app\admin\model\Messages;
class Tomessages extends Permissions
{
    public function index()
    {
        $model = new Messages();

        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['message'] = ['like', '%' . $post['keywords'] . '%'];
        }
        
        if (isset($post['is_look']) and ($post['is_look'] == 1 or $post['is_look'] === '0')) {
            $where['is_look'] = $post['is_look'];
        }
 
        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
        }
        
        $message = empty($where) ? $model->order('create_time desc')->paginate(20) : $model->where($where)->order('create_time desc')->paginate(20,false,['query'=>$this->request->param()]);
        $this->assign('message',$message);
        return $this->fetch();
    }


    public function publish()
    {
    	$model = new Messages();
		$id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
		if ($id > 0) {
            //是新增操作
            if ($this->request->isPost()) {
                //是提交操作
                $post = $this->request->post();
                //验证  唯一规则： 表名，字段名，排除主键值，主键名
                $validate = new \think\Validate([
                    ['message', 'require|length:0,200', '回复不能为空'],
                ]);
                //验证部分数据合法性
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                //设置创建人
                //$post['ip'] = $this->request->ip();
                if (false == DB::name('messages')->where('id',$post['id'])->update(['reply'=>$post['message']])) {
                    return $this->error('提交失败');
                } else {
                    //addlog($model->id);//写入日志
                    return $this->success('提交成功', 'admin/tomessages/index');
                }
            } else {
                $message = $model->where('id',$id)->find();

                $this->assign('message',$message);
                //非提交操作
                return $this->fetch();
            }
        }else {
            return $this->error('id不正确');
        }
    }


    public function mark()
    {

        //获取id
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        $model = new Messages();
        //是正常添加操作
        if($id > 0) {
            //是修改操作
            if($this->request->isPost()) {
                //是提交操作
                $post = $this->request->post();
                //验证菜单是否存在
                $message = $model->where('id',$id)->find();
                if(empty($message)) {
                    return $this->error('id不正确');
                }
                if(false == $model->allowField(true)->save($post,['id'=>$id])) {
                    return $this->error('提交失败');
                } else {
                    addlog($model->id);//写入日志
                    return $this->success('提交成功','admin/tomessages/index');
                }
            }
        }
    }


    public function delete()
    {
    	if($this->request->isAjax()) {
    		$id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if(false == Db::name('messages')->where('id',$id)->delete()) {
                return $this->error('删除失败');
            } else {
                addlog($id);//写入日志
                return $this->success('删除成功','admin/tomessages/index');
            }
    	}
    }
}
