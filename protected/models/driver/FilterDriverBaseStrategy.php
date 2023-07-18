<?php

abstract class FilterDriverBaseStrategy  {

	public static  $filter_count = 0;   
 
	public function filter($city_id, $drivers,$lng, $lat,$range, $type, $order_id,$driver_app_ver=nul) {
	}


	public static function arraySortByKey(array $array, $key) {
		$asc=true;
		$result=array();
		// 整理出准备排序的数组
		foreach($array as $k=>&$v) {
			$values[$k]=isset($v[$key]) ? $v[$key] : '';
		}
		unset($v);
		// 对需要排序键值进行排序
		$asc ? asort($values) : arsort($values);
		// 重新排列原有数组
		$i=0;
		foreach($values as $k=>$v) {
			$i++;
			$result[$array[$k]['id']]=$array[$k];
		}

		return $result;
	}
}
