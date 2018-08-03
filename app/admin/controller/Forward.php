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
use app\admin\model\Forward as ForwardModel;
use app\admin\model\ForwardCate as cateModel;
class Forward extends Permissions
{
    public function index()
    {
        $adminCateId = Session::get("admin_cate_id");
        $userId = Session::get("admin");
        //如果该用户的用户组是笔者
        $model = new ForwardModel();
        $post = $this->request->param();
        // if (isset($post['keywords']) and !empty($post['keywords'])) {
        //     $where['title'] = ['like', '%' . $post['keywords'] . '%'];
        // }
        
        if (isset($post['type']) and $post['type'] > 0) {
            $where['type'] = ['like', '%,' . $post['type'] . ',%'];
        }

        if (isset($post['admin_id']) and $post['admin_id'] > 0) {
            $where['admin_id'] = $post['admin_id'];
        }
        
        if (isset($post['status']) and ($post['status'] == 1 or $post['status'] == 2 or $post['status'] == 3)) {
            $where['status'] = $post['status'];
        }

        if (isset($post['is_top']) and ($post['is_top'] == 1 or $post['is_top'] === '0')) {
            $where['is_top'] = $post['is_top'];
        }
 
        if(isset($post['create_time']) and !empty($post['create_time'])) {
            $min_time = strtotime($post['create_time']);
            $max_time = $min_time + 24 * 60 * 60;
            $where['create_time'] = [['>=',$min_time],['<=',$max_time]];
        }

        if(isset($post['forward_no']) and !empty($post['forward_no'])) {
            $where['forward_no'] = $post['forward_no'];
        }

        if($adminCateId == 20){
            $where['user_id'] = $userId;
            $where['user_type'] = 1;
        }else{
            $where['user_id'] = '-1';
        }

        $data = $model->forwardList($where);
        $this->assign('data', $data['data']);
        $this->assign('forward',$data['forward']);
        return $this->fetch();
    }

    public function publish()
    {
        $adminCateId = Session::get("admin_cate_id");
        $userId = Session::get("admin");
        $where['user_id'] = $userId;

        if($adminCateId == 20){
            $where['user_type'] = 1;
        }else{
            $where['user_type'] = 3;
        }

        $model = new ForwardModel();
        $cateModel = new cateModel();
        //是正常添加操作
        
        //是新增操作
        if($this->request->isPost()) {
            //是提交操作
            $post = $this->request->post();
            //验证  唯一规则： 表名，字段名，排除主键值，主键名
            $validate = new \think\Validate([
                ['price', 'require', '金额不能为空'],
                ['type', 'require', '请选择提现方式'],
                ['no', 'require', '提现账号不能为空'],
            ]);

            //验证部分数据合法性
            if (!$validate->check($post)) {
                $this->error('申请失败：' . $validate->getError());
            }
            if($post['price'] <= 0){
                $this->error('申请失败：提现金额不能为0或小于0');
            }

            $cate = Db::name('wallet')->where($where)->find();
            //查看余额
            if($post['price'] > $cate['price']){
                $this->error('申请失败：提现金额不能大于余额');
            }
            switch ($post['type']) {
                case '1':
                    $post['alipay_no'] = $post['no'];
                    break;
                
                case '2':
                    $post['weixin_no'] = $post['no'];
                    break;

                case '3':
                    $post['bank_no'] = $post['no'];
                    # code...
                    break;
            }

            unset($post['no']);
            $post['create_time'] = time();
            $post['user_id'] = Session::get("admin");
            $post['forward_no'] = date('Y',time()).time().$post['user_id'];
            $post['user_type'] = $where['user_type'];

            if(false == Db::name('forward')->insert($post)) {
                return $this->error('申请失败');
            } else {
                // addlog($model->id);//写入日志
                Db::name('wallet')->where($where)->setDec('price', $post['price']);
                return $this->success('申请成功','admin/forward/index');
            }
        } else {
            //非提交操作
            $cate = Db::name('wallet')->where($where)->find();
            $this->assign('cates',$cate);
            return $this->fetch();
        }
        // }
    }

    //提现审核
    public function forward()
    {
        $adminCateId = Session::get("admin_cate_id");
        $userId = Session::get("admin");
        $where['user_id'] = $userId;
        if($adminCateId !== 1){
           return $this->error('权限不足'); 
        }

        //获取菜单id
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;

        $model = new ForwardModel();
        $cateModel = new cateModel();
        //是修改操作
        if($this->request->isPost()) {
            //是提交操作
            $post = $this->request->post();
            //验证  唯一规则： 表名，字段名，排除主键值，主键名
            $validate = new \think\Validate([
                ['status', 'require', '请选择通过或拒绝'],
            ]);
            //验证部分数据合法性
            if (!$validate->check($post)) {
                $this->error('提交失败：' . $validate->getError());
            }
            $post['update_time'] = time();
            unset($post['id']);
            //验证菜单是否存在
            $article = Db::name('forward')->where('id',$id)->find();
            if(empty($article)) {
                return $this->error('id不正确');
            }
            //设置修改人
            if(false == Db::name('forward')->where('id',$id)->update($post)) {
                return $this->error('审核失败');
            } else {
                // addlog($model->id);//写入日志
                //如果拒绝 把审核的金额加入钱包 通过则扣除 
                if($post['status'] == 3){
                    // $where['id'] = $id;
                    Db::name('wallet')->where('user_id',$article['user_id'])->setInc('price', $article['price']);
                }
                return $this->success('审核成功','admin/forward/index');
            }
        } else {
            //非提交操作
            $book = Db::name('forward')->where('id',$id)->find();
            if(!empty($book)) {
                $this->assign('book',$book);
                return $this->fetch();
            } else {
                return $this->error('id不正确');
            }
        }
    }

    public function delete()
    {
        if($this->request->isAjax()) {
            $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
            if(false == Db::name('forward')->where('id',$id)->delete()) {
                return $this->error('删除失败');
            } else {
                // addlog($id);//写入日志
                return $this->success('删除成功','admin/forward/index');
            }
        }
    }

}
