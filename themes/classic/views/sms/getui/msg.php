<?php
$city = Dict::items('city');
$this->pageTitle = '推送消息列表';
echo "<h1>".$this->pageTitle."</h1><br />";
echo "<div class='search-form'>";
echo '<div class="span12">';
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));
echo '发送状态：';
echo "<select id='sms_status' name='status' style='width:80px;'>";
echo "<option value=''>全部</option>";
echo "<option value='0'>未发送</option>";
echo "<option value='1'>已发送</option>";
echo "</select>&nbsp;&nbsp;";
echo CHtml::submitButton('Search');
$this->endWidget();

echo '</div>';
echo '</div>';
echo CHtml::Button('新建发送消息',array('class'=>'btn btn-success','id'=>'add'));

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    'options'=>array(
        'title'=>'推送内容',
        'autoOpen'=>false,
		'width'=>'400',
		'height'=>'300',
		'modal'=>true,
		'buttons'=>array(
        	'Close'=>'js:function(){$("#mydialog").dialog("close");}'
		),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'sms-grid',
    'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
		 array(
			'name'=>'司机工号',
			'headerHtmlOptions'=>array(
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["driver_id"]'),
		array(
			'name'=>'类型',
			'headerHtmlOptions'=>array(
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => array($this , 'getMsgType')),
		array(
			'name'=>'级别',
			'headerHtmlOptions'=>array(
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => array($this , 'getMsgLevel')),
		array(
			'name'=>'内容',
			'headerHtmlOptions'=>array(
				'width'=>'300px',
				'nowrap'=>'nowrap'
			),
			'type' => 'raw',
			'value' => 'CHtml::link(Helper::cut_str($data["content"] , 25), "javascript:void(0);", array (
			"onclick"=>"{view(\"$data[content]\")}"));'),
		array(
			'name'=>'预定发送时间',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["pre_send_time"]'),
		array(
			'name'=>'操作员',
			'headerHtmlOptions'=>array(
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => 'AdminUserNew::model()->getName($data["user_id"])'),
		array(
			'name'=>'发送状态',
			'headerHtmlOptions'=>array(
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["status"] ? "发送" : "未发送"'),
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
		window.location.href="<?php echo Yii::app()->createUrl('sms/addgetuimsg'); ?>";
	});

	
});
function view(content) {
	$("#dialogdiv").html(content);
	$("#mydialog").dialog("open");
}
</script>