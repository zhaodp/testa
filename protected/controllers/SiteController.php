<?php

Yii::import("application.components.Lucifer");
Yii::import("application.components.AppVersionUtil");
class SiteController extends Controller {

	/**
     * Declares class-based actions.
     */
	public function actions() {
		return array(
				// captcha action renders the CAPTCHA image displayed on the contact page
				'captcha'=>array(
						//'class'=>'CCaptchaAction',
						'class'=>'Lucifer',
						'backColor'=>0xFFFFFF,
						'maxLength'=>'4', // 最多生成几个字符
						'minLength'=>'4', // 最少生成几个字符
						'height'=>'48',
						'width'=>'100',
						'padding'=>'5',
						'offset'=>'8'
				),
		);
	}

	public function actionIndex() {

		if (isset(Yii::app()->user->type ) && Yii::app()->user->type == 1 ) {
			$url=Yii::app()->createUrl('/notice/index', array(
					'category'=>0
			));
		} else {
			$url=Yii::app()->createUrl('/account/summary');
		}
		
		Yii::app()->request->redirect($url, true);
	}

	public function actionHome() {
		$params=Yii::app()->params['appVersion'];
		$type=0;
		if(isset($_GET['type'])) {
	            $type=$_GET['type'];
		}
		$appInfo=AppVersionUtil::getAndroidAppInfo($type);

        $city_fee = RCityList::model()->loadFeeGroupbyFid();

        $city_open = RCityList::model()->getOpenCityList();

//print_r($city_fee);die;
		$this->layout=false;

        $this->render('index', array(
                'params'=>$params,
                'appInfo'=>$appInfo,
                'type'=>$type,
                'city_fee'=>$city_fee,
        ));

	}

	public function actionabout() {
		if(Yii::app()->theme->name == 'mobile'){
			$this->redirect('http://wap.edaijia.cn/about', true, 302);
		}
		
		$this->layout=false;
		$this->render('about');
	}

	public function actionzhaopin() {
		$this->redirect('http://zhaopin.' . Common::getDomain(SP_HOST) .'/', true, 301);
		return;
	}

	public function actionhezuo() {
		if(Yii::app()->theme->name == 'mobile'){
			$this->redirect('http://wap.edaijia.cn/hezuo', true, 302);
		}
		$this->layout=false;
		$this->render('hezuo');
	}

	public function actionfaq() {
		if(Yii::app()->theme->name == 'mobile'){
			$this->redirect('http://wap.edaijia.cn/faq', true, 302);
		}
        $city_fee = RCityList::model()->loadFeeGroupbyFid();

        $city_open = RCityList::model()->getOpenCityList();
        $city_open_str = implode('、',$city_open);
		$this->layout=false;
		$this->render('faq',array('city_open'=>$city_open_str,'fee_arr' => (is_array($city_fee ) ? $city_fee : array())));
	}

	/**
     * 司机端faq
     * @author sunhongjing 2013-08-02
     */
	public function actiondriverfaq() {
		$this->layout=false;
		$this->render('driverfaq');
	}

	public function actionLink() {
		if(Yii::app()->theme->name == 'mobile'){
			$this->redirect('http://wap.edaijia.cn/link', true, 302);
		}
		
		$this->layout=false;
		$this->render('link');
	}

	public function actionAd() {
		if(Yii::app()->theme->name == 'mobile'){
			$this->redirect('http://wap.edaijia.cn/ad', true, 302);
		}
		
		$this->layout=false;
		$this->render("ad_sh");
	}

	public function actionError() {
		//$this->layout = '//layouts/blank';
		$error=Yii::app()->errorHandler->error;
		if ($error) {
			EdjLog::info("error is ".print_r($error,true));
			unset($error['traces']);
			$error['created']=date(Yii::app()->params['formatDateTime'], time());
			if ($error['code']!=404) {
				@Yii::app()->dbstat->createCommand()->insert('t_api_error_log', $error);
			}
			//echo json_encode(array('message', $error['code']));
			

			if (Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
     * Displays the contact page
     */
	public function actionContact() {
		$model=new ContactForm();
		if (isset($_POST['ContactForm'])) {
			$model->attributes=$_POST['ContactForm'];
			if ($model->validate()) {
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'], $model->subject, $model->body, $headers);
				Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact', array(
				'model'=>$model
		));
	}

	/**
     * Displays the login page
     */
	public function actionLogin() {
		$model=new LoginForm();

        if (isset($_POST['type'])&&$_POST['type']=='admin') {
			$model=new LoginForm('admin');
		}
		
		if (isset($_POST['ajax'])&&$_POST['ajax']==='login-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if (isset($_POST['LoginForm'])) {
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if ($model->validate()&&$model->login()) {

			  	// Delete the key for kick in cache
			  	$kick_key = 'ECENTER_KICK_KEY_'.$model->username;
			  	Yii::app()->cache->delete($kick_key);
				//跳转到首页之后再跳转到公告页面
				$url = Yii::app()->request->hostInfo;
				$user_type = Yii::app()->user->type;
				if ($user_type === AdminUserNew::USER_TYPE_ADMIN) {
					//如果是首次登录，跳转到密码修改
					if (isset(Yii::app()->user->first_login)
                        && ((Yii::app()->user->first_login == AdminUserNew::FIRST_LOGIN_TRUE)
                            || (Yii::app()->user->first_login == AdminUserNew::FIRST_LOGIN_AUTH))) {
						$this->redirect(array(
								'profile/changepasswd'
						));
					}
				}
				
				$this->callBackUrl(true);

				Yii::app()->request->redirect($url, true);
			}
		}
		$this->callBackUrl();
		$this->layout='//layouts/blank';
		// display the login form
		$this->render('login', array(
				'model'=>$model
		));
	}

	/**
	 * callbackurl 机制
	 */
	public function callBackUrl($login = false){
		$name = 'backurl';
		if($login){
			$cookie=Yii::app()->request->cookies[$name];
			if($cookie){
				$value=urldecode($cookie->value);  
				//删除Cookie  
				$cookie = Yii::app()->request->getCookies();  
				unset($cookie[$name]); 
				$this->setSsid();
				header("Location:$value");
				exit;
			}
		}else{
			$value = empty($_GET[$name])?'':$_GET[$name];
			if(!empty($value)){
				$cookie=new CHttpCookie($name,$value);  
				Yii::app()->request->cookies[$name]=$cookie;  
			}else{
				$cookie = Yii::app()->request->getCookies();  
				unset($cookie[$name]); 
			}
		}
	}

	/**
     * Logs out the current user and redirect to homepage.
     */
	public function actionLogout() {
		Yii::app()->user->logout(true);
		//退出之后跳转到v2的退出页面
		$appv2 = AdminApp::model()->findByPk(2);
		$url = $appv2->url . '/index.php?r=site/logout';
		Yii::app()->request->redirect($url, true);

		$this->redirect(Yii::app()->homeUrl);
	}

    public function actiongetSmsCode($username,$password){


        $login_mod = new LoginForm();
        $res = $login_mod->getSmsCode($username,$password);

        $this->responseAjax($res['code'],$res['message'],$res['data']);
    }


    public function actiongetRdCode($username,$password,$smscode){
        $res = AdminUserNew::model()->bindGoogleAuth($username,$password,$smscode);
        $this->responseAjax($res['code'],$res['message'],$res['data']);
    }

	public function actionCallback() {
		$this->layout='//layouts/blank';
		$this->render('callback', array(
				'app'=>$_GET['app']
		));
	}

	public function actionDay() {
	}

	/**
     *
     * 批量把数据按月统计信息
     */
	public function actionSource() {
		$connection=Yii::app()->dbstat;
		$time=$_GET['ctime'];
		$str=""; //记录macaddress
		$status=""; //记录status
		$date=strtotime($time);
		$dateDay=date('t', $date);
		$dateYM=date('Y-m-', $date);
		$table_date=date('Ym', $date);
		for($i=1; $i<=$dateDay; $i++) {
			if ($i<10) {
				$startTime=$dateYM.$i;
				$endTime=$dateYM.'0'.($i+1);
			} else {
				$startTime=$dateYM.$i;
				$endTime=$dateYM.($i+1);
			}
			$sql="SELECT COUNT( * )as count , macaddress, method, created, source
	FROM t_api_log_201210
	WHERE macaddress !=  '' and 
	created between '".$startTime."' and '".$endTime."'
	GROUP BY macaddress, method";
			$command=$connection->createCommand($sql);
			$source=$command->queryAll();
			if (!empty($source)) {
				foreach($source as $source) {
					if ($str!=$source['macaddress']) {
						$str=$source['macaddress'];
						$status=$this->isselect($source, $startTime);
					}
					$sql='INSERT INTO t_api_log_count_'.$table_date.'(method, macaddress, source, status, count_day, created) VALUES(:method, :macaddress, :source, :status, :count_day, :created)';
					$command=$connection->createCommand($sql);
					$command->bindParam(":method", $source['method']);
					$command->bindParam(":macaddress", $source['macaddress']);
					$command->bindParam(":source", $source['source']);
					$command->bindParam(":status", $status);
					$command->bindParam(":count_day", $source['count']);
					$command->bindParam(":created", $source['created']);
					$command->execute();
					$command->reset();
					echo $source['method']."\n";
				}
			}
		}
	}

	public function isselect($data, $endTime) {
		$connection=Yii::app()->dbstat;
		$sql="SELECT count(1) as count FROM t_customer_macaddress where macaddress = '".$data['macaddress']."' and created < '".$endTime."'";
		$command=$connection->createCommand($sql);
		$isnot=$command->queryRow();
		
		if ($isnot["count"]>0) {
			return 1;
		} else {
			//			$sql = 'INSERT INTO t_customer_macaddress(macaddress, source, created) VALUES(:macaddress,  :source, :created)';
						//			$command_m = $connection->createCommand($sql);
						//			$command_m->bindParam(":source", $data['source']);
						//			$command_m->bindParam(":macaddress", $data['macaddress']);
						//			$command_m->bindParam(":created", $data['created']);
						//			$command_m->execute();
						//			$command_m->reset();
			return 0;
		}
	}

	public function actionDottedLine($date) {
		$connection=Yii::app()->db;
		$date=strtotime($date);
		$dateDay=date('t', $date);
		$dateYM=date('Y-m-', $date);
		$table_date=date('Ym', $date);
		for($i=1; $i<$dateDay; $i++) {
			if ($i<10) {
				$startTime=$dateYM.$i;
				$endTime=$dateYM.'0'.($i+1);
			} else {
				$startTime=$dateYM.$i;
				$endTime=$dateYM.($i+1);
			}
			//老用户
			$sql="SELECT id FROM `t_user_access` where status = 1 and
	created between '".$startTime."' and '".$endTime."' group by macaddress";
			$command=$connection->createCommand($sql);
			$repeat_active=$command->query()->count();
			//新用户
			$sql="SELECT id FROM `t_user_access` where status = 0 and
	created between '".$startTime."' and '".$endTime."' group by macaddress";
			$command=$connection->createCommand($sql);
			$fresh_actives=$command->query()->count();
			//活跃用户
			$actives=$repeat_active+$fresh_actives;
			
			if (isset($repeat_active)&&isset($fresh_actives)) {
				$insert_sql="INSERT INTO t_dotted_line(repeat_active, fresh_actives, actives,created) VALUES($repeat_active,$fresh_actives,$actives,$startTime)";
				$command=$connection->createCommand($insert_sql)->execute();
			}
		}
	}

	public function actionCheckedName() {
		$name=$_GET['name'];
		$back=0;
		if (!empty($name)) {
			$user=Yii::app()->db_readonly->createCommand()->select("*")->from("t_admin_user")->where("name = :name", array(
					':name'=>$name
			))->queryRow();
			if (!empty($user)) {
				$back=1;
			}
		}
		echo $back;
	}

	/**
     * 官网vip申请
     * by 曾志海
     * 2013-07-29
     */
	public function actionVip() {
		$this->layout=false;
		$model=new VipApply();
		$this->performAjaxValidation($model);
		
		if (isset($_POST['VipApply'])) {
			$model->attributes=$_POST['VipApply'];
			$model->type=$_POST['type'];
			$model->create_time=date("Y-m-d H:i:s", time());
			$this->filterData($model);
			if ($model->save()) {
				echo "<meta charset='utf-8'/>";
				Yii::app()->clientScript->registerScript('alert', 'alert("恭喜您，申请成功！");');
				Yii::app()->clientScript->registerScript('close', '
	                    if (navigator.userAgent.indexOf("Firefox") > 0){
	                        window.location.href = "about:blank";
	                    }else{
	                        window.opener=null;window.open("","_self");window.close();
	                    }
	                ');
				$this->renderText('');
				Yii::app()->end();
			}
		}
		$this->render('vip', array(
				'model'=>$model
		));
	}

	/**
     * 官网婚庆代驾申请
     * by 曾志海
     * 2013-07-29
     */
	public function actionWedding() {
		$this->layout=false;
		$model=new WeddingApply();
		$this->performAjaxValidation($model);
		if (isset($_POST['WeddingApply'])) {
			$model->attributes=$_POST['WeddingApply'];
			$model->wedding_type=$_POST['wedding_type'];
			$model->create_time=date("Y-m-d H:i:s", time());
			$this->filterData($model);
			if ($model->save()) {
				echo "<meta charset='utf-8'/>";
				Yii::app()->clientScript->registerScript('alert', 'alert("恭喜您，申请成功！");');
				Yii::app()->clientScript->registerScript('close', '
	                    if (navigator.userAgent.indexOf("Firefox") > 0){
	                        window.location.href = "about:blank";
	                    }else{
	                        window.opener=null;window.open("","_self");window.close();
	                    }
	                ');
				$this->renderText('');
				Yii::app()->end();
			}
		}
		$this->render('wedding', array(
				'model'=>$model
		));
	}

	/**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
	protected function performAjaxValidation($model) {
		if (isset($_POST['ajax'])&&($_POST['ajax']==='wedding-apply-form'||$_POST['ajax']==='apply-vip-form')) {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	protected function filterData($model) {
		//过滤
		if ($model) {
			foreach($model as $k=>$v) {
				$model[$k]=strip_tags($v);
			}
		}
	}

	/**
     * 显示长文章
     * @author 曾志海  2013-09-04
     */
	public function actionNoticepost() {
		if (isset($_GET['id'])) {
			$id=intval($_GET['id']);
			$postNews=NewNoticePost::model()->findByPk($id);
			$this->renderPartial('noticepost', array(
					'model'=>$postNews
			));
		}
	}
}
