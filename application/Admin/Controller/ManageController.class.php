<?php
    namespace Admin\Controller;
    use Common\Controller\AdminbaseController;

    class ManageController extends AdminbaseController {
        protected $model;
        protected $submdl;
        protected $classmdl;
        private $params;

        public function _initialize() {
            parent::_initialize();
            $this->model = D('counsellor');
            $this->depmdl = D('depart');
            $this->classmdl = D('class');
            $this->ximdl = D('xi');
            $this->params = I('params.');
            $ID = $_SESSION['ADMIN_ID'];
            $users = M('users');
            $user_id = $users->where('id='.$ID)->field('rele_id')->find();
            $this->id = $user_id['rele_id'];
        }

        public function index() {
            $data = $this->model->where('ban_id='.$this->id)->field('ban_name,ban_no,xi_id,class_id,depart_id')->find();
            $depart = $this->depmdl->where('depart_id='.$data['depart_id'])->field('depart_name')->find();
            $data['depart'] = $depart['depart_name'];
            $class = $this->classmdl->where('class_id='.$data['class_id'])->field('class_no,class_id')->find();
            $xi = $this->ximdl->where('xi_id='.$data['xi_id'])->field('xi_name')->find();
            $data['class_no'] = $class['class_no'];
            $data['class_id'] = $class['class_id'];
            $data['xi'] = $xi['xi_name'];
            $this->assign('list',$data);
            $this->display();
        }
    }