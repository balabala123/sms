<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class SymsgController extends AdminbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = D('Help_msg');
        $this->params = I('params.');
    }

    public function index(){
        $where['disabled'] = 0;

        //搜索
        $type_name = trim(I('request.type_name'));
        $money = trim(I('request.money'));

        if($type_name != ''){
            $where['type_name'] = array('like',"%$type_name%");
        }

        if($money != ''){
            $where['money'] = array('like',"%$money%");
        }

        //分页
        $count=$this->model->where($where)->count();
        $page = $this->page($count, 7);
        $this->assign("page", $page->show('Admin'));


        $res = $this->model->limit($page->firstRow , $page->listRows)->where($where)->select();
        foreach($res as $k=>$v){
            $data[$k]['type_id'] = $v['type_id'];
            $data[$k]['type_name'] = $v['type_name'];
            $data[$k]['money'] = $v['money'];
            $data[$k]['quota'] = $v['quota'];
            $data[$k]['start_time'] = date("Y/m/d H:i",$v['start_time']);
            $data[$k]['end_time'] = date("Y/m/d H:i",$v['end_time']);
        }

        $this->assign('data',$data);
        $this -> display();
    }

    public function add(){
        $this->display();
    }

    public function add_post(){
        $post = I("post.");
        $data['type_name'] = $post['type_name'];
        $data['money'] = $post['money'];
        $data['quota'] = $post['quota'];
        $data['start_time'] = strtotime($post['start_time']);
        $data['end_time'] = strtotime($post['end_time']);

        if($this->model->create($data) !== false){
            if($this->model->add() !== false){
                $this->success('添加成功！');
            }else{
                $this->error('添加失败！');
            }
        }else{
            $this->error($this->model->getError());
        }
    }

    public function edit(){
        $id = I("get.id",0,"intval");

        $res = $this->model->where(array("type_id" => $id))->find();

        $data['type_id'] = $res['type_id'];
        $data['type_name'] = $res['type_name'];
        $data['money'] = $res['money'];
        $data['quota'] = $res['quota'];
        $data['start_time'] = date('Y-m-d H:i:s',$res['start_time']);
        $data['end_time'] = date('Y-m-d H:i:s',$res['end_time']);

        $this->assign("data", $data);

        $this->display();
    }

    public function edit_post(){
        $post = I("post.");
        $id = I('post.id',0,'intval');
        $data=$this->model->where(array('type_id'=>$id))->find();
        $data['type_name'] = $post['type_name'];
        $data['money'] = $post['money'];
        $data['quota'] = $post['quota'];
        $data['start_time'] = strtotime($post['start_time']);
        $data['end_time'] = strtotime($post['end_time']);


        if($this->model->create($data) !== false){
            if($this->model->save() !== false){
                $this->success('保存成功！' ,U('Symsg/index'));
            }else{
                $this->error('保存失败！');
            }
        }else{
            $this->error($this->model->getError());
        }

    }

    public function delete(){
        if(isset($_POST['ids'])){
            $ids = I('post.ids/a');
            if ($this->model->where(array('type_id'=>array('in',$ids)))->save(array('disabled'=>1))!==false) {
                $this->success('删除成功！');
            } else {
                $this->error('删除失败！');
            }
        }else{
            $id = I("get.id",0,'intval');
            if ($this->model->where(array('type_id'=>$id))->save(array('disabled'=>1))!==false) {
                $this->success('删除成功！');
            } else {
                $this->error('删除失败！');
            }
        }
    }

}
