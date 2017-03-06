<?php
    namespace Admin\Controller;

    use Common\Controller\AdminbaseController;

    class LunbotuController extends AdminbaseController {

        // 参数
        private $params;

        public function _initialize() {
            $this->params = I('param.');
            parent::_initialize();
            $this->model = M('image');
        }

        //轮播图地址录入
        public function index() {
            $src =  $this->model->order('image_id desc')->select();
            $this->assign('src', $src);
            $this->display();
        }

        //删除
        public function delete() {
            $id = $this->params['image_id'];
            $bool =  $this->model->where('image_id=' . $id)->delete();
            if ($bool) {
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }

        public function add() {
            $mdl_count =  $this->model->count();
            $p_count = count($_FILES['image']['name']);
            $count = $mdl_count+$p_count;
            if($count <= 10) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 3145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->autoSub = false;
                $upload->rootPath = "./public/"; // 设置附件上传根目录
                $upload->savePath = 'upload/'; // 设置附件上传（子）目录
                $info = $upload->upload();
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {// 上传成功 获取上传文件信息
//                    $image = new \Think\Image();
                    foreach($info as $v) {
                       /* $image->open("SMS/public/upload/".$v['savename']);
                        // 生成一个固定大小为150*150的缩略图并保存为thumb.jpg
                        $image->thumb(150, 150,\Think\Image::IMAGE_THUMB_FIXED);*/
                        $this->model->add(array('image_src'=>$v['savename']));
                    }
                    $this->success('上传成功！');
                }
            }else{
                $this->error('轮播图数量已达上限，请删减图片');
            }
        }
    }