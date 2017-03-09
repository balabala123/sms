<?php
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class MainController extends AdminbaseController {
	
    public function index(){
    	$model = M('image');
		$data = $model->select();
		foreach($data as $key=>$value) {
			$count[] = $key+2;
		}
		$c = count($count) - 1;
		unset($count[$c]);
		$this->assign('count',$count);
		$this->assign('list',$data);
    	$this->display();
    }
}