<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class ScApproveController extends AdminbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = D('Reward');
        $this->params = I('params.');
    }

    public function index(){
//        $stu_id =get_current_admin_id();
//        $stu_id = get_current_userid();
//        print_r($stu_id);die;
        $where['cmf_reward.disabled'] = 0;
        $where['cmf_reward.status'] = 0;
        //搜索
        $xi_id = trim(I('request.xi_id'));
        $depart_id = trim(I('request.depart_id'));
        $class_id = trim(I('request.class_id'));
        $type_name = trim(I('request.type_name'));
        $stu_name = trim(I('request.stu_name'));
        $stu_id = trim(I('request.stu_id'));
        if($xi_id != ''){
            $where['cmf_reward.xi_id'] = array('like',"%$xi_id%");
        }
        if($type_name || ($type_name == 0 && $type_name != '')){
            $where['cmf_reward.type_name'] = array('like',"%$type_name%");
        }
        if($type_name || ($type_name == 0 && $type_name != '')){
            $where['cmf_reward.type_name'] = array('like',"%$type_name%");
        }
        if($type_name || ($type_name == 0 && $type_name != '')){
            $where['cmf_reward.type_name'] = array('like',"%$type_name%");
        }
        if($stu_name || ($stu_name == 0 && $stu_name != '')){
            $where['stu_name'] = array('like',"%$stu_name%");
        }
        if($stu_id || ($stu_id == 0 && $stu_id != '')){
            $where['cmf_reward.stu_id'] = array('like',"%$stu_id%");
        }
        //分页
        $count=$this->model->where($where)->count();
        $page = $this->page($count, 8);
        $this->assign("page", $page->show('Admin'));

        $res = $this->model->field('stu_name,cmf_xi.xi_name,cmf_depart.depart_name,class_id,cmf_reward.id,cmf_reward.type_name,cmf_reward.stu_id')
                            ->join('cmf_student on cmf_student.stu_id = cmf_reward.stu_id')
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
            $data[$k]['stu_id'] = $v['stu_id'];
            $data[$k]['type_name'] = $v['type_name'];

        }

        $this->assign('data',$data);
        $this -> display();
    }

    public function check(){
        $id = I("get.id",0,'intval');
        $sel = $this->model->field('sc_note')->where(array('id'=>$id))->find();
        $sc_note = $sel['sc_note'];
        $this->assign('sc_note',$sc_note);
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
