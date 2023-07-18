<?php
class TestMultipleProcessCommand extends MultipleProcessCommand
{
	public $count = 0;
	public $avgProcessCount = 0; 

	public function init() {
		parent::init();	//记得要调用父类init方法
		$this->setMaxProcessNum(10); //设置最大进程数
	}

	//主进程初始化，如果需要共享数据，请使用公共变量 ，可以不需要设置max
	protected function beforeIndex($max=2) {
		$this->setMaxProcessNum($max);
		$sql = "SELECT COUNT(id) FROM t_address_pool";
		//总条数
		$this->count = Yii::app()->db_readonly->createCommand($sql)->queryScalar();
		//使用分页方式，切分得到每个进程需要处理的条数
		$this->avgProcessCount = ceil($this->count/$this->_maxProcesses);
	}

	//子线程处理
	//参数max 为最大进程数，如果actionIndex有参数，那么beforeIndex和afterIndex都需要加相同的参数，
	public function actionIndex($max=2) {
		//看看当前启动了几个子进程
		$currentProcessNum = $this->getCurrentProcessNum();
		$offset = ($currentProcessNum) * $this->avgProcessCount; //获得偏移量
		$sql = "select id from t_address_pool limit $offset, ". $this->avgProcessCount;

		//由于Yii::app()->db如果创建了实例，将不会创建，
		//所有这里需要为每个子进程创建一个db实例连接
		$db_readonly = $this->getNewDbInstance(Yii::app()->db_readonly);
		
		$datas = $db_readonly->createCommand($sql)->queryAll();
		foreach($datas as $data) {
			$this->output($data['id']);
			//todo somethings

			//暂定一时，不然速度太快，echo数量不准
			usleep(1);
		}
		
		//下面是需要模型的处理
		/*
		$db = $this->getNewDbInstance(Yii::app()->db);
		Module11Sections::$db = $db;
		$models = Module11Sections::model()->findAll();
		foreach($models as $model) {
			$this->output($data['sectionId']);
			//tod somethings

			usleep(1);
		}
		*/
	}

	//主进程结束前，收尾处理，如果需要共享数据，请使用公共变量
	public function afterIndex($max=2) {
		//echo "end\n";
	}

}