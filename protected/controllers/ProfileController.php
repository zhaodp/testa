<?php

class ProfileController extends Controller {
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column1';
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionInfo($id = '') {
		if (Yii::app()->request->isAjaxRequest) {
			$employee = Driver::getProfile($id);
			$driverStatus = DriverPosition::model()->getDriverStatus($employee->user, $employee->id);
			switch ($driverStatus){
				case DriverPosition::POSITION_IDLE:
					$statusLabel = '空闲';
					break;
				case DriverPosition::POSITION_WORK:
					$statusLabel = '服务中';
					break;
				case DriverPosition::POSITION_GETOFF:
					$statusLabel = '下班';
					break;
				default:
					$statusLabel = '下班';
					break;
			} 
			$this->layout = false;
			echo $this->render('info_view', array (
				'employee'=>$employee, 
				'statusLable'=>$statusLabel,
				false));
			Yii::app()->end();
		}
		$employee = Driver::getProfile(Yii::app()->user->getId());
		$driverStatus = DriverPosition::model()->getDriverStatus($employee->user, $employee->id);
		
		switch ($driverStatus){
			case DriverPosition::POSITION_IDLE:
				$statusLabel = '空闲';
				break;
			case DriverPosition::POSITION_WORK:
				$statusLabel = '服务中';
				break;
			case DriverPosition::POSITION_GETOFF:
				$statusLabel = '下班';
				break;
			default:
				$statusLabel = '下班';
				break;
		} 
		$this->render('info', array (
			'employee'=>$employee,
			'statusLable'=>$statusLabel));
	}
	
	public function actionChangePasswd() {
		//if (in_array(AdminGroup::model()->getID('drivers'), Yii::app()->user->roles)) {
		if (Yii::app()->user->type == AdminUserNew::USER_TYPE_DRIVER) {
			$user = Driver::getProfile(Yii::app()->user->getId());
		} else {
			$user = AdminUserNew::model()->find('name=:name', array (
				':name'=>Yii::app()->user->getId()));
		}
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);		
		

		if (isset($_POST['Employee'])) {
			if (trim($_POST['Employee']['old_password'])=="") {
				$user->addError('changepasswd', "请填写旧密码。");
			}
			if (trim($_POST['Employee']['password'])=="") {
				$errorContent = "请填写新密码。";
				$user->addError('changepasswd', "请填写新密码。");
			} else {
                $pLevel = AdminUserNew::model()->pwdLevel(trim($_POST['Employee']['password']));
                if($pLevel < 2){
                    $user->addError ('changepasswd','密码太弱，请更换');
                }
            }
			if (trim($_POST['Employee']['re_password'])=="") {
				$user->addError('changepasswd', "请再次填写新密码。");
			}
			
			if (trim($_POST['Employee']['re_password'])!=trim($_POST['Employee']['password'])) {
				$user->addError('changepasswd', "两次新密码填写不同，请再次填写新密码。");
			}
			if (get_class($user)=='Driver') {
				if (trim($user->password)!=trim($_POST['Employee']['old_password'])) {
					$user->addError('changepasswd', "请正确填写旧密码。");
				}
			} else {
				if (trim($user->password)!=md5(trim($_POST['Employee']['old_password']))) {
					$user->addError('changepasswd', "请正确填写旧密码。");
				}
			}
			if (!$user->hasErrors('changepasswd')) {
				if (get_class($user)=='Driver') {
					
					$attr = array (
						'password'=>$_POST['Employee']['password']);
					$user->attributes = $attr;
					if ($user->save()) {
						Yii::app()->clientScript->registerScript('alert','alert("密码成功修改。");');
					}
				} else {
                    if($user->first_login == AdminUserNew::FIRST_LOGIN_TRUE || $user->first_login == AdminUserNew::FIRST_LOGIN_AUTH ){
                        $user_first_login = $user->first_login + 1;
                    }else{
                        $user_first_login = $user->first_login;
                    }
                    $res = $user->updateByPk($user->id, array (
                        'password'=>md5($_POST['Employee']['password']),
                        'first_login'=>$user_first_login));
					if ($res) {
						Yii::app()->clientScript->registerScript('alert','alert("密码成功修改。");');
						if ($user->first_login == AdminUserNew::FIRST_LOGIN_TRUE || $user->first_login == AdminUserNew::FIRST_LOGIN_AUTH ) {
							//如果是第一次登录，修改密码后强制退出重新登录
							$this->redirect(array (
								'site/logout'));
						}
					}
				}
			}
		}
		$this->render('changepasswd', array (
			'model'=>$user));
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model) {
		if (isset($_POST['ajax'])&&$_POST['ajax']==='passwd-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
