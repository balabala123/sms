<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class StuMessageController extends AdminbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = D('Stu_message');
        $this->params = I('params.');
    }

    public function index(){

        $stu_id = $_COOKIE['stu_id'] = 8;
        $stu_msg = M("Student");
        $data = $stu_msg->field('cmf_student.stu_name,cmf_student.stu_no,cmf_xi.xi_name,cmf_depart.depart_name,cmf_class.class_no,
                                 cmf_stu_message.stu_age,cmf_stu_message.stu_email,cmf_stu_message.stu_father_name,
                                 cmf_stu_message.stu_sex,cmf_stu_message.stu_father_job,cmf_stu_message.stu_father_work,
                                 cmf_stu_message.stu_mother_job,cmf_stu_message.stu_mother_name,cmf_stu_message.stu_mother_work,
                                 cmf_stu_message.stu_from,cmf_stu_message.stu_home,cmf_stu_message.stu_phone,
                                 cmf_stu_message.stu_mother_phone,cmf_stu_message.stu_father_phone,cmf_stu_message.stu_birthday,
                                 cmf_stu_message.stu_minzu,cmf_stu_message.stu_face')
                        ->join('cmf_xi on cmf_xi.xi_id = cmf_student.xi_id')
                        ->join('cmf_depart on cmf_depart.depart_id = cmf_student.depart_id')
                        ->join('cmf_class on cmf_class.class_id = cmf_student.class_id')
                        ->join('cmf_stu_message on cmf_stu_message.stu_id = cmf_student.stu_id')
                        ->where(array('cmf_student.stu_id'=>$stu_id))
                        ->find();
        if($data['stu_sex'] == 1){
            $data['stu_sex'] = '男';
        }else{
            $data['stu_sex'] = '女';
        }
        $this->assign("data",$data);
        $this -> display();
    }


}
