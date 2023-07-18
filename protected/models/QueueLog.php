<?php
/**
 * 低优先级任务处理队列
 */

class QueueLog extends HttpQueue
{
	public function __construct()
	{
		$queue = Yii::app()->params['httpsqs']['task_low'];
		parent::init($queue);
	}
	
	public static function model($className = __CLASS__)
	{
		$model = null;
		if (isset(self::$_models[$className]))
			$model = self::$_models[$className];
		else
		{
			$model = self::$_models[$className] = new $className(null);
		}
		return $model;
	}
	
}