<?php
    namespace Admin\Controller;

    use Common\Controller\AdminbaseController;

    class TeaGradeController extends AdminbaseController
    {

        // 参数
        private $params;
        private $stumdl;
        private $depmdl;
        private $classmdl;
        private $pageNum;

        public function _initialize()
        {
            $this->params = I('param.');
            parent::_initialize();
            $this->model = M('teacher');
            $this->stumdl = M('student');
            $this->depmdl = M('depart');
            $this->classmdl = M('class');
            $this->pageNum = 1;
        }

        public function index() {
            $id = 25;
            $data = $this->model->where('teacher_id='.$id)->field('class_id')->find();
            $class_id = explode('-',$data['class_id']);
            $where['class_id'] = array('in',$class_id);
            $class = $this->classmdl->where($where)->field('class_no,class_id')->select();
            $this->assign('class',$class);
            //学生成绩start
            /*if($this->params['id'])*/
            //end
            $this->display();
        }

        public function grade()
        {
            $id = $this->params['id'];
            $student = $this->stumdl->where('class_id='.$id)->field('stu_name,stu_no')->select();
        }
    }