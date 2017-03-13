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
            $ID = $_SESSION['ADMIN_ID'];
            $users = M('users');
            $user_id = $users->where('id='.$ID)->field('rele_id')->find();
            $this->id = $user_id['rele_id'];
            $this->id = 30;
        }

        public function index() {
            $data = $this->model->where('teacher_id='.$this->id)->field('class_id,subject_id')->find();
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
           // $sql = "select s.stu_name,s.stu_no,s.stu_id,g.grade,g.grade_id from cmf_student s,cmf_grade g where class_id=".$class_id.' and s.stu_id=g.stu_id';
          //  $student = $this->model->query($sql);
          //  if(!$student) {
                $student = $this->stumdl->where('class_id=' . $class_id)->field('stu_name,stu_no,stu_id')->select();
                $where['subject_id'] = $data['subject_id'];
                foreach($student as $v) {
                    $where['stu_id'] = $v['stu_id'];
                    $grade[] = $this->grademdl->where($where)->field('grade,grade_id')->find();
                }
          //  }
               foreach($student as $k=>$v) {
                   $data_all[$k]['stu_id'] = $v['stu_id'];
                   $data_all[$k]['stu_name'] = $v['stu_name'];
                   $data_all[$k]['stu_no'] = $v['stu_no'];
               }
            foreach($grade as $k=>$v) {
                $data_all[$k]['grade_id'] = $v['grade_id'];
                $data_all[$k]['grade'] = $v['grade'];
            }

            $this->assign('stu_list',$data_all);
            //end
            $this->display();
        }

        public function grade()
        {
            if (!$this->params['grade_id'][0]) {
                foreach ($this->params['stu_id'] as $k => $v) {
                    $data[$k]['stu_id'] = $v;
                }
                foreach ($this->params['grade'] as $k => $item) {
                    $data[$k]['grade'] = $item;
                }
                $subject = $this->model->where('teacher_id=' . $this->id)->field('subject_id')->find();
                foreach ($data as $k => $v) {
                    $data[$k]['subject_id'] = $subject['subject_id'];
                }
                $this->grademdl->startTrans();
                foreach ($data as $k => $v) {
                    $bool[] = $this->grademdl->data($v)->add();
                }
                if (!array_key_exists('false', $bool)) {
                    $this->grademdl->commit();
                    $this->success('录入成功');
                } else {
                    $this->error('录入失败');
                    $this->grademdl->rollback();
                }
            } else {
                foreach ($this->params['stu_id'] as $k => $v) {
                    $data[$k]['stu_id'] = $v;
                }
                foreach ($this->params['grade'] as $k => $item) {
                    $data[$k]['grade'] = $item;
                }
                foreach ($this->params['grade_id'] as $k => $item) {
                    $data[$k]['grade_id'] = $item;
                }
                $subject = $this->model->where('teacher_id=' . $this->id)->field('subject_id')->find();
                foreach ($data as $k => $v) {
                    $data[$k]['subject_id'] = $subject['subject_id'];
                }
                $this->grademdl->startTrans();
                foreach($data as $k=>$v) {
                    if(!$v['grade_id']) {
                        $this->grademdl->data($v)->add();
                    }
                }
                foreach ($data as $k => $v) {
                    $bool[] = $this->grademdl->save($v);
                }
                if (!array_key_exists('false', $bool)) {
                    $this->grademdl->commit();
                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                    $this->grademdl->rollback();
                }
            }
        }

        public function add_grade() {
            !isset($this->params['stu_id']) && $this->error('学生ID为空');
            !isset($this->params['grade']) && $this->error('请填写学生成绩');
            $subject = $this->model->where('teacher_id=' . $this->id)->field('subject_id')->find();
            $data['subject_id'] = $subject['subject_id'];
            $data['stu_id'] = $this->params['stu_id'];
            $data['grade'] = $this->params['grade'];
            $res = $this->grademdl->save($data);
            if($res) {
                $this->success('录入成功');
            }else{
                $this->error('录入失败');
            }
        }

    }