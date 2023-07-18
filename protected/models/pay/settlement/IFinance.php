<?php
/**
 * 定义财务类的基础callback接口,需要进行其他操作的可以放到一个callback里面实现
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/10/28
 * Time: 16:08
 */

abstract class IFinance {
	abstract function callback();
	abstract function getName();
} 