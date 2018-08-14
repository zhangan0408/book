<?php
/**
 * Created by PhpStorm.
 * User: zhangan
 * Date: 2018/8/9
 * Time: 11:13
 */

namespace app\api\controller\v1;
use app\api\controller\Api;
use think\Db;

class Index extends Api
{
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put|delete';
    //首页展示
    public function save()
    {
        if ($this->request->isPost()) {

            $post = $this->request->post();
            //print_r($post);die;
            //查找今日免费书籍数据
            $freebook = Db::name('book')->where('td_free',1)->order('id desc')->limit(4)->select();
            //书架书籍id
            $shelf = Db::name('user')->field('shelf')->where('id',$post['id'])->find()['shelf'];
            $ids = explode(',',$shelf);
            foreach($ids as $k=>$v) {
                 $id[$k] = Db::name('book')->where('id',$v)->find();
            }
            $data = [
                'freebokk'=>$freebook,
                'shelf' => $id,
                ];
            $this->returnmsg(200,'success',$data);
        }
    }

    //小说阅读
    public function bookInfo($id,$uid)
    {
            $page = $this->request->param('page');
            $info = Db::name('book_content')->where('book_id',$id)->order('id')->paginate(1);
            if($info->toArray()['total'] < $page){
                $this->returnmsg('203','最后一章了');
            } else {
                //章节id
                $cid = $info->toArray()['data'][0]['id'];
                //加入阅读日志
                readLog($uid,$id,$cid);
                $this->returnmsg(200,'success',$info);
            }

    }
}