<?php
    namespace Admin\Controller;

    use Common\Controller\AdminbaseController;

    class CoummController extends AdminbaseController
    {
        // 参数
        private $params;
        private $depmdl;
        private $classmdl;

        public function _initialize()
        {
            $this->params = I('param.');
            parent::_initialize();
            $this->depmdl = M('depart');
            $this->classmdl = M('class');
            $this->ximdl = M('xi');
            $this->model = M('subject');
            $this->pageNum = 10;
        }

        public function index() {
            //搜索start
            if ($this->params['sub_id']) {
                $where['subject_id'] = $this->params['sub_id'] ;
            }
            if ($this->params['sub_name']) {
                $where['subject_name'] = $this->params['sub_name'] ;
            }
            if ($this->params['dep_name']) {
                $dep =$this->depmdl->where("depart_name='".$this->params['dep_name']."'")->field('depart_id')->find();
                $where['depart_id'] = get_search_str($dep['depart_id']) ;
            }
            //搜索end
            $count = $this->model->where($where)->count();
            $page = $this->page($count, $this->pageNum);
            $data = $this->model->where($where)->limit($page->firstRow , $page->listRows)->select();
            $this->assign("page", $page->show('Admin'));
            $this->assign('list',$data);
            $this->display();
        }

        public function add() {
            $data = $this->depmdl->select();
            $this->assign('list',$data);
            $this->display();
        }

        public function add_post() {
            !isset($this->params['cou_name']) && $this->error('请填写课程名称');
            $data['depart_id'] = implode(',',$this->params['dep']);
            $data['subject_name'] = $this->params['cou_name'];
            if ($this->model->data($data)->add()){
                $this->success("添加成功", U('Coumm/add'));
            } else {
                $this->error("添加失败");
            }
        }

        //删除信息
        public function delete() {
            $ids = $this->params['ids'];
            $id = $this->params['subject_id'];
            if(is_array($ids)) {
                $where['subject_id'] = array('in', $ids);
            } elseif(is_numeric($id)) {
                $where['subject_id'] = $id;
            }
            $where || $this->error(L('NO_DATA'));
            $res = $this->model->where($where)->delete();
            if($res) {
                $this->success("删除成功");
            }else{
                $this->error("删除失败");
            }
        }

        public function get_data() {
            $data = $this->model->where('subject_id='.$this->params['subject_id'])->find();
            $this->ajaxReturn($data);
        }

        public function up_data() {
            !isset($this->params['name']) && $this->error('请填写课程名称');
            !isset($this->params['id']) && $this->error('课程ID不能为空');
            $data['subject_name'] = $this->params['name'];
            $id = $this->params['id'];
            $bool = $this->model->where('subject_id=' . $id)->save($data);
            if ($bool) {
                $this->success("修改成功", U('Coumm/index'));
            } else {
                $this->error("修改失败");
            }
        }
    }