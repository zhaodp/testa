<?php
/**
 * 各个版本的api文档页面
 */

switch ($ver) {
	case 1 :
		break;
	case 2 :
		$this->render('/docs/ver2');
		break;
	case 3 :
		include_once 'ver3.php';
		break;
}
