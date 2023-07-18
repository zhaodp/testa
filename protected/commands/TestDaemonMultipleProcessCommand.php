<?php
class TestDaemonMultipleProcessCommand extends MultipleProcessCommand
{
	//主进程初始化，如果需要共享数据，请使用公共变量
	protected function beforeIndex() {
		$this->setMaxProcessNum(10); //设置最大进程数
		$this->setDaemon(true); //守护模式

		//为了降低cpu使用率，可以调整主进程的休息时间。默认是10，会调用usleep
		$this->setMainUsleepTime(1000);

		//信号检测频率 默认是1
		$this->setSignalUsleepTime(1);

		//fork子进程频率 默认是1
		$this->setForkUsleepTime(1);

	}

	//子线程处理
	//参数max 为最大进程数，如果actionIndex有参数，那么beforeIndex和afterIndex都需要加相同的参数，
	public function actionIndex() {
		$rand = rand(1, 10);
		$this->output("sleep(". $rand .")");
		sleep($rand);
	}

	//因为是守护模式，所以不需要收尾处理，主进程基本上不会主动退出
	/*public function afterIndex() {
	}
	*/

}