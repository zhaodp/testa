<?php
/**
 * 财务补偿用户和司机
 * User: Bidong
 * Date: 13-6-20
 * Time: 下午10:02
 * To change this template use File | Settings | File Templates.
 */

class ComplainRecoupAction extends CAction
{

    public function run()
    {
        $model = new CustomerComplainRecoup();
        $model::$db = Yii::app()->db_readonly;
        $start_time = $end_time = '';
        $attArr = $model->attributeLabels();
        $criteria = new CDbCriteria();
        $params = array();
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['search'])) {
            if (isset($_GET['start_time']) && isset($_GET['end_time'])) {
                $start_time = $_GET['start_time'];
                $end_time = $_GET['end_time'];
                $cstr = '';
                if ($start_time) {
                    $cstr = 'create_time>=:s_time';
                    $params[':s_time'] = $start_time;
                }
                if ($end_time) {
                    if ($cstr)
                        $cstr .= ' and create_time<=:e_time';
                    $params[':e_time'] = $end_time;
                }
                $criteria->addCondition($cstr);
            }
            foreach ($_GET as $k => $v) {
                if (array_key_exists($k, $attArr) && !empty($v)) {
                    $criteria->addCondition($k . '=:' . $k);
                    $params[':' . $k] = trim($v);
                    $model->$k = trim($v);
                }
            }
            if(isset($_GET['status'])){
                $criteria->addCondition('status=:status');
                $params[':status'] =$model->status= intval($_GET['status']);
            }

        }else{
            $criteria->addCondition('status=:status');
            $params[':status'] =$model->status= 0;
            $criteria->addCondition('recoup_type=:recoup_type');
            $params[':recoup_type'] =$model->recoup_type=1;
        }

        $criteria->order='create_time desc';
        $criteria->params = $params;
        $dataProvider = new CActiveDataProvider('CustomerComplainRecoup', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20,
            ),
        ));
        $this->controller->render('recoup', array(
            'vmodel' => $dataProvider,
            'model' => $model,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ));
    }


}