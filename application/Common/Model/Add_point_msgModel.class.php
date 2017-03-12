<?php
namespace Common\Model;
use Common\Model\CommonModel;
class Add_point_msgModel extends CommonModel {

    /**
     * 自动验证
     *
     */
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('type_name', 'require', '请填写类型！', 1, 'regex', CommonModel:: MODEL_BOTH ),
        array('point', 'require', '请填写加分！', 1, 'regex', CommonModel:: MODEL_BOTH ),
        array('type_name', 'checkAction', '加分项类型已经存在！', 1, 'callback', CommonModel:: MODEL_INSERT   ),
        array('type_id,type_name', 'checkActionUpdate', '加分项类型已经存在！', 1, 'callback', CommonModel:: MODEL_UPDATE   ),

    );

    /**
     * 验证action是否重复添加
     *
     */
    public function checkAction($data) {
        //检查是否重复添加
        $find = $this->where(array('type_name'=>$data))->find();
        if ($find) {
            return false;
        }
        return true;
    }

    /**
     * 验证更新时action是否重复添加
     *
     */
    public function checkActionUpdate($data) {
        //检查是否重复添加
        $id=$data['type_id'];
        $find = $this->field('type_id')->where(array('type_name'=>$data['type_name'],'disabled'=>'0'))->find();
        if (isset($find['type_id']) && $find['type_id']!=$id) {
            return false;
        }
        return true;
    }



}