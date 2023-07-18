<?php

class RightFilter extends CFilter {
	public $roles;
	
	protected function preFilter($filterChain) {
		// 动作被执行之前应用的逻辑
		print_r($filterChain);
		print_r($this->roles);die();
		
		return true; // 如果动作不应被执行，此处返回 false
	}
	
	protected function postFilter($filterChain) {
		// 动作执行之后应用的逻辑
	}
}

