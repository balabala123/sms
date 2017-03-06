<?php
    namespace Admin\Controller;

    use Common\Controller\AdminbaseController;

    class StummController extends AdminbaseController {

        // 参数
        private $params;
        private $ximdl;
        private $depmdl;
        private $classmdl;
        private $pageNum;

        public function _initialize() {
            $this->params = I('param.');
            parent::_initialize();
            $this->model = M('student');
            $this->ximdl = M('xi');
            $this->depmdl = M('depart');
            $this->classmdl = M('class');
            $this->pageNum = 1;
        }

        //查看学生信息
        function index() {
            $xi_data = $this->ximdl->select();
            $this->assign('xi',$xi_data);
            //搜索start
            if ($this->params['stu_name']) {
                $where['stu_name'] = $this->params['stu_name'] ;
            }
            if ($this->params['stu_xi']) {
                $where['xi_id'] = $this->params['stu_xi'] ;
            }
            if ($this->params['stu_dep']) {
                $where['depart_id'] = $this->params['stu_dep'] ;
            }
            if ($this->params['stu_class']) {
                $where['class_id'] = $this->params['stu_class'] ;
            }
            //搜索end
            $count = $this->model->where($where)->count();
            $page = $this->page($count, $this->pageNum);
            $data = $this->model->where($where)->limit($page->firstRow , $page->listRows)->select();
            foreach($data as $key=>$item) {
                $xi = $this->ximdl->where("xi_id=" . $item['xi_id'])->field('xi_name')->find();
                $data[$key]['xi'] = $xi['xi_name'];
                $dep = $this->depmdl->where("depart_id=" . $item['depart_id'])->field('depart_name')->find();
                $data[$key]['depart'] = $dep['depart_name'];
                $class = $this->classmdl->where("class_id=" . $item['class_id'])->field('class_no')->find();
                $data[$key]['class'] = $class['class_no'];
            }
            $this->assign("page", $page->show('Admin'));
            $this->assign('list',$data);
            $this->display();
        }

        //添加学生页面
        public function add() {
            $xi_data = $this->ximdl->select();
            $this->assign('xi',$xi_data);
            $this->display();
        }

        //添加学生
        public function add_post() {
            !isset($this->params['stu_name']) && $this->error('请填写学生姓名');
            !isset($this->params['stu_xi']) && $this->error('请选择院系');
            !isset($this->params['stu_dep']) && $this->error('请选择专业');
            !isset($this->params['stu_class']) && $this->error('请选择班级');
            $data['stu_name'] = $this->params['stu_name'];
            $data['xi_id'] = $this->params['stu_xi'];
            $data['depart_id'] = $this->params['stu_dep'];
            $data['class_id'] = $this->params['stu_class'];
            //学号start
            $class_no = $this->classmdl->where('class_id='.$data['class_id'])->field('class_no')->find();
            $stu_no = $this->model->where('class_id='.$data['class_id'])->max(stu_no);
            if(!isset($stu_no)) {
                $stu_no = '01';
            }else{
                $stu_no = ++$stu_no;
                if ($stu_no < 9) {
                    $stu_no = '0' . $stu_no;
                }
            }
            $data['stu_pwd'] = sp_password($class_no['class_no']. $stu_no);
            $data['stu_no'] = $stu_no;
            //end
            if ($this->model->data($data)->add()){
            $this->success("添加成功", U('Stumm/add'));
            } else {
                $this->error("添加失败");
            }

        }

        //删除学生信息
        public function delete() {
            $ids = $this->params['ids'];
            $id = $this->params['stu_id'];
            if(is_array($ids)) {
                $where['stu_id'] = array('in', $ids);
            } elseif(is_numeric($id)) {
                $where['stu_id'] = $id;
            }
            $where || $this->error(L('NO_DATA'));
            $res = $this->model->where($where)->delete();
            if($res) {
                $this->success("删除成功");
            }else{
                $this->error("删除失败");
            }
        }
        //三级联动
        public function change()
        {
            $data = $this->depmdl->where('xi_id=' . $this->params['xi_id'])->field('depart_name,depart_id')->select();
            $this->ajaxReturn($data);
        }

        //三级联动2
        public function change_two()
        {
            $data = $this->classmdl->where('depart_id='.$this->params['depart_id'])->field('class_no,class_id')->select();
            $this->ajaxReturn($data);
        }

        public function get_data() {
            $data = $this->model->where('stu_id='.$this->params['stu_id'])->find();
            $data['c_id'] = $data['id'];
            $this->ajaxReturn($data);
        }

        //修改学生信息
        public function up_data() {
            !isset($this->params['stu_name']) && $this->error('请填写学生姓名');
            !isset($this->params['xi_id']) && $this->error('请选择院系');
            !isset($this->params['depart_id']) && $this->error('请选择专业');
            !isset($this->params['class_id']) && $this->error('请选择班级');
            $stu_id = $this->params['stu_id'];
            $xi_id = $this->params['xi_id'];
            $depart_id = $this->params['depart_id'];
            $class_id = $this->params['class_id'];
            $stu_name = $this->params['stu_name'];
            //从数据库中拿到原有的depart_id
            $mdepart = $this->model->getFieldBystu_id($stu_id, 'depart_id');
            //从数据库中拿到姓名
            $mname = $this->model->getFieldBystu_id($stu_id, 'stu_name');
            //从数据库中拿到class_id
            $mclass = $this->model->getFieldBystu_id($stu_id, 'class_id');
            //从数据库中拿到原有的xi_id
            $mxi = $this->model->getFieldBystu_id($stu_id, 'xi_id');
            //当修改院系、专业或者班级时
            if ($depart_id != $mdepart || $mclass != $class_id || $mxi != $xi_id) {
                $class_no = $this->classmdl->where('class_id='.$class_id)->field('class_no')->find();
                $stu_no = $this->model->where('class_id='.$class_id)->max(stu_no);
                if(!isset($stu_no)) {
                    $stu_no = '01';
                }else{
                    $stu_no = ++$stu_no;
                    if ($stu_no < 9) {
                        $stu_no = '0' . $stu_no;
                    }
                }
                $data = array();
                $data['stu_pwd'] = sp_password($class_no['class_no']. $stu_no);
                $data['stu_no'] = $stu_no;
                $data['class_id'] = $class_id;
                $data['depart_id'] = $depart_id;
                $data['stu_name'] = $stu_name;
                $data['xi_id'] = $xi_id;
                $bool = $this->model->where('stu_id=' . $stu_id)->save($data);
                if ($bool) {
                    $this->success("添加成功", U('Stumm/add'));
                } else {
                    $this->error("添加失败");
                }
            } elseif ($mname != $stu_name) {
                $data['stu_name'] = $stu_name;
                $bool = $this->model->where('stu_id=' . $stu_id)->save($data);
                if ($bool) {
                    $this->success("修改成功", U('Stumm/index'));
                } else {
                    $this->error("修改失败");
                }
            } else {
                $this->success("修改成功", U('Stumm/index'));
            }
        }
    }