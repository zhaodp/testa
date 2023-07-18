<?php
/**
 * Description of MasterSlaveBehavior
 *
 * @author syang
 */
class MasterSlaveBehavior extends CActiveRecordBehavior {
	
	/**
	 * @var mixed master db hander name list array/string;
	 */
	public $masterDbHandlerName = array('db');
	
	/**
	 * @var mixed slave db hander name list array/string;
	 */
	public $slaveDbHandlerName = array('db_readonly');
	
	public function events()
	{
		return array_merge(parent::events(), array(
			'onAfterCount'=>'afterCount',
		));
	}
	
	public function beforeSave($event)
	{
		//insert and update used masterDbHandlerName
		$this->switchDb($this->masterDbHandlerName);

	}
	
	public function afterSave($event)
	{
		$this->switchDb($this->masterDbHandlerName);
	}
	
	public function beforeDelete($event)
	{
		//delete used masterDbHandlerName
		$this->switchDb($this->masterDbHandlerName);
	}
	
	public function afterDelete($event)
	{
		//delete used masterDbHandlerName
		$this->switchDb($this->masterDbHandlerName);
	}
	
	public function beforeFind($event)
	{
		//select used slave
		$this->switchDb($this->slaveDbHandlerName);
	}

	public function afterFind($event)
	{
		$this->switchDb($this->masterDbHandlerName);
	}
	
	public function beforeCount($event)
	{
		$this->switchDb($this->slaveDbHandlerName);
	}

    public function afterCount($event)
    {
		$this->switchDb($this->masterDbHandlerName);
    }
	
	/**
	 * switch db hander
	 */
	private function switchDb($dbHandler='db') {
		if ($this->getOwner() instanceof CActiveRecord) {
			$modelName = get_class($this->getOwner());
			$dbHandler = is_string($dbHandler) ? array($dbHandler) : $dbHandler;
			try {
				if ( count($dbHandler) == 1 ) {
					$db = $dbHandler[0];
				}
				else if ( count($dbHandler) > 1 ) {
					shuffle($dbHandler);
					$db = array_rand($dbHandler);
				}
				$modelName::$db = Yii::app()->{$db};
			
			}
			catch(Exception $e) {
				throw new CException($e->getMessage());
			}
		}
	}
}

?>
