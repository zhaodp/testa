<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-14
 * Time: 下午9:06
 * To change this template use File | Settings | File Templates.
 */

class ComplainInfoAction extends CAction {

    public function  run(){

        if ($_POST) {
            $id = Yii::app()->request->getPost('id');
            $top_type = Yii::app()->request->getPost('top_type');
            $second_type = Yii::app()->request->getPost('second_type');
            $ret = CustomerComplain::model()->setComplainType($id,$second_type);
            if ($ret) {
                $topType = CustomerComplainType::model()->getTypeById($top_type);
                $secondType = CustomerComplainType::model()->getTypeById($second_type);
                CnodeLog::model()->pushCnodeLog($id,CnodeLog::CLASS_SET,'一级分类：'.$topType['name'].'|二级分类:'.$secondType['name']);
                $res['succ'] = 1;
            } else {
                $res['succ'] = 0;
            }
            echo json_encode($res);
            Yii::app()->end();
        } else {
            $id = Yii::app()->request->getQuery('id');

            $model = new CustomerComplain();
            $data = $model->getComplainById($id);
            //var_dump($data);die;

            //获取一级分类
            $complainType = CustomerComplainType::model()->getComplainTypeByID(0);
            $typeArr = array('-1' => '---全部---');
            foreach ($complainType as $item) {
                $typeArr[$item->id] = $item->name;
            }

            $parent_id = '';
            $child_id = '';
            if (!empty($data['complain_type'])) {
                $child_id = $data['complain_type'];
            }
            $subtypeArr = array('-1' => '---全部---');
            if (!empty($child_id)) {//获取二级分类
                $type = CustomerComplainType::model()->getTypeById($child_id);
                $parent_id = $type['parent_id'];
                $subtype = CustomerComplainType::model()->getComplainTypeByID((int)$parent_id);
                if (!empty($subtype)) {
                    foreach($subtype as $v) {
                        $subtypeArr[$v->id] = $v->name;
                    }
                }
            }

//            echo $parent_id;
//            echo $child_id;die;
            $this->controller->renderPartial('info',array(
                'data'=>$data,
                'typelist'=>$typeArr,
                'subtypelist'=>$subtypeArr,
                'parent_id'=>$parent_id,
                'child_id'=>$child_id
            ));
        }
    }
}