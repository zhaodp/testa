<?php

?>

<h1>司机评价</h1>

<?php
$this->widget('zii.widgets.CListView', array (
	'dataProvider'=>$dataProvider, 
	'itemView'=>'_mobile', 
	'template'=>"{summary}\n{items}\n{pager}", 
	'pager'=>array (
		'class'=>'CLinkPager', 
		'header'=>'',
		'maxButtonCount'=>5,
		'firstPageLabel'=>'第一页', 
		'prevPageLabel'=>'上页', 
		'nextPageLabel'=>'下页', 
		'lastPageLabel'=>'末页'
	)
));
?>
