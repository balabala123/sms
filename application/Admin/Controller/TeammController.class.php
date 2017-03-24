<?php
    namespace Admin\Controller;

    use Common\Controller\AdminbaseController;

    class TeammController extends AdminbaseController
    {

        // 参数
        private $params;
        private $submdl;
        private $depmdl;
        private $classmdl;

        public function _initialize()
        {
            $this->params = I('param.');
            parent::_initialize();
            $this->model = M('teacher');
            $this->submdl = M('subject');
            $this->depmdl = M('depart');
            $this->classmdl = M('class');
            $this->usermdl = M('users');
            $this->pageNum = 5;
        }

        public function index() {
            $sub = $this->submdl->select();
            $this->assign('subject',$sub);
            //搜索start
            if ($this->params['tea_name']) {
                $where['teacher_name'] = $this->params['tea_name'] ;
            }
            if ($this->params['tea_no']) {
                $where['teacher_no'] = $this->params['tea_no'] ;
            }
            if ($this->params['sub']) {
                $where['subject_id'] = $this->params['sub'] ;
            }
            if ($this->params['cls']) {
                $id = $this->classmdl->where('class_no='.$this->params['cls'])->field('class_id')->find();
                $where['class_id'] = get_search_str($id['class_id']) ;
            }
            //搜索end
            $count = $this->model->where($where)->count();
            $page = $this->page($count, $this->pageNum);
            $data = $this->model->where($where)->limit($page->firstRow , $page->listRows)->select();
            foreach($data as $key=>$item) {
                $sub = $this->submdl->where("subject_id=" . $item['subject_id'])->field('subject_name')->find();
                $data[$key]['sub'] = $sub['subject_name'];
                $clsd = explode('-',$item['class_id']);
                $where['class_id'] = array('in',$clsd);
                $clsno = $this->classmdl->where($where)->field('class_no')->select();
                foreach($clsno as $value) {
                    $data[$key]['class_no'] .= $value['class_no'].' ';
                }
            }
            $this->assign("page", $page->show('Admin'));
            $this->assign('list',$data);
            $this->display();
        }

        public function add() {
            $sub = $this->submdl->select();
            $this->assign('subject',$sub);
            $this->display();
        }

        public function change() {
            $data = $this->submdl->where('subject_id=' . $this->params['subject_id'])->field('depart_id')->find();
            $data = explode(',',$data['depart_id']);
            $where['depart_id'] = array('in', $data);
            $dep = $this->depmdl->where($where)->field('depart_id,depart_name')->select();
            $this->ajaxReturn($dep);
        }

        //添加教师
        public function add_post() {
            !isset($this->params['tea_name']) && $this->error('请填写教师姓名');
            !isset($this->params['subj']) && $this->error('请选择课程');
            !isset($this->params['dep']) && $this->error('请选择专业');
            !isset($this->params['cls']) && $this->error('请选择班级');
            $data['teacher_name'] = $this->params['tea_name'];
            $data['subject_id'] = $this->params['subj'];
            $data['depart_id'] = $this->params['dep'];
            $data['class_id'] = implode('-',$this->params['cls']);
            $maxno = $this->model->max('teacher_no');
            $data['teacher_no'] = ++$maxno;
            if($data['teacher_no']<10) {
                $data['teacher_no'] = '0'.$data['teacher_no'];
            }
            $date = date("Y");
            $data['teacher_pwd'] = sp_password($date.$data['teacher_no']);
            if ($this->model->data($data)->add()){
                $id = $this->model->where('teacher_no='.$data['teacher_no'])->field('teacher_id')->find();
                $data_logn['user_login'] = $data['teacher_name'];
                $data_logn['rele_id'] = $id['teacher_id'];
                $data_logn['user_pass'] = $data['teacher_pwd'];
                $data_logn['user_email'] = '823650031@qq.com';
                $data_logn['user_type'] = 2;
                if( $this->usermdl->data($data_logn)->add()) {
                    $id =  $this->usermdl ->where("user_login='".$data_logn['user_login']."'")->field('id')->find();
                    $role_mdl = M('role_user');
                    $data_role['role_id'] = 3;
                    $data_role['user_id'] = $id['id'];
                    if($role_mdl->data($data_role)->add()) {
                        $this->success("添加成功", U('Teamm/add'));
                    }else{
                        $this->error("添加角色用户失败");
                    }
                }else{
                    $this->error("添加用户失败");
                }
                $this->success("添加成功", U('Teamm/add'));
            } else {
                $this->error("添加失败");
            }

        }

        //删除信息
        public function delete() {
            $ids = $this->params['ids'];
            $id = $this->params['teacher_id'];
            if(is_array($ids)) {
                $where['teacher_id'] = array('in', $ids);
            } elseif(is_numeric($id)) {
                $where['teacher_id'] = $id;
            }
            $where || $this->error(L('NO_DATA'));
            $res = $this->model->where($where)->delete();
            if($res) {
                $this->success("删除成功");
            }else{
                $this->error("删除失败");
            }
        }

        //修改获取信息
        public function get_data() {
            $data = $this->model->where('teacher_id='.$this->params['teacher_id'])->find();
                $clsd = explode('-',$data['class_id']);
                $where['class_id'] = array('in',$clsd);
                $clsno = $this->classmdl->where($where)->field('class_no')->select();
                foreach($clsno as $value) {
                    $data['class_no'] .= $value['class_no'].' ';
                }
            $dep = $this->depmdl->where("depart_id=" . $data['depart_id'])->field('depart_name')->find();
            $data['dep'] = $dep['depart_name'];
            $this->ajaxReturn($data);
        }

        //修改
        public function up_data() {
            if(isset($this->params['cls'])) {
                !isset($this->params['name']) && $this->error('请填写教师姓名');
                !isset($this->params['subject']) && $this->error('请选择课程');
                !isset($this->params['depart']) && $this->error('请选择专业');
                $id = $this->params['id'];
                $data['subject_id'] = $this->params['subject'];
                $data['depart_id'] = $this->params['depart'];
                $data['teacher_name'] = $this->params['name'];
                $data['class_id'] = implode('-',$this->params['cls']);
            }else{
                $data['teacher_name'] = $this->params['name'];
                $id = $this->params['id'];
            }
            $mname = $this->model->getFieldByteacher_id($id, 'teacher_name');
            $bool = $this->model->where('teacher_id=' . $id)->save($data);
            if ($bool) {
                $where_u['user_login'] = $mname;
                $where_u['rele_id'] = $id;
                if($this->usermdl->where($where_u)->save(array('user_login'=>$data['teacher_name']))) {
                    $this->success("修改成功");
                }else{
                    $this->error("修改用户失败");
                }
            } else {
                $this->error("修改失败");
            }
        }
    }