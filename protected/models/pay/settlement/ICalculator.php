<?php
/**
 * 定义费用计算器的基本接口,实现该接口的类,支持根据参数返回计算结果
 *
 * 它的实现类有@see // TODO ...
 *
 * User: tuan
 * Date: 14/11/29
 * Time: 11:09
 */
abstract class ICalculator {
	/** 校验器 */
	abstract function validator();
	/** 计算结果 */
	abstract function calculator();
	/** 返回能够返回该计算器的字符串 */
	abstract function toString();
	/** 是否符合预期 */
	function isExcepted(){}
} 