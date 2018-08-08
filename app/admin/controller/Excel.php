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

use \think\Db;
use \think\Cookie;
use \think\Session;
// use app\admin\model\Admin as adminModel;//管理员模型
// use app\admin\model\AdminMenu;
use app\admin\controller\Permissions;
class Excel extends Permissions
{
    /**
     * xls文件上传
     * @return [type] [description]
     */
    public function index()
    {
        //import('PHPExcel.Classes.PHPExcel.IOFactory');
        if(request()->isPost()){
            //$file = $this->request->file('excel');
            //$file = $_FILES['excel'];
            //$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            //if(empty($file)){
                //return $this->error('上传文件为空');
            //}
            //$file_types = explode(".", $file['name']); // ["name"] => string(25) "excel文件名.xls"
            //$file_type = $file_types [count($file_types) - 1];//xls后缀
            /*判别是不是.xls文件，判别是不是excel文件*/
            //if (strtolower($file_type) != "xls" && strtolower($file_type) != "xlsx") {
                //return $this->error('不是Excel文件，重新上传');
            //}
            //$PHPExcel_IOFactory = new \PHPExcel_IOFactory();
            //$objPHPExcel = $PHPExcel_IOFactory->load($file['tmp_name']);//读取上传的文件
            //$arrExcel = $objPHPExcel->getSheet(0)->toArray();//获取其中的数据
            //array_shift($arrExcel);
            //print_r($arrExcel);die;
            $bid = $this->request->post('nbook');
            $file = $_FILES['excel'];
            //dump($file);die;
            if ($file['error']) {
                $this->error('上传文件未');
            }
            $info = file_get_contents($file['tmp_name'],0);
            $data = ['book_id'=>$bid,'content'=>$info];
            $re = Db::name('book_content')->insert($data);
            if ($re) {
                $this->success('加入成功');
            } else {
                $this->error('加入失败');
            }
        }
        $book = Db::name('book')->field('id,title')->select();
        $this->assign('book',$book);
        return $this->fetch();
    }
}
