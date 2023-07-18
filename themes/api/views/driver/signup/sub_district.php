<?php
$parent_id = $this->getParam('parent_id');
$data = ChinaDistrictData::getSubDistricts($parent_id);
$result = array();
foreach($data as $id => $name){
    $result[] = array('label' => $name, 'value' => $id);
}
$this->outputJson($result);
