<?php

class MobileController extends Controller {
	public $layout = '//layouts/column_mobile';
	
//	public function filters() {
//		return array (
//			'accessControl' // perform access control for CRUD operations
//		);
//	}
	
	public function accessRules() {
		return array (
			array (
				'allow',  // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array (
					'index',
					'view', 
					'update', 
					'cancel',
					'demo'
				), 
				'roles'=>array (
					'*'
				)
			), 
			array (
				'allow',  // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array (
					'view'
				), 
				'roles'=>array (
					'drivers', 
					'admins'
				)
			), 
			array (
				'allow',  // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array (
					'index', 
					'admin'
				), 
				'roles'=>array (
					'admins'
				)
			), 
			array (
				'deny',  // deny all users
				'users'=>array (
					'*'
				)
			)
		);
	}	
	
	public function actionIndex() {
		$this->render('index');
	}

	public function actionDemo() {
		$this->render('demo');
	}
	
}