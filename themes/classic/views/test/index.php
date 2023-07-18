<?php
$group = AdminGroup::model()->getGroups();

foreach($group as $item) {
	if (isset($item['children'])) {
		echo '<legend>'.CHtml::label($item['name'], null, array (
			'style'=>'display:inline')).'</legend>';
		
		foreach($item['children'] as $child) {
			echo '<label class="checkbox" >';
			echo CHtml::checkBox('AdminUser[roles][]', true, array (
				'id'=>'AdminUser_roles_' . $child['id'], 
				'value'=>$child['code'], 
				'name'=>$child['name'], 
				'separator'=>''));
			echo $child['name'] .'</label>';
		}
	}
}
