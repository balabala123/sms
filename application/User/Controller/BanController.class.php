<?php
    namespace User\Controller;

    use Common\Controller\MemberbaseController;

    class BanController extends MemberbaseController
    {

        function _initialize()
        {
            parent::_initialize();
            $this->rolemdl = M('role_user');
            $this->stumdl = M('student');
            $this->teamdl = M('teacher');
            $this->banmdl = M('counsellor');
            $this->classmdl = M('class');
            $this->depmdl = M('depart');
            $this->ximdl = M('xi');
            $this->submdl = M('subject');
            $this->mdl = M('grade');
            $message = $this->user;
            $this->id = $message['rele_id'];
        }
        public function index() {
            $class_id = $this->banmdl->where('ban_id='.$this->id)->getField('class_id');
            $stu = $this->stumdl->where('class_id='.$class_id)->field('stu_name,stu_id,stu_no')->select();
            foreach($stu as $k=>$v) {
                $data[$k] = $this->mdl->where('stu_id='.$v['stu_id'])->select();
                $data[$k]['name'] = $v['stu_name'];
                $data[$k]['no'] = $v['stu_no'];
            }
            $sum = array();
            foreach($data as $k=>$v) {
                foreach($v as $key=>$value) {
                    if($key != 'name' && $key != 'no') {
                        $arr[$k]['name'] = $v['name'];
                        $arr[$k]['no'] = $v['no'];
                        $subject = $this->submdl->where('subject_id=' . $value['subject_id'])->getfield('subject_name');
                        $arr[$k][$subject] = $value['grade'];
                        $arr[$k]['sum'] += $value['grade'];
                    }
                }
            }
            foreach($arr[0] as $k=>$v) {
                if($k != 'name' && $k != 'no' && $k != 'sum') {
                    $sub[] = $k;
                }
            }
            $newArr=array();
            for($j=0;$j<count($arr);$j++){
                $newArr[]=$arr[$j]['sum'];
            }
            array_multisort($newArr,$arr);
            $this->assign('data',$arr);
            $this->assign('sub_list',$sub);
            $this->display();
        }
    }
