<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class StuThinkController extends AdminbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = M('Think');
        $this->params = I('params.');
    }

    public function index(){
        //判断该学生是否提交过评价
        $stu_id = $_COOKIE['stu_id']=8;
//        print_r($_COOKIE);
        $sel = $this->model->where(array('from_stu_id'=>$stu_id))->find();
        if($sel){
            $this->error('您已经提交过评价!',U('Main/index'));
        }
        $Student = M('Student');
        $class_res = $Student->field('class_id')->where(array('stu_id'=>$stu_id))->find();
        $class = $class_res['class_id'];
        //班级所有学生
        $where['class_id'] = $class;
        $where['stu_id'] = array('neq',$stu_id);
        $stu_all_res = $Student->field('stu_id,stu_name')->where($where)->select();
//        print_r($Student->getLastSql());die;
        foreach($stu_all_res as $k=>$v){
            $stu_all[$k]['stu_name'] = $v['stu_name'];
            $stu_all[$k]['stu_id'] = $v['stu_id'];
        }
//        print_r($stu_all);die;
        for($i=0;$i<ceil(count($stu_all)/3);$i++)
        {
            $stu_three[] = array_slice($stu_all, $i * 3 ,3);
        }

//        print_r($stu_three);die;
        $this->assign("data",$stu_three);
        $this -> display();
    }

    public function add(){
        $post = I('post.');
//        print_r($post);die;

        if(IS_POST){
            foreach($post as $k=>$v){
                $data[$k]['to_stu_id'] = $k;
                $data[$k]['from_stu_id'] = $_COOKIE['stu_id'];
                if($v == 'A'){
                    $data[$k]['score'] = 4;
                }
                if($v == 'B'){
                    $data[$k]['score'] = 3;
                }
                if($v == 'C'){
                    $data[$k]['score'] = 2;
                }
                if($v == 'D'){
                    $data[$k]['score'] = 1;
                }
            }
//        print_r($data);die;
            foreach($data as $v){
                $sel[] = $v;
            }
//        print_r($sel);die;
            if($this->model->addALL($sel) !== false){
                $this->success('提交成功', U('Main/index'));
            }else{
                $this->error('提交失败');
            }
        }

    }


}
