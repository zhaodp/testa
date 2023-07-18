<?php
class ClearSchemaController extends LoggerExtController {

	public function beforeAction($action){
		return parent::beforeAction($action);
	}

	public function actions() {
		return array(
		);
	}

	public function actionDbadmin() {
		/*
		$schema = Yii::app()->dbadmin->schema;
		$tables = $schema->getTables();
		if (isset($tables)) {
			foreach ($tables as $table) {
			    $schema->getTable($table->name,true);
			}
		}
		 */
		$name = "{{admin_action}}";
		$key='yii:dbschema'.Yii::app()->dbadmin->connectionString.':'.Yii::app()->dbadmin->username.':'.$name;
		$value = Yii::app()->cache->delete($key);
		var_dump($value);

	}
}
