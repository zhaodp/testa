<?php
class AdminAppController extends Controller{
	public $layout = '//layouts/column2';
	//默认进入
	public function actionIndex(){
		$model = new ApiKey;
		$this->render("index", array("test"=>"just do it", "model"=>$model));
	}
	//查询展示
	public function actionShow(){

		$apikey = $_GET["ApiKey"];
		$description = $apikey["description"];
		$this->render("show", array("description"=>$description));
	}

	//新加一个
	public function actionAdd(){
	


		if(isset($_POST["description"])) {
			//todo check the input;
			$enable = isset($_POST["enable"]) ? $_POST["enable"] : 0;

			$app = ApiKey::model()->addNewApp($enable,$_POST["description"]);
			$this->render("result", array("model"=>$app));
		}

		$model = new ApiKey;
		$this->render("add", array("model"=>$model));	
	}

	// 保存
	public function actionSave(){
		$apikey = $_GET["ApiKey"];
		$enable = $apikey["enable"];
		$description = $apikey["description"]; 
		$app = ApiKey::model()->addNewApp($enable,$description);
			$this->render("result", array("model"=>$app));
	}

	public function actionUpdate(){

	}
	public function actionDisable(){
		$appkey = $_GET["appkey"];
		$result = ApiKey::model()->enable($appkey, 0);
		if(1 == $result){
			$this->render("success");
		}else{
			$this->render("error");
		}
	}
	public function actionEnable(){
		$appkey = $_GET["appkey"];
		$result = ApiKey::model()->enable($appkey, 1);
		if(1 == $result){
			$this->render("success");
		}else{
			$this->render("error");
		}

	}


}
