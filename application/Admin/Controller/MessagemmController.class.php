<?php
    namespace Admin\Controller;

    use Common\Controller\AdminbaseController;

    class MessagemmController extends AdminbaseController {

        // 参数
        private $params;
        private $ximdl;
        private $depmdl;
        private $classmdl;
        private $pageNum;

        public function _initialize() {
            $this->params = I('param.');
            parent::_initialize();
            $this->model = M('counsellor');
            $this->ximdl = M('xi');
            $this->depmdl = M('depart');
            $this->classmdl = M('class');
            $this->usermdl = M('users');
            $this->pageNum = 10;
        }

        //查看班主任信息
        function index() {
            $xi_data = $this->ximdl->select();
            $this->assign('xi',$xi_data);
            //搜索start
            if ($this->params['ban_name']) {
                $where['ban_name'] = $this->params['ban_name'] ;
            }
            if ($this->params['ban_no']) {
                $where['ban_no'] = $this->params['ban_no'] ;
            }
            if ($this->params['ban_xi']) {
                $where['xi_id'] = $this->params['ban_xi'] ;
            }
            if ($this->params['ban_dep']) {
                $where['depart_id'] = $this->params['ban_dep'] ;
            }
            if ($this->params['class_no']) {
                $class = $this->classmdl->where("class_no=" . $this->params['class_no'])->field('class_id')->find();
                $where['class_id'] = $class['class_id'] ;
            }
            //搜索end
            $count = $this->model->where($where)->count();
            $page = $this->page($count, $this->pageNum);
            $data = $this->model->where($where)->limit($page->firstRow , $page->listRows)->order('class_id')->select();
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

        //添加班主任页面
        public function add() {
            $xi_data = $this->ximdl->select();
            $this->assign('xi',$xi_data);
            $this->display();
        }

        //添加班主任
        public function add_post() {
            !isset($this->params['ban_name']) && $this->error('请填写班主任姓名');
            !isset($this->params['ban_xi']) && $this->error('请选择院系');
            !isset($this->params['ban_dep']) && $this->error('请选择专业');
            !isset($this->params['ban_class']) && $this->error('请选择班级');
            $data['ban_name'] = $this->params['ban_name'];
            $data['xi_id'] = $this->params['ban_xi'];
            $data['depart_id'] = $this->params['ban_dep'];
            $data['class_id'] = $this->params['ban_class'];
            //工号start
            $ban_no = $this->model->max(ban_no);
            $ban_no = substr($ban_no,-1,2);
            if(!isset($ban_no) || empty($ban_no)) {
                $ban_no = '01';
            }else{
                $ban_no = ++$ban_no;
                if ($ban_no <= 9) {
                    $ban_no = '0' . $ban_no;
                }
            }
            $date = date('Y');
            $data['ban_no'] = 'ban'.$date.$ban_no;
            //end
            if ($this->model->data($data)->add()){
                $id = $this->model->where("ban_no='".$data['ban_no']."'")->field('ban_id')->find();
                $data_logn['user_login'] = $data['ban_name'];
                $data_logn['rele_id'] = $id['ban_id'];
                $data_logn['user_pass'] = sp_password($data['ban_no']);
                $data_logn['user_email'] = '823650031@qq.com';
                if($this->usermdl->data($data_logn)->add()) {
                    $id = $this->usermdl->where("user_login='".$data_logn['user_login']."'")->field('id')->find();
                    $role_mdl = M('role_user');
                    $data_role['role_id'] = 4;
                    $data_role['user_id'] = $id['id'];
                    if($role_mdl->data($data_role)->add()) {
                        $this->success("添加成功");
                    }else{
                        $this->error("添加角色用户失败");
                    }
                }else{
                    $this->error("添加用户失败");
                }
            } else {
                $this->error("添加失败");
            }

        }

        //删除班主任信息
        public function delete() {
            $ids = $this->params['ids'];
            $id = $this->params['ban_id'];
            if(is_array($ids)) {
                $where['ban_id'] = array('in', $ids);
            } elseif(is_numeric($id)) {
                $where['ban_id'] = $id;
            }

            $where || $this->error(L('NO_DATA'));
            $no = $this->model->where($where)->field('ban_no,class_id')->select();
            $res = $this->model->where($where)->delete();
            if($res) {
                foreach($no as $value) {
                    $where_no['ban_no'] = array('gt',$value['ban_no']);
                    $where_no['class_id'] = $value['class_id'];
                    $bool[] = $this->model->where($where_no)->setDec('ban_no',1);
                }
                if(array_key_exists('false',$bool)) {
                    $this->error("失败");
                }else {
                    $this->success("删除成功");
                }
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
            $class = $this->model->field('class_id')->select();
            foreach($data as $k=>$v){
                foreach($class as $value) {
                    if($v['class_id'] == $value['class_id']) {
                        unset($data[$k]);
                    }
                }
            }
            $this->ajaxReturn($data);
        }

        public function get_data() {
            $data = $this->model->where('ban_id='.$this->params['ban_id'])->find();
            $data['c_id'] = $data['id'];
            $this->ajaxReturn($data);
        }

        //修改班主任信息
        public function up_data() {
            !isset($this->params['ban_name']) && $this->error('请填写班主任姓名');
            !isset($this->params['xi_id']) && $this->error('请选择院系');
            !isset($this->params['depart_id']) && $this->error('请选择专业');
            !isset($this->params['class_id']) && $this->error('请选择班级');
            $ban_id = $this->params['ban_id'];
            $xi_id = $this->params['xi_id'];
            $depart_id = $this->params['depart_id'];
            $class_id = $this->params['class_id'];
            $ban_name = $this->params['ban_name'];
            //从数据库中拿到class_id
            $mdep = $this->model->getFieldByban_id($ban_id, 'depart_id');
            //从数据库中拿到原有的xi_id
            $mxi = $this->model->getFieldByban_id($ban_id, 'xi_id');
            //当修改院系、专业或者班级时
            if ($mxi != $xi_id || $mdep != $depart_id) {
                $data['depart_id'] = $depart_id;
                $data['xi_id'] = $xi_id;
                $data['class_id'] = $class_id;
                $data['ban_name'] = $ban_name;
            }else{
                $data['ban_name'] = $ban_name;
            }
            $mname = $this->model->getFieldByban_id($ban_id, 'ban_name');
            if($this->model->where('ban_id='.$ban_id)->save($data)){
                $where_u['user_login'] = $mname;
                $where_u['rele_id'] = $ban_id;
                if($this->usermdl->where($where_u)->save(array('user_login'=>$data['ban_name']))) {
                    $this->success("修改成功");
                }else{
                    $this->error("修改用户失败");
                }
            }else{
                $this->error('修改失败');
            }
        }
    }