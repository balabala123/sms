<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class StuGradeController extends AdminbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = D('Grade');
        $this->params = I('params.');
    }

    public function index(){
        $stu_id = $_COOKIE['stu_id'] = 8;
        //分页
        $count=$this->model->where(array('stu_id'=>$stu_id))->count();
        $page = $this->page($count, 8);
        $this->assign("page", $page->show('Admin'));


        $sel = $this->model->field('cmf_subject.subject_name,cmf_grade.grade,cmf_subject.score')
            ->join('cmf_subject on cmf_subject.subject_id = cmf_grade.subject_id')
            ->limit($page->firstRow , $page->listRows)
            ->where(array('cmf_grade.stu_id'=>$stu_id))
            ->select();
//             print_r($sel);die;
        foreach($sel as $k=>$v){
            $data[$k]['subject_name'] = $v['subject_name'];
            $data[$k]['grade'] = $v['grade'];
            $data[$k]['score'] = $v['score'];
            if($v['grade']<60){
                $s = 0;
            }elseif($v['grade']>=60 && $v['grade']<65){
                $s = 1;
            }elseif($v['grade']>=65 && $v['grade']<70){
                $s = 1.5;
            }elseif($v['grade']>=70 && $v['grade']<75){
                $s = 2;
            }elseif($v['grade']>=75 && $v['grade']<80){
                $s = 2.5;
            }elseif($v['grade']>=80 && $v['grade']<85){
                $s = 3;
            }elseif($v['grade']>=85 && $v['grade']<90){
                $s = 3.5;
            }elseif($v['grade']>=90 && $v['grade']<95){
                $s = 4;
            }elseif($v['grade']>=95 && $v['grade']<100){
                $s = 4.5;
            }elseif($v['grade']=100){
                $s = 5;
            }
            $data[$k]['jidian'] = $s*$data[$k]['score'];
        }
        $this->assign("data",$data);
        $this -> display();
    }


}
