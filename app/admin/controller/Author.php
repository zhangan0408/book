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

use \think\Cache;
use \think\Controller;
use think\Loader;
use think\Db;
use \think\Cookie;
use \think\Session;
use app\admin\controller\Permissions;
use app\admin\model\Author as AuthorModel;
use app\admin\model\AuthorCate as cateModel;
class Author extends Permissions
{
    public function index()
    {
        $adminCateId = Session::get("admin_cate_id");
        $userId = Session::get("admin");
        //如果该用户的用户组是笔者
        $model = new AuthorModel();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['a.title'] = ['like', '%' . $post['keywords'] . '%'];
        }
        
        if (isset($post['type']) and $post['type'] > 0) {
            $where['a.type'] = ['like', '%' . $post['type'] . '%'];
        }

        if (isset($post['admin_id']) and $post['admin_id'] > 0) {
            $where['admin_id'] = $post['admin_id'];
        }
        
        if (isset($post['status']) and ($post['status'] == 1 or $post['status'] == 2)) {
            $where['a.status'] = $post['status'];
        }

        if (isset($post['is_top']) and ($post['is_top'] == 1 or $post['is_top'] === '0')) {
            $where['is_top'] = $post['is_top'];
        }
 
        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['a.create_time'] = [['>=',$min_time],['<=',$max_time]];
        }
        if($adminCateId == 20){
            $where['a.author_id'] = $userId;
        }else{
            $where['a.author_id'] = '-1';
        }
        $book = $model->selectBook($where);
        $data = $book->toArray()['data'];

        for ($i=0; $i < count($data); $i++) { 
            $type = Db::name('book_cate')->where('id','in',$data[$i]['type'])->column('title');
            $data[$i]['type'] = implode(',', $type);
            $data[$i]['create_time'] = date('Y-m-d H:i:s',$data[$i]['create_time']);
        }
        $this->assign('data', $data);
        $this->assign('book',$book);
        $info['cate'] = Db::name('book_cate')->select();
        $info['admin'] = Db::name('admin')->select();
        $this->assign('info',$info);
        return $this->fetch();
    }

    public function publish()
    {
        //获取菜单id
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        $model = new AuthorModel();
        $cateModel = new cateModel();
        //是正常添加操作
        if($id > 0) {
            //是修改操作
            if($this->request->isPost()) {
                //是提交操作
                $post = $this->request->post();
                //验证  唯一规则： 表名，字段名，排除主键值，主键名
                $validate = new \think\Validate([
                    ['title', 'require', '标题不能为空'],
                    ['type', 'require', '请选择分类'],
                    ['thumb', 'require', '请上传缩略图'],
                    ['intr', 'require', '小说简介不能为空'],
                ]);
                //验证部分数据合法性
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }

                $post['update_time'] = time();
                $post['avatar'] = $post['thumb'];
                unset($post['thumb']);
                //验证菜单是否存在
                $article = Db::name('book')->where('id',$id)->find();
                if(empty($article)) {
                    return $this->error('id不正确');
                }
                //设置修改人
                $post['edit_admin_id'] = Session::get('admin');
                if(false == $model->allowField(true)->save($post,['id'=>$id])) {
                    return $this->error('修改失败');
                } else {
                    addlog($model->id);//写入日志
                    return $this->success('修改成功','author/article/index');
                }
            } else {
                //非提交操作
                $book = Db::name('book')->where('id',$id)->find();
                // echo getSql();die;
                $cate = Db::name('book_cate')->select();
                $this->assign('cates',$cate);
                if(!empty($book)) {
                    $this->assign('book',$book);
                    return $this->fetch();
                } else {
                    return $this->error('id不正确');
                }
            }
        } else {
            //是新增操作
            if($this->request->isPost()) {
                //是提交操作
                $post = $this->request->post();
                //验证  唯一规则： 表名，字段名，排除主键值，主键名
                $validate = new \think\Validate([
                    ['title', 'require', '标题不能为空'],
                    ['type', 'require', '请选择分类'],
                    ['thumb', 'require', '请上传缩略图'],
                    ['intr', 'require', '小说简介不能为空'],
                ]);
                //验证部分数据合法性
                if (!$validate->check($post)) {
                    $this->error('提交失败：' . $validate->getError());
                }
                $post['type'] = implode(',',$post['type']);
                $post['create_time'] = time();
                $post['update_time'] = time();
                $post['status'] = 2;
                $post['author_id'] = Session::get("admin");
                $post['avatar'] = $post['thumb'];
                unset($post['thumb']);
                //设置创建人
                // $post['admin_id'] = Session::get('admin');
                //设置修改人
                // $post['edit_admin_id'] = $post['admin_id'];
                // ->allowField(true)
                if(false == Db::name('book')->insert($post)) {
                    return $this->error('添加失败');
                } else {
                    // addlog($model->id);//写入日志
                    return $this->success('添加成功','admin/author/index');
                }
            } else {
                //非提交操作
                $cate = Db::name('book_cate')->field('id,title')->select();
                // dump($cate);die;
                // $cates = $cateModel->catelist($cate);
                $this->assign('cates',$cate);
                return $this->fetch();
            }
        }
    }


    public function delete()
    {
        if($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if(false == Db::name('book')->where('id',$id)->delete()) {
                return $this->error('删除失败');
            } else {
                // addlog($id);//写入日志
                return $this->success('删除成功','admin/author/index');
            }
        }
    }


    public function is_top()
    {
        if($this->request->isPost()){
            $post = $this->request->post();
            if(false == Db::name('article')->where('id',$post['id'])->update(['is_top'=>$post['is_top']])) {
                return $this->error('设置失败');
            } else {
                addlog($post['id']);//写入日志
                return $this->success('设置成功','admin/article/index');
            }
        }
    }


    public function status()
    {
        if($this->request->isPost()){
            $post = $this->request->post();
            if(false == Db::name('article')->where('id',$post['id'])->update(['status'=>$post['status']])) {
                return $this->error('设置失败');
            } else {
                addlog($post['id']);//写入日志
                return $this->success('设置成功','admin/article/index');
            }
        }
    }

    //阅读量统计
    public function readcount(){
        $adminCateId = Session::get("admin_cate_id");
        $userId = Session::get("admin");
        //如果该用户的用户组是笔者
        $model = new AuthorModel();
        $post = $this->request->param();
        if (isset($post['keywords']) and !empty($post['keywords'])) {
            $where['a.title'] = ['like', '%' . $post['keywords'] . '%'];
        }

        if (isset($post['status']) and ($post['status'] == 1 or $post['status'] == 2)) {
            $where['a.status'] = $post['status'];
        }

        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['a.create_time'] = [['>=',$min_time],['<=',$max_time]];
        }

        if($adminCateId == 20){
            $where['a.author_id'] = $userId;
        }else{
            $where['a.author_id'] = '-1';
        }

        $data = $model->readcount($where);

        $this->assign('data',$data['data']);
        $this->assign('book',$data['book']);
        return $this->fetch();
        // dump($bookCount);die;
    }
}
