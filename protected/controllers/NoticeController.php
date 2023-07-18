<?php
/**
 * 
 * @TODO e代驾 公告列表，公告管理，公告发布（发布页面选择分类），公告内容用html编辑器
 * @author dayuer
 *
 */
class NoticeController extends Controller {
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = '//layouts/column1';
	public $defaultAction = 'index';
	
	
	public function actions()
	{
		return array(
			'myhome'=>'application.controllers.admin.group.MyHomeAction', 
			
		);
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id) {
		
		if (Yii::app()->request->isAjaxRequest) {
			$this->layout = false; 
			echo $this->render('view', array (
				'model'=>Notice::model()->getContent($id),
				false));
			Yii::app()->end();
		}
		
		$this->render('view', array (
			'model'=>Notice::model()->getContent($id)
		));
	}
	
	/**
	 * 
	 * 检查是否有最新的消息
	 */
	public function actionCheck() {
		$this->layout='//layouts/main_no_nav';
		$this->format='json';
		$this->render('check');
	}
	
	/**
	 * Updates a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionRead($id) {
		if (Yii::app()->request->isAjaxRequest) {
			$params = array();
			$notice = $this->loadModel($id);
			$driver = Driver::getProfile(Yii::app()->user->getId());
			$params['driver_id'] = $driver ? $driver->user : (Yii::app()->user ? Yii::app()->user->name : '');
			$params['notice_id'] = $notice->id;
			NoticeRead::model()->noticeReadSave($params);
		}
	}
	
	/**
	 * 
	 * 获取是否有最新公告
	 */
	public function actionNewest() {
		if (Yii::app()->request->isAjaxRequest) {
			$params = array();
			$driver = Driver::getProfile(Yii::app()->user->getId());
			if($driver){
				$params['driver_id'] = $driver->user;
				$params['city_id'] = $driver->city_id;
			}else{
				if(Yii::app()->user){
					$params['driver_id'] = Yii::app()->user->name;
					$params['city_id'] = Yii::app()->user->city;
				}
			}

			$notice = Notice::model()->getNewest($params);
			if(!empty($notice)){
				echo $notice;
			}

		}
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate() {
		$model = new Notice();
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if (isset($_POST['Notice'])) {
			//先判断登录的人是分公司的还是总公司
			$city = Yii::app()->user->city;
			if ($city == 0){
				$post_city = isset($_POST['city']) ? $_POST['city'] : '';
				if(!empty($post_city)){
					$count = count($post_city);
					$city_id = '';
					if ($count == 7 || $count == 0){
						$city_id = '0';
					}else{
						if(!empty($post_city)){
							$city = $_POST['city'];
							foreach ($city as $items){
								$city_id .= $items.',';
							}
						}
					}
				}else{
					$city_id = '0';
				}
			}else {
				$city_id = $city;
			}

			if(isset($_POST['valid'])){
				$time = '+'.$_POST['valid'].' day';
				$_POST['Notice']['top_period'] = date('Y-m-d H:i:s',strtotime($time));
			}
			
			if(!isset($_POST['deadline'])){
				$_POST['Notice']['deadline'] = date('Y-m-d H:i:s',time()+3600*24*7);
			}
			
			$_POST['Notice']['city_id'] = rtrim($city_id,',');
			
			$model->attributes = $_POST['Notice'];

			if ($model->save())
				$this->redirect(array (
					'view', 
					'id'=>$model->id));
		}
		
		$this->render('create', array (
			'model'=>$model));
	}
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id) {
		$model = $this->loadModel($id);
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if (isset($_POST['Notice'])) {
			$city = isset($_POST['city']) ? $_POST['city'] : '';
			$count = count($city);
			$city_id = '';
			if ($count == 7 || $count == 0){
				$city_id = '0';
			}else {
				if(!empty($_POST['city'])){
					$city = $_POST['city'];
					foreach ($city as $items){
						$city_id .= $items.',';
					}
				}
			}
			$_POST['Notice']['city_id'] = rtrim($city_id,',');
			if(isset($_POST['valid'])){
				$time = '+'.$_POST['valid'].' day';
				$_POST['Notice']['top_period'] = date('Y-m-d H:i:s',strtotime($time));
			}

			$model->attributes = $_POST['Notice'];

			if ($model->update())
				$this->redirect(array (
					'admin'));
		}
		
		$this->render('update', array (
			'model'=>$model));
	}
	
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id) {
		if (Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();
			
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if (!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array (
					'admin'));
		} else
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex() {
		$params = array();
		$params['category'] = isset($_GET['category']) ? $_GET['category'] : 0;
		$driver = Driver::getProfile(Yii::app()->user->getId());	
		
		$type = DailyDriverOrderReport::TYPE_DAILY;
		
		if (isset($_GET['Notice']) && $params['category'] == 0){
			$params['title'] = isset($_GET['Notice']['title']) ? $_GET['Notice']['title'] : '';
			$params['city_id'] = isset($_GET['Notice']['city_id']) ? $_GET['Notice']['city_id'] : 0;
			$params['class'] = isset($_GET['Notice']['class']) ? $_GET['Notice']['class'] : 0;
			
			$notice = new Notice();
			$dataProvider = $notice->getNoticeList($params);
			
			//是否显示城市搜索选项标识
			$is_city = 0;
			$this->render('index',array(
				'dataProvider'=>$dataProvider,
				'params'=>$params,
				'type' => $type,
				'model'=>$notice,
				'is_city'=>$is_city,
			));
		}else {
			
			$params['city_id'] = 0;
			if($driver){
				$params['city_id'] = $driver->city_id;
			}
			//notice 列表
			$notice = new Notice();
			$dataProvider = $notice->getNoticeList($params);
			//排行榜
			if($params['city_id']>0 && $params['category'] == 0){
				if(isset($_GET['DailyDriverOrderReport'])){
					//获取类型    current_day别名
					$type = $_GET['DailyDriverOrderReport']['current_day'];
				}
				$model = new DailyDriverOrderReport();
				//获取条件和回应的数据
				$data = $model->getDriverRankData($params['city_id'],$type);
				$is_city = 1;
				$this->render('index',array(
					'dataProvider'=>$dataProvider,
					'params'=>$params,
					'dataDailyOrderRank'=>$data['dataDailyOrderRank'],
					'driverRankCount'=>$data['driverRankCount'],
					'type' => $type,
					'model'=>$notice,
					'is_city'=>$is_city,
				));
			}else {
				$is_city = 0;
				$this->render('index',array(
					'dataProvider'=>$dataProvider,
					'params'=>$params,
					'type' => $type,
					'model'=>$notice,
					'is_city'=>$is_city,
				));
			}
		}
		
	}
	/**
	 * 后台人员的排行榜
	 * Enter description here ...
	 */
	public function actionRanking() {
		$city_id = 1;
		$type = DailyDriverOrderReport::TYPE_DAILY;	//0日排行，1月排行
		$model=new DailyDriverOrderReport();
		if(isset($_GET['DailyDriverOrderReport'])){
			$city_id = $_GET['DailyDriverOrderReport']['city_id'];
			$type = $_GET['DailyDriverOrderReport']['current_day'];
		}
		
		//获取条件和回应的数据
		$data = $model->getDriverRankData($city_id, $type);
		
		$this->render('ranking',array(
			'model'=>$model,
			'dataProvider'=>$data['dataDailyOrderRank'],
			'driverRankCount' =>$data['driverRankCount'],
			'type'=>$type
		));
	}
	
	
	/**
	 * ajax 获取排行统计
	 * Enter description here ...
	 * @param unknown_type $city_id
	 * @param unknown_type $type
	 */
	public function actionDriverRankCountAjax(){
		$dailyDriverOrderReport = new DailyDriverOrderReport();
		if (Yii::app()->request->isAjaxRequest) {
			$type = (isset($_GET['type'])) ? $_GET['type'] : 0;
			$city_id = (isset($_GET['city_id'])) ? $_GET['city_id'] : 0;
			if($type == 0)
				echo $dailyDriverOrderReport->getDriverDailyRankCount($city_id);
			else
				echo $dailyDriverOrderReport->getDriverMonthlyRankCount($city_id);
		}
	}
	/**
	 * 日排行列表详单
	 * Enter description here ...
	 */
	public function actionrank(){
		$this->layout = '//layouts/main_no_nav';
		$city_id = $_GET['city_id'] ? $_GET['city_id'] : 1;
		$type = $_GET['type'] ? $_GET['type'] : DailyDriverOrderReport::TYPE_DAILY;	//0日排行，1月排行
		$model=new DailyDriverOrderReport();
		//获取条件和回应的数据
		switch ($type){
			case 0:
				$data = $model->getDriverRankData($city_id, $type);
				break;
			case 1:
				$data = $model->getDriverRankData($city_id, $type, 50);
				break;
		}
		$this->render('rank', array (
			'dataProvider'=>$data['dataDailyOrderRank'],
			'type'=>$type));
	}
	
	/**
	 * Manages all models.
	 */
	public function actionAdmin() {
		$model = new Notice();
		$params = array();
		if(isset($_GET['Notice'])){
			$params['class'] = isset($_GET['Notice']['class']) ? $_GET['Notice']['class'] : 0;
			$params['city_id'] = isset($_GET['Notice']['city_id']) ? $_GET['Notice']['city_id'] : 0;
			$params['title'] = isset($_GET['Notice']['title']) ? $_GET['Notice']['title'] : '';
			$params['is_valid'] = isset($_GET['Notice']['is_valid']) ? $_GET['Notice']['is_valid'] : 0;
			$params['created'] = isset($_GET['Notice']['created']) ? $_GET['Notice']['created'] : '';
			
		}

		$dataProvider = $model->getNoticeLists($params);
		
		$this->render('admin', array (
			'model'=>$model,
			'dataProvider'=>$dataProvider,
		));
	}
	
	public function actionimgupload(){
		$url=array();
		$url['flash_dir']="notice/flash";
		$url['img_dir']="notice/img";
		$url['get_type'] = $_GET['type'];
		$url['CKEditorFuncNum'] = $_GET['CKEditorFuncNum'];
		$url['upload'] = $_FILES['upload'];
		$this->imgupload($url);
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionMaterialView($id)
	{
		$this->render('material_view',array(
			'model'=>$this->loadMaterialModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionMaterialCreate()
	{
		$model=new DriverMaterial();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DriverMaterial']))
		{
			$_POST['DriverMaterial']['created'] = time();
			$model->attributes=$_POST['DriverMaterial'];
			if($model->save())
				$this->redirect(array('materialAdmin'));
		}

		$this->render('material_create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionMaterialUpdate($id)
	{
		$model=$this->loadMaterialModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['DriverMaterial']))
		{
			$model->attributes=$_POST['DriverMaterial'];
			if($model->save())
				$this->redirect(array('materialAdmin'));
		}

		$this->render('material_update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionMaterialDelete($id)
	{
		$this->loadMaterialModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionMaterialIndex()
	{
		$dataProvider=new CActiveDataProvider('DriverMaterial');
		$this->render('material_index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionMaterialAdmin()
	{
		$model=new DriverMaterial('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['DriverMaterial']))
			$model->attributes=$_GET['DriverMaterial'];

		$this->render('material_admin',array(
			'model'=>$model,
		));
	}
	
	public function loadMaterialModel($id)
	{
		$model=DriverMaterial::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	/**
	 * config 上传图片配置文件
	 * Enter description here ...
	 * @param unknown_type $url
	 */
	public function imgupload($url){
		$config=array();
		$config['type']=array("flash","img"); //上传允许type值
		$config['img']=array("jpg","bmp","gif","png");
		$config['flash']=array("flv","swf");
		$config['flash_size']=2000;
		$config['img_size']=2000;
		$config['message']="上传成功";
		$config['name']=mktime();
		$config['flash_dir']=$url['flash_dir'];
		$config['img_dir']=$url['img_dir'];
		$config['site_url']=SP_URL_HOME;
		$config['get_type'] = $url['get_type'];
		$config['CKEditorFuncNum'] = $url['CKEditorFuncNum'];
		$config['upload'] = $url['upload'];
		$this->uploadfile($config);
	}
	
	public function uploadfile($config)
	{
		//判断是否是非法调用
		if(empty($config['CKEditorFuncNum']))
		   $this->mkhtml(1,"","错误的功能调用请求");
		$fn=$config['CKEditorFuncNum'];
		if(!in_array($config['get_type'],$config['type']))
		   $this->mkhtml(1,"","错误的文件调用请求");
		$type = $config['get_type'];
		if(is_uploaded_file($config['upload']['tmp_name']))
		{
		   //判断上传文件是否允许
		   $filearr=pathinfo($config['upload']['name']);
		   $filetype=strtolower($filearr["extension"]);
		   if(!in_array($filetype,$config[$type]))
		   		$this->mkhtml($fn,"","错误的文件类型！");
		   //判断文件大小是否符合要求
		   if($config['upload']['size']>$config[$type."_size"]*1024)
				$this->mkhtml($fn,"","上传的文件不能超过".$config[$type."_size"]."KB！");
			$filename = IMAGE_ASSETS.$config[$type."_dir"];
			if(!file_exists($filename))
				mkdir($filename,0777,true);
		   $file_abso="images/".$config[$type."_dir"]."/".$config['name'].".".$filetype;
		   $file_host=IMAGE_ASSETS.$config[$type."_dir"]."/".$config['name'].".".$filetype;
		   if(move_uploaded_file($config['upload']['tmp_name'],$file_host))
		   {
				$this->mkhtml($fn,$config['site_url'].$file_abso,$config['message']);
		   }
		   else
		   {
				$this->mkhtml($fn,"","文件上传失败，请检查上传目录设置和目录读写权限");
		   }
		}
	}
	public function mkhtml($fn,$fileurl,$message)
	{
		$str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$fileurl.'\', \''.$message.'\');</script>';
		exit($str);
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id) {
		$model = Notice::model()->findByPk($id);
		if ($model===null)
			throw new CHttpException(404, 'The requested page does not exist.');
		return $model;
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model) {
		if (isset($_POST['ajax'])&&$_POST['ajax']==='notice-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * 获取状态
	 */
	public function getNoticeStatus($status){
		$statusHtml = '';
		switch ($status){
			case 1: 
				$statusHtml = '培训';
				break;
			case 2:
				$statusHtml = '制度';
				break;
			case 3:
				$statusHtml = '奖惩';
				break;
			case 4:
				$statusHtml = '通知';
				break;
		}
		return $statusHtml;
	}
	
	/**
	 * 
	 * 获取城市名称
	 */
	public function getCityName($city_id){
		$city = explode(',', $city_id);
		$city_name = '';
		foreach ($city as $items){
			$city_name .= Dict::item('city', $items).',';
		}
		return rtrim($city_name,',');
	}
}
