<?php
/**
 * ajax修改副卡信息
 * Enter description here ...
 * @author zengzhihai
 *
 */
class SubAjaxAction extends CAction
{
	/**
	 *  副卡信息增、删、改ajax接口
	 */
	public function run(){
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
		try {
		switch ($action) {
			case 'update':
				if (empty($_POST['id'])){
					$sql = "SELECT id,vip_card FROM {{customer_main}} WHERE phone=:phone AND name=:name";
					$params[':phone']=$_POST['phone'];
                    $params[':name']=$_POST['name'];
                    $command = Yii::app ()->db_readonly->createCommand ($sql)->bindValues($params);
					$Card_id = $command->queryRow();
                    if($Card_id){
                        if (!empty($Card_id['vip_card'])){
                            $result['status'] = false;
                            $result['msg'] = '操作失败,此人已有vip卡或已有副卡';
                            break;
                        }
                        $model = CustomerMain::model()->findByPk($Card_id['id']);
                    }else{
                        $result['status'] = false;
                        $result['msg'] = '操作失败,没有找到对应的数据';
                        break;
                    }
				    $model = CustomerMain::model()->findByPk($Card_id['id']);
				}else {
					$model = CustomerMain::model()->findByPk($_POST['id']);
				}
				$model->name = $_POST['name'];
				$model->phone = $_POST['phone'];
				$model->vip_card = $_POST['vip_card'];
				$model->status = $_POST['status'];
				$state = $model->save();
				$result['status'] = $state ? true : false;
				$result['msg'] = $state ? '操作成功' : ($model->errors?'此人未选择城市，无法添加副卡':'操作失败');
				break;
			
			case 'delete':
				$id = intval($_POST['id']);
				if (!$id) {
					throw new Excetpion('缺少参数');
				}
				$model = CustomerMain::model()->findByPk($id);
				$model->vip_card='';
				$result['status'] = $model->save() ? true : false;
				$result['msg'] = $result['status'] ? '删除成功' : '删除失败';
				break;
			default : 
				throw new Exception('action invisible');
		}
		} catch (Exception $e) {
			$result['sataus'] = false;
			$result['msg'] = $e->getMessage();
		}
		echo json_encode($result);
		exit;
	}


}