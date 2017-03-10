<?php
    namespace Admin\Controller;
    use Common\Controller\AdminbaseController;

    class ClassmsgController extends AdminbaseController
    {
        protected $model;
        protected $submdl;
        protected $classmdl;
        private $params;

        public function _initialize()
        {
            parent::_initialize();
            $this->model = D('counsellor');
            $this->stumdl = D('student');
            $this->classmdl = D('class');
            $this->ximdl = D('xi');
            $this->params = I('params.');
            $ID = $_SESSION['ADMIN_ID'];
            $users = M('users');
            $user_id = $users->where('id=' . $ID)->field('rele_id')->find();
            $this->id = $user_id['rele_id'];
        }

        public function index() {
            $this->id = 9;
            if($_GET['id']) {
                $class_id = $_GET['id'];
            }else{
                $class = $this->model->where('ban_id='.$this->id)->field('class_id')->find();
                $class_id = $class['class_id'];
            }
            $data = $this->stumdl->where('class_id='.$class_id)->field('stu_id,stu_name,stu_no')->select();
            $this->assign('list',$data);
            $this->display();
        }
    }