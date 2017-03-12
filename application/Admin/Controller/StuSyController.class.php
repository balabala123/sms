<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class StuSyController extends AdminbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = D('Help');
        $this->params = I('params.');
    }

    public function index(){
        $stu_id = $_COOKIE['stu_id']=8;
        $where['disabled'] = 0;
        $where['stu_id'] = $stu_id;

        //搜索
        $type_name = trim(I('request.type_name'));
        if($type_name || ($type_name == 0 && $type_name != '')){
            $where['type_name'] = array('like',"%$type_name%");
        }

        //分页
        $count=$this->model->where($where)->count();
        $page = $this->page($count, 8);
        $this->assign("page", $page->show('Admin'));

        $Help_msg = M('Help_msg');

        $res = $this->model->limit($page->firstRow , $page->listRows)->where($where)->select();
        foreach($res as $k=>$v){
            $data[$k]['id'] = $v['id'];
            $data[$k]['type_name'] = $v['type_name'];

            $sel = $Help_msg->field('money')->where(array('type_name'=>$v['type_name']))->find();
            $data[$k]['money'] = $sel['money'];

            $data[$k]['sy_note'] = $v['sy_note'];
            $data[$k]['create_time'] = date("Y/m/d H:i",$v['create_time']);
            if($v['status'] == 0){
                $data[$k]['status'] = '申请中';
            }
            if($v['status'] == 1){
                $data[$k]['status'] = '审批通过';
            }
            if($v['status'] == 2){
                $data[$k]['status'] = '审批拒绝';
            }
            if($v['status'] == 3){
                $data[$k]['status'] = '已取消';
            }

        }

        $this->assign('data',$data);
        $this -> display();
    }

    public function add(){
        $Help_msg = M("Help_msg");
        $Student = M("Student");
        $where['disabled'] = 0;

        //过滤，介于开始时间与结束时间之中
        $where['start_time'] = array('elt',time());
        $where['end_time'] = array('egt',time());

        //查询用户所在班级所有学生
        $stu = $Student->query('select stu_id from cmf_student where(class_id = (select class_id from cmf_student where(stu_id = 8)));');

        foreach($stu as $v){
            $stu_ids[] = $v['stu_id'];
        }

        $sel = $Help_msg->field('type_name')->where($where)->select();
        foreach ($sel as $k=>$v){
            $str['type_name'] = $v['type_name'];
            $str['stu_id'] = array('in',$stu_ids);
            $str['status'] = 1;
            $str['disabled'] = 0;

            $res1 = $this->model->where($str)->count();

            //查询奖学金类型对应的名额
            $res2 = $Help_msg->field('quota')->where(array('type_name'=>$v['type_name']))->find();

            $type[$k]['sheng'] = $res2['quota'] - $res1;

            $type[$k]['type_name'] = $v['type_name'];
        }
//        print_r($type);die;
        $this->assign("type",$type);
        $this->display();
    }

    public function add_post(){
        $data = I('post.');
        $Student = M("Student");
        $Help_msg = M("Help_msg");


        $stu_id = $_COOKIE['stu_id']=8;
        $data['create_time'] = time();
        $data['stu_id'] = $stu_id;

        //处理type_name
        $data['type_name'] = preg_replace("/\(.*\)/", '', $data['type_name']);

        //查询是否还有名额
        $where['type_name'] = $data['type_name'];
        //查询用户所在班级所有学生
        $stu = $Student->query('select stu_id from cmf_student where(class_id = (select class_id from cmf_student where(stu_id = 8)));');

        foreach($stu as $v){
            $stu_ids[] = $v['stu_id'];
        }

        $str['type_name'] = $data['type_name'];
        $str['stu_id'] = array('in',$stu_ids);
        $str['status'] = 1;
        $str['disabled'] = 0;

        $res1 = $this->model->where($str)->count();

        //查询奖学金类型对应的名额
        $res2 = $Help_msg->field('quota')->where(array('type_name'=>$v['type_name']))->find();
        $p = $res2['quota'] - $res1;
        if($p == 0){
            $this->error('没有名额了!');
        }


        if ($this->model->create($data)!==false) {
            if ($this->model->add()!==false) {
                $this->success('提交成功!', U('StuSy/index'));
            } else {
                $this->error('提交失败!');
            }
        } else {
            $this->error($this->model->getError());
        }


    }

    public function remove_add(){
        //取消申请
        $id = I("get.id",0,'intval');
        //判断状态是否为审批通过
        $status = $this->model->field('status')->where(array('id'=>$id))->find();

        if($status['status'] == 1){
            $this->error('审批已经通过，无法取消!');
        }else{
            //更改状态值为3（已取消）
            if ($this->model->where(array('id'=>$id))->save(array('status'=>3))!==false) {
                $this->success('取消成功');
            } else {
                $this->error('取消失败');
            }
        }

    }

}
