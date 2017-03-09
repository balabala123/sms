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
        }

        public function index() {
            $data = sp_get_current_user();
            echo "<pre>";
            print_r($_SESSION);
            exit;
            $data = $this->model->where('teacher_id='.$id)->field('teacher_name,teacher_no,subject_id,class_id')->find();
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