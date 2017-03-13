<?php
    namespace Admin\Controller;
    use Common\Controller\AdminbaseController;

    class TeaMessageController extends AdminbaseController {
        protected $model;
        protected $submdl;
        protected $classmdl;
        private $params;

        public function _initialize() {
            parent::_initialize();
            $this->model = D('teacher');
            $this->submdl = D('subject');
            $this->classmdl = D('class');
            $this->params = I('params.');
            $ID = $_SESSION['ADMIN_ID'];
            $users = M('users');
            $user_id = $users->where('id='.$ID)->field('rele_id')->find();
            $this->id = $user_id['rele_id'];
            $this->id = 30;
        }

        public function index() {
            $data = $this->model->where('teacher_id='.$this->id)->field('teacher_name,teacher_no,subject_id,class_id')->find();
            $subject = $this->submdl->where('subject_id='.$data['subject_id'])->field('subject_name')->find();
            $data['subject'] = $subject['subject_name'];
            $class_id = explode('-',$data['class_id']);
            $where['class_id'] = array('in',$class_id);
            $class = $this->classmdl->where($where)->field('class_no,class_id')->select();
            /*foreach($class as $value) {
                $data['class_no'] .= $value['class_no'].' ';
            }*/
            $this->assign('class',$class);
            $this->assign('list',$data);
            $this->display();
        }
    }