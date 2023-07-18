<?php

class AdminuserController extends Controller {
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column2';

	/**
	 *
	 * 客服坐席一览
	 */
	public function actionAgent() {
		$model = new AdminAgent();
		$model->unsetAttributes(); // clear any default values
		if (isset($_GET['AdminAgent']))
			$model->attributes = $_GET['AdminAgent'];

		$this->render('agent', array (
			'model'=>$model));

	}

	/**
	 * 客服坐席分配设定
	 * @param int $agent_num
	 */
	public function actionAgentAllot($agent_num) {
		$model = AdminAgent::model()->findByPk($agent_num);

		if (isset($_POST['AdminAgent'])) {
			unset($model->attributes);
			$model->attributes = $_POST['AdminAgent'];

			//清除以前的选择
			$sql = 'update t_admin_agent set user_id =0 where user_id =:user_id;';
			Yii::app()->db->createCommand($sql)->execute(array (
				'user_id'=>$model->user_id));

			if ($model->save()) {
				if (!empty($_GET['asDialog'])) {
					//Close the dialog, reset the iframe and update the grid
					echo CHtml::script("window.parent.$('#cru-dialog').dialog('close');window.parent.$('#cru-frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['gridId']}');");
					Yii::app()->end();
				}
			}
		}
		$this->layout = '//layouts/main_no_nav';
		$this->render('agent_allot', array (
			'model'=>$model));
	}

    public function actioncheckIsAdmin($username){
        $res = AdminUserNew::model()->checkIsSupperAdmin($username) ;
        $this->responseAjax($res['code'],$res['message'],$res['data']);
    }

	/**
	 * 异步获取用户列表
	 * @param int $agent_num
	 */
        public function actionGetUserList($mid=0)
        {
            $list = AdminUserNew::model()->getAgentUsers($mid);
            foreach($list as $key => $val) {
                echo CHtml::tag('option', array('value'=>$key),CHtml::encode($val),true);
            }
        }

// 	protected function showUserRoles($data) {
// 		return CHtml::dropDownList('roles', '', $data->getRoles());
// 	}


}
