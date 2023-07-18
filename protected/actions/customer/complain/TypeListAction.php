<?php
/**
 * 投诉分类列表
 * User: Bidong
 * Date: 13-6-11
 * Time: 上午1:07
 * To change this template use File | Settings | File Templates.
 */

class TypeListAction extends CAction {
    public function run()
    {
        if($_POST) {
            $typeModel = new CustomerComplainType;
            if(isset($_POST['create'])) {
                //检查是否存在同名分类
                $typeModel->name = trim($_POST['name']);
                $typeModel->status = 1;
                $typeModel->operator = Yii::app()->user->id;
                $typeModel->create_time = date('Y-m-d H:i:s',time());
                $typeModel->save();
            }
            if(isset($_POST['update']) && isset($_POST['CustomerComplainType'])) {
                $typeModel = $typeModel->findByPk($_POST['CustomerComplainType']['id']);
                $typeModel->name = trim($_POST['CustomerComplainType']['name']);
                $typeModel->category = trim($_POST['CustomerComplainType']['category']);
                $typeModel->status = trim($_POST['CustomerComplainType']['status']);
                $typeModel->operator = Yii::app()->user->id;
                $typeModel->update_time = date('Y-m-d H:i:s',time());
                $typeModel->group_id = trim($_POST['CustomerComplainType']['group_id']);
                if(isset($_POST['CustomerComplainType']['weight'])) {
                    $typeModel->weight = trim($_POST['CustomerComplainType']['weight']);
                }
                if(isset($_POST['CustomerComplainType']['score'])) {
                    $typeModel->score = trim($_POST['CustomerComplainType']['score']);
                }
                if(isset($_POST['CustomerComplainType']['should_response_hour'])) {
                    $typeModel->should_response_hour = trim($_POST['CustomerComplainType']['should_response_hour']);
                }
                if(isset($_POST['CustomerComplainType']['should_follow_hour'])) {
                    $typeModel->should_follow_hour = trim($_POST['CustomerComplainType']['should_follow_hour']);
                }
                if(isset($_POST['CustomerComplainType']['should_closing_hour'])) {
                    $typeModel->should_closing_hour = trim($_POST['CustomerComplainType']['should_closing_hour']);
                }

                $typeModel->update();
            }
            if(isset($_POST['createsub']) && isset($_POST['CustomerComplainType'])) {
                $typeModel->name = trim($_POST['CustomerComplainType']['name']);
                $typeModel->category = trim($_POST['CustomerComplainType']['category']);
                $typeModel->status = trim($_POST['CustomerComplainType']['status']);
                $typeModel->parent_id = trim($_POST['CustomerComplainType']['parent_id']);
                $typeModel->operator = Yii::app()->user->id;
                $typeModel->create_time = date('Y-m-d H:i:s',time());
                $typeModel->weight = trim($_POST['CustomerComplainType']['weight']);

                $typeModel->score = trim($_POST['CustomerComplainType']['score']);
                $typeModel->group_id = trim($_POST['CustomerComplainType']['group_id']);
                $typeModel->should_response_hour = trim($_POST['CustomerComplainType']['should_response_hour']);
                $typeModel->should_follow_hour = trim($_POST['CustomerComplainType']['should_follow_hour']);
                $typeModel->should_closing_hour = trim($_POST['CustomerComplainType']['should_closing_hour']);

                $typeModel->save();
            }
            $this->controller->redirect(array('typelist'));
        }
        if($_GET) {
            if(isset($_GET['type'])) {
                $model=new CustomerComplainType;

                //添加子分类
                if ($_GET['type'] == 'add') {
                    $id = trim($_GET['id']);
                    $model = $model->findByPk($id);
                    $model->name = '';
                    $model->parent_id = $id;
                    //获取投诉任务组
                    $groupArr = array();
                    $group = CustomerComplainGroup::model()->getAllGroup();
                    foreach ($group as $k=>$v) {
                        $groupArr[$v['id']] = $v['name'];
                    }
                    $this->controller->render('typeadd',array(
                        'model'=>$model,
                        'group'=>$groupArr
                    ));
                    exit;
                }

                if ($_GET['type'] == 'up') {
                    $id = trim($_GET['id']);
                    $model = $model->findByPk($id);
                    //获取投诉任务组
                    $groupArr = array();
                    $group = CustomerComplainGroup::model()->getAllGroup();
                    foreach ($group as $k=>$v) {
                        $groupArr[$v['id']] = $v['name'];
                    }
                    $this->controller->render('typeadd',array(
                        'model'=>$model,
                        'group'=>$groupArr
                    ));
                    exit;
                }
                if($_GET['type'] == 'del') {
                    $id = trim($_GET['id']);
                    $model = $model->findByPk($id);
                    $model->status=2;
                    $model->save();
                    $this->controller->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array (
                        'typelist'));
                }
            }
        }

        $data= CustomerComplainType::model()->getComplainTypeList();
        $dataProvider=new CArrayDataProvider($data, array(
            'id'=>'id',
            'sort'=>array(),
            'pagination'=>array(
                'pageSize'=>100,
            ),
        ));

        $this->controller->render('typelist',array(
            'model'=>$dataProvider,
        ));
    }
}