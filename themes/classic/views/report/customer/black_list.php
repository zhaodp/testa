<?php
$city = Dict::items('city');
$this->pageTitle = '黑名单列表';
echo "<h1>".$this->pageTitle."</h1><br />";

echo "<div class='search-form'>";
echo '<div class="span12">';
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));
echo '电话号：';
echo "<input type='text' name='phone' max='20'>&nbsp;&nbsp;";
echo CHtml::submitButton('Search');
$this->endWidget();

echo '</div>';
echo '</div>';
echo CHtml::Button('新建黑名单用户',array('class'=>'btn btn-success','id'=>'add'));
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'sms-grid',
    'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
		 array(
			'name'=>'电话号',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["phone"]'),
		array(
			'name'=>'操作员',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => 'AdminUserNew::model()->getName($data["user_id"])'),
		array(
			'name'=>'创建时间',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["created"]'),
     ),
));
Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('sms-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<script>
$(function(){
    //新建发送短信
	$("#add").click(function(){
		window.location.href="<?php echo Yii::app()->createUrl('report/newblack'); ?>";
	});

	
});
</script>