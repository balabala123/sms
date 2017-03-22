<?php
namespace User\Controller;

use Common\Controller\MemberbaseController;

class CenterController extends MemberbaseController {
	
	function _initialize(){
		parent::_initialize();
		$this->rolemdl = M('role_user');
		$this->stumdl = M('student');
		$this->teamdl = M('teacher');
		$this->banmdl = M('counsellor');
		$this->classmdl = M('class');
		$this->depmdl = M('depart');
		$this->ximdl = M('xi');
		$this->submdl = M('subject');
	}
	
    // 会员中心首页
	public function index() {
		/*echo "<pre>";
		print_r($this->user);
		exit;*/
		$message = $this->user;
		$role = $this->rolemdl->where('user_id='.$message['id'])->getField('role_id');
		switch($role){
			case 2:
				$stumsg = $this->stumdl->where('stu_id='.$message['rele_id'])->find();
				$stumsg['class_no'] = $this->classmdl->where('class_id='.$stumsg['class_id'])->getField('class_no');
				$stumsg['xi'] = $this->ximdl->where('xi_id='.$stumsg['xi_id'])->getField('xi_name');
				$stumsg['depart'] = $this->depmdl->where('depart_id='.$stumsg['depart_id'])->getField('depart_name');
				$stumsg['ban_name'] = $this->banmdl->where('class_id='.$stumsg['class_id'])->getField('ban_name');
				$this->assign('stumsg',$stumsg);
				break;
			case 3:
				$teamsg = $this->teamdl->where('teacher_id='.$message['rele_id'])->find();
				$teamsg['subject'] = $this->submdl->where('subject_id='.$teamsg['subject_id'])->getField('subject_name');
				$class_id = explode('-',$teamsg['class_id']);
				$where['class_id'] = array('in',$class_id);
				$class = $this->classmdl->where($where)->field('class_no,class_id')->select();
				$this->assign('class_no',$class);
				$this->assign('teamsg',$teamsg);
				break;
			case 4:
				$banmsg = $this->banmdl->where('ban_id='.$message['rele_id'])->find();
				$banmsg['class_no'] = $this->classmdl->where('class_id='.$banmsg['class_id'])->getField('class_no');
				$banmsg['xi'] = $this->ximdl->where('xi_id='.$banmsg['xi_id'])->getField('xi_name');
				$banmsg['depart'] = $this->depmdl->where('depart_id='.$banmsg['depart_id'])->getField('depart_name');
				$banmsg['ban_name'] = $this->banmdl->where('class_id='.$banmsg['class_id'])->getField('ban_name');
				$this->assign('banmsg',$banmsg);
				break;
		}
		$this->assign($this->user);
    	$this->display(':center');
    }

	public function grade() {
		echo '11111';
	}
}
