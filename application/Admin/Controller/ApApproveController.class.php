<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class ApApproveController extends AdminbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = D('Add_point');
        $this->params = I('params.');
    }

    public function index(){
//        $stu_id =get_current_admin_id();
//        $stu_id = get_current_userid();
//        print_r($stu_id);die;
        $where['cmf_add_point.disabled'] = 0;
        $where['cmf_add_point.status'] = 0;
        //搜索
        $xi_name = trim(I('request.xi_name'));
        $depart_name = trim(I('request.depart_name'));
        $class_id = trim(I('request.class_id'));
        $type_name = trim(I('request.type_name'));
        $stu_name = trim(I('request.stu_name'));
        $stu_no = trim(I('request.stu_no'));
        if($xi_name != ''){
            $where['xi_name'] = array('like',"%$xi_name%");
        }
        if($depart_name != ''){
            $where['depart_name'] = array('like',"%$depart_name%");
        }
        if($class_id != ''){
            $where['class_id'] = array('like',"%$class_id%");
        }
        if($type_name != ''){
            $where['cmf_add_point.type_name'] = array('like',"%$type_name%");
        }
        if($stu_name != ''){
            $where['stu_name'] = array('like',"%$stu_name%");
        }
        if($stu_no != ''){
            $where['stu_no'] = array('like',"%$stu_no%");
        }
        //分页
        $count=$this->model->where($where)->count();
        $page = $this->page($count, 8);
        $this->assign("page", $page->show('Admin'));

        $res = $this->model->field('stu_name,cmf_xi.xi_name,cmf_depart.depart_name,class_id,cmf_add_point.id,cmf_add_point.type_name,cmf_student.stu_no')
            ->join('cmf_student on cmf_student.stu_id = cmf_add_point.stu_id')
            ->join('cmf_xi on cmf_xi.xi_id = cmf_student.xi_id')
            ->join('cmf_depart on cmf_depart.depart_id = cmf_student.depart_id')
            ->limit($page->firstRow , $page->listRows)
            ->where($where)
            ->select();
//        print_r($this->model->getLastsql());die;
        foreach($res as $k=>$v){
            $data[$k]['id'] = $v['id'];
            $data[$k]['stu_name'] = $v['stu_name'];
            $data[$k]['depart_name'] = $v['depart_name'];
            $data[$k]['class_id'] = $v['class_id'];
            $data[$k]['xi_name'] = $v['xi_name'];
            $data[$k]['stu_no'] = $v['stu_no'];
            $data[$k]['type_name'] = $v['type_name'];

        }

        $this->assign('data',$data);
        $this -> display();
    }

    public function check(){
        $id = I("get.id",0,'intval');
        $sel = $this->model->field('start_time,end_time,add_res,add_note')->where(array('id'=>$id))->find();
        $start_time = date('Y-m-d H:i:s',$sel['start_time']);
        $end_time = date('Y-m-d H:i:s',$sel['end_time']);
        $add_res = $sel['add_res'];
        $add_note = $sel['add_note'];
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->assign('add_res',$add_res);
        $this->assign('add_note',$add_note);
        $this->display();
    }

    public function pass(){
        //通过
        $id = I("get.id",0,'intval');

        if ($this->model->where(array('id'=>$id))->save(array('status'=>1))!==false) {
            $this->success('成功！');
        }else{
            $this->error('失败！');
        }

    }

    public function rebut(){
        //驳回
        $id = I("get.id",0,'intval');

        if ($this->model->where(array('id'=>$id))->save(array('status'=>2))!==false) {
            $this->success('成功！');
        }else{
            $this->error('失败！');
        }

    }

}
