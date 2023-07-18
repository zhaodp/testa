<?php
class ComplainDelMaterialAction extends CAction {
    public function run() {
        $res = array('succ'=>0,'errmsg'=>'');
        $cid = Yii::app()->request->getQuery('cid');
        $mids = Yii::app()->request->getQuery('mids');

        if (is_array($mids)) {
            foreach ($mids as $mid) {
                CustomerComplainMaterial::model()->delMaterial($mid);
            }
            $res['succ'] = 1;
        } else if (!empty($mids)) {
            CustomerComplainMaterial::model()->delMaterial($mids);
            $res['succ'] = 1;
        }

        echo json_encode($res);
    }
}