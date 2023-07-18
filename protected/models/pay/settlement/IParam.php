<?php
/**
 * 提供根据参数结账需要的接口
 *
 * User: tuan
 * Date: 14/11/29
 * Time: 13:41
 */
abstract class IParam {
	/** 是否是 vip */
	abstract public function isVip();

	/** 用户流水表里面的 type */
	abstract public function getUserType();
	/** 用户流水表里面的 source */
	abstract public function getUserSource();
	/** 用户流水表里面的 comment */
	abstract public function getUserComment();

	/**  司机流水表里面的 type */
	abstract public function getDriverType();
	/**  司机流水表里面的 channel */
	abstract public function getDriverChannel();
	/**  司机流水表里面的 comment */
	abstract public function getDriverComment();
	/** 该参数期望的值 */
	abstract public function getExcepted();
}