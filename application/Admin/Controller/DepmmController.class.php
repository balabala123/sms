<?php
    namespace Admin\Controller;

    use Common\Controller\AdminbaseController;

    class DepmmController extends AdminbaseController
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
        }

        public function index() {
            $xi_data = $this->ximdl->select();
            $this->assign('xi',$xi_data);
            $this->display();
        }

        public function add_xi() {
            !isset($this->params['name']) && $this->error('请填写院系名称');
            $data['xi_name'] = $this->params['name'];
            if ($this->ximdl->data($data)->add()){
                $this->success("添加成功", U('Depmm/index'));
            } else {
                $this->error("添加失败");
            }
        }
            //修改院系
        public function up_xi() {
            !isset($this->params['xi_name']) && $this->error('请填写院系名称');
            $data['xi_name'] = $this->params['xi_name'];
            $id = $this->params['xi_id'];
            if ($this->ximdl->where('xi_id='.$id)->save($data)){
                $this->success("修改成功", U('Depmm/index'));
            } else {
                $this->error("修改失败");
            }
        }

        //删除院系
        public function del_xi() {
            if($this->ximdl->where('xi_id='.$this->params['xi_id'])->delete()){
                $this->success("删除成功", U('Depmm/index'));
            }else{
                $this->error("删除失败");
            }
        }

        //添加专业
        public function add_dep() {
            !isset($this->params['xi_id']) && $this->error('请选择院系');
            !isset($this->params['dep_name']) && $this->error('请填写专业名称');
            $data['xi_id'] = $this->params['xi_id'];
            $data['depart_name'] = $this->params['dep_name'];
            if ($this->depmdl->data($data)->add()){
                $this->success("添加成功", U('Depmm/index'));
            } else {
                $this->error("添加失败");
            }
        }

        //修改院系
        public function up_dep() {
            !isset($this->params['dep_name']) && $this->error('请填写专业名称');
            $data['depart_name'] = $this->params['dep_name'];
            $id = $this->params['dep_id'];
            if ($this->depmdl->where('depart_id='.$id)->save($data)){
                $this->success("修改成功", U('Depmm/index'));
            } else {
                $this->error("修改失败");
            }
        }

        //删除专业
        public function del_dep() {
            if($this->depmdl->where('depart_id='.$this->params['dep_id'])->delete()){
                $this->success("删除成功", U('Depmm/index'));
            }else{
                $this->error("删除失败");
            }
        }

        //添加班级
        public function add_class() {
            /*形成班级号，
              前两位有当前年份的后两位组成，
              中间三位所属院系的序号组成
              后三位由新增班级在所属院系中的排列顺序决定
            */
            //获取前两位
            $date = date("Y");
            $date = substr($date, 2, 2);
            //获取中间第三位
            $med = $this->params['dep_id'];
            if ($med < 10) {
                $med = '00' . $med;
            } elseif ($med < 100) {
                $med = '0' . $med;
            }
            //后三位
            $count = $this->classmdl->where('depart_id=' . $this->params['dep_id'])->count();
            $count = ++$count;
            if ($count < 10) {
                $last = '00' . $count;
            } elseif ($count < 100) {
                $last = '0' . $count;
            } else {
                $last = $count;
            }
            $data['class_no'] = $date . $med . $last;
            $data['depart_id'] = $this->params['dep_id'];
            $bool = $this->classmdl->data($data)->add();
            if ($bool) {
                $this->success("添加成功", U('Depmm/index'));
            }else{
                $this->error("添加失败");
            }
        }

        public function del_class() {
            $class_id = $this->classmdl-> where('depart_id='.$this->params['dep_id'])->order('class_id desc')->field('class_id')->select();
            $bool = $this->classmdl->where('class_id='.$class_id[0]['class_id'])->delete();
            if ($bool) {
                $this->success("删除成功", U('Depmm/index'));
            }else{
                $this->error("删除失败");
            }
        }
    }