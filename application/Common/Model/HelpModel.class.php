<?php
namespace Common\Model;
use Common\Model\CommonModel;
class HelpModel extends CommonModel {

    /**
     * 自动验证
     *
     */
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('type_name', '请选择', '请选择助学金类型！', 1, 'notequal', CommonModel:: MODEL_INSERT ),
        array('sy_note', '', '请填写家庭状况！', 1, 'notequal', CommonModel:: MODEL_INSERT ),
        array('type_name', 'checkAction', '已经申请过同类型助学金！', 1, 'callback', CommonModel:: MODEL_INSERT   ),
    );

    /**
     * 验证action是否重复添加
     *
     */
    public function checkAction($data) {
        //检查是否重复添加
        $ty = preg_replace("/\(.*\)/", '', $data);
        $find = $this->where(array('type_name'=>$ty))->find();
        if ($find) {
            return false;
        }
        return true;
    }

}