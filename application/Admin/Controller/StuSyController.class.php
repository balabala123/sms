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

        }

        $this->assign('data',$data);
        $this -> display();
    }

    public function add(){
        $Help_msg = M("Help_msg");
        $sel = $Help_msg->field('type_name')->select();
        foreach ($sel as $v){
            $type[] = $v['type_name'];
        }
//        print_r($type);die;
        $this->assign("type",$type);
        $this->display();
    }

    public function add_post(){
        $data = I('post.');
        $stu_id = $_COOKIE['stu_id']=8;
//        print_r($post);die;
        $data['create_time'] = time();
        $data['stu_id'] = $stu_id;


//        print_r($data);die;
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
