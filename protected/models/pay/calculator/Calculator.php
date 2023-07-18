<?php
/**
 *
 *
 * User: tuan
 * Date: 14/11/29
 * Time: 12:57
 */
Yii::import('application.models.pay.settlement.*');
class Calculator extends ICalculator{

	/** @var  计算结果 */
	protected  $result;
	/** @var  预期值 */
	protected  $excepted = null;

	/**
	 * @param \预期值 $excepted
	 */
	public function setExcepted($excepted)
	{
		$this->excepted = $excepted;
	}

	/**
	 * @return \预期值
	 */
	public function getExcepted()
	{
		return $this->excepted;
	}

	/**
	 * @param \计算结果 $result
	 */
	public function setResult($result)
	{
		$this->result = $result;
	}

	/**
	 * @return \计算结果
	 */
	public function getResult()
	{
		return $this->result;
	}

	/** 校验器 */
	function validator()
	{
		return false;
	}

	/** 计算结果 */
	function calculator()
	{
		$status = $this->validator();
		if($status){
			$this->result = $this->getFee();
		}else{
			$this->result = 0;
		}
		if($this->isSetExcepted()){
			return $this->excepted;
		}else{
			return $this->result;
		}
	}

	/**
	 * 是否设置了预期的值
	 *
	 * @return bool
	 */
	protected  function isSetExcepted(){
			return !is_null($this->excepted);
	}

	/** 返回能够返回该计算器的字符串 */
	public  function toString()
	{
		// TODO: Implement toString() method.
		$format = '|result|%s|excepted|%s';
		$excepted = 'not set';
		if($this->isSetExcepted()){
			$excepted = $this->excepted;
		}
		return sprintf($format, $excepted, $this->result);
	}


	/**
	 * 是否符合预期
	 *
	 * @return bool|void
	 */
	public function isExcepted()
	{
		return ($this->excepted == $this->result);
	}


	protected  function getFee(){
		return 0;
	}

} 