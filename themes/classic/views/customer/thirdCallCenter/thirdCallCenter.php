<?php
$this->renderPartial('thirdCallCenter/_uploadFile',
	array(
		'model' 		=> $model,
		'sourceList'	=> $sourceList,
	)
);
?>
<div class="row-fluid">
	<b>
		<?php echo $summary ?>;
	</b>

</div>

<?php
$this->renderPartial('thirdCallCenter/_tableView',
	array(
		'dataProvider' => $dataProvider,
	)
);
?>


