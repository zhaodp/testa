<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-9-2
 * Time: 下午5:11
 */
class DictAction extends CAction
{

    public function run()
    {
        $model = new Dict();

        $criteria = new CDbCriteria();
        $criteria->select = "*";
        $params = array();
        if (isset($_GET['Dict'])) {
            if (!empty($_GET['Dict']['dictname'])) {
                $criteria->addCondition('dictname = :dictname');
                $params[':dictname'] = $_GET['Dict']['dictname'];
                $model->dictname = $_GET['Dict']['dictname'];
            }

            if (!empty($_GET['Dict']['name'])) {
                $criteria->addCondition('name = :name');
                $params[':name'] = $_GET['Dict']['name'];
                $model->name = $_GET['Dict']['name'];
            }
            $criteria->params = $params;
        }
        $criteria->order = 'id desc';
//        $criteria->order = 'dictname asc,postion asc';

        $dataProvider = new CActiveDataProvider($model, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20
            ),
        ));

        $this->controller->render('dict', array(
            'model' => $model,
            'dataProvider' => $dataProvider,
        ));
    }
}