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
            $this->grademdl = M('grade');
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
            if($this->params['id']) {
                $class_id = $this->params['id'];
            }else{
                $class_id = $class_id[0];
            }
            $this->assign('class_id',$class_id);
            $sql = "select s.stu_name,s.stu_no,s.stu_id,g.grade,g.grade_id from cmf_student s,cmf_grade g where class_id=".$class_id.' and s.stu_id=g.stu_id';
            $student = $this->model->query($sql);
            if(!$student) {
                $student = $this->stumdl->where('class_id=' . $class_id)->field('stu_name,stu_no,stu_id')->select();
            }
            $this->assign('stu_list',$student);
            //end
            $this->display();
        }

        public function grade()
        {
           if(!$this->params['grade_id'][0]) {
               foreach ($this->params['stu_id'] as $k => $v) {
                   $data[$k]['stu_id'] = $v;
               }
               foreach ($this->params['grade'] as $k => $item) {
                   $data[$k]['grade'] = $item;
               }
               $id = 25;
               $subject = $this->model->where('teacher_id=' . $id)->field('subject_id')->find();
               foreach ($data as $k => $v) {
                   $data[$k]['subject_id'] = $subject['subject_id'];
               }
               $this->grademdl->startTrans();
               foreach ($data as $k => $v) {
                   $bool[] = $this->grademdl->data($v)->add();
               }
               if (!array_key_exists('false', $bool)) {
                   $this->grademdl->commit();
                   $this->success('操作成功');
               } else {
                   $this->error('操作失败');
                   $this->grademdl->rollback();
               }
           }else{
               foreach ($this->params['stu_id'] as $k => $v) {
                   $data[$k]['stu_id'] = $v;
               }
               foreach ($this->params['grade'] as $k => $item) {
                   $data[$k]['grade'] = $item;
               }
               foreach ($this->params['grade_id'] as $k => $item) {
                   $data[$k]['grade_id'] = $item;
               }
               $id = 25;
               $subject = $this->model->where('teacher_id=' . $id)->field('subject_id')->find();
               foreach ($data as $k => $v) {
                   $data[$k]['subject_id'] = $subject['subject_id'];
               }
               $this->grademdl->startTrans();
               foreach ($data as $k => $v) {
                   $bool[] = $this->grademdl->save($v);
               }
               if (!array_key_exists('false', $bool)) {
                   $this->grademdl->commit();
                   $this->success('操作成功');
               } else {
                   $this->error('操作失败');
                   $this->grademdl->rollback();
               }
           }
        }
    }