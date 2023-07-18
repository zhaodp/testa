<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-zhaopin-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>通知司机培训</h1>

<div class="search-form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-admin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error',
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get'
)); ?>
	<div class="row-fluid">
		<div class="span3">
		<?php
			echo CHtml::label('批次','');
		?>
		<input type="text" id="DriverZhaopin_batch" name="DriverZhaopin[batch]" value='<?php echo $_GET['batch']?>' disabled = 'true' />	
		</div>	
		<div class="span3">
		<?php
			$src = Dict::items('recruitment_src');
			
			ksort($src);
			echo CHtml::label('来源渠道选择','src'); 
			echo $form->dropDownList($model,'src', $src);
		?>		
		</div>
		<div class="span3">
		<?php
			echo CHtml::label('数量','');
		?>
		<input type="text" id="DriverZhaopin_num" name="DriverZhaopin[num]" value='50' />	
		</div>		
	</div>
	<div class="row-fluid">
		<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
	</div>
</div>
<?php $this->endWidget(); ?>
<div class="row-fluid">
	<a href="#myModal" role="button" class="btn" data-toggle="modal">通知培训</a>
</div>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">短信信息</h3>
	</div>
	<div class="modal-body">
		<textarea rows="3" cols="80" class="span12" id="sms_content"></textarea>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
		<?php echo CHtml::button('发送',array('class'=>'btn','onclick'=>'sns_send()','id' => 'send','data-loading-text'=>'发送中...')); ?>
	</div>
</div>
<?php 

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'报名信息',
        'autoOpen'=>false,
		'width'=>'800',
		'height'=>'600',
		'modal'=>true,
		'buttons'=>array(
        	'Close'=>'js:function(){$("#mydialog").dialog("close");}'
		),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-zhaopin-grid',
	'dataProvider'=>$data,
	'itemsCssClass'=>'table table-striped',
	'pager'=>false,
	'template'=>'{items}',
	'columns'=>array(
		array(
			'name' =>CHtml::checkBox("batchall",false,array('onclick'=>'checkedall()')),
			'type' => 'raw',
			'value' =>'CHtml::checkBox("batch",false,array("value"=>"$data->id"))',
		),
		array(
			'name'=>'报名流水号',
			'type' => 'raw',
			'value'=>'$data->id',
        ),
		array (
			'name' => 'name',
			'type' => 'raw',
			'value' => '$data->name'
		),
		'mobile',
        'id_card',
		array(
			'name'=>'居住城市',
			'value'=>'Yii::app()->controller->getRecruitmentCity($data->city_id)',
        ),
		array(
			'name'=>'居住地区',
			'value'=>'Yii::app()->controller->getRecruitmentDistrict($data->district_id)',
		),
		array(
			'name'=>'担保',
			'value'=>'$data->assure > 0 ? "是"  :"否"',
		),
		array(
			'name'=>'报名时间',
			'value'=>'($data->apply_time > 0) ?date("Y.m.d",$data->apply_time) : ""',
        ),
		array(
			'name'=>'通知培训',
			'type'=>'raw',		
			'value'=>'($data->inform_time == 0)? CHtml::link("通知培训", "javascript:void(0);", array (
			"onclick"=>"{changestatus($data->id, 2);}")) : date("Y-m-d", $data->inform_time);',		
        ), 
        array(
			'name'=>'考试',
			'value'=>'$data->exam == 0 ? "未通过"  :"已通过"',
        ), 
		array(
			'name'=>'已培训考核',
			'type'=>'raw',
			'value'=>'($data->inform_time > 0 && $data->cultivate_time == 0)? CHtml::link("已培训考核", "javascript:void(0);", array (
			"onclick"=>"{changestatus($data->id, 3);}")) : ($data->cultivate_time == 0?"" : date("Y-m-d", $data->cultivate_time));',	
        ),
		array(
			'name'=>'签约',
			'type'=>'raw',		
			'value'=>'($data->cultivate_time > 0 && $data->entrant_time == 0)? CHtml::link("签约", array("driver/create", "Driver[name]"=>$data->name, "Driver[domicile]"=>$data->domicile, "Driver[address]"=>$data->address, "Driver[id_card]"=>$data->id_card, "Driver[car_card]"=>$data->driver_card, "Driver[ext_phone]"=>$data->mobile, "Driver[license_date]"=> date("Y-m-d", $data->driver_year))) : ($data->entrant_time == 0?"" : date("Y-m-d", $data->entrant_time));',
        ), 
	),
)); 
?>

<script>
function checkedall(){
	var arrChk=$("input[name='batchall']").attr("checked");
	if(arrChk){
		$("input[name='batch']").attr("checked",true);
	}else{
		$("input[name='batch']").attr("checked",false);
	}
}

function import_date(){
	var arrID = new Array();
    var arrChk=$("input[name='batch']:checked");
    for (var i=0;i<arrChk.length;i++)
    {
    	arrID.push(arrChk[i].value);
    }
    var url = '<?php echo Yii::app()->createUrl('/recruitment/importdriverzhaopin',array('batch'=>$_GET['batch']));?>';
	url += '&id='+arrID;
	window.location.href=url;
}

function sns_send(){
	var arrID = new Array();
    var arrChk=$("input[name='batch']:checked");
    var sms_content = $('#sms_content').val();
    if(sms_content !=''){
	    for (var i=0;i<arrChk.length;i++)
	    {
	    	arrID.push(arrChk[i].value);
	    }
	    if(arrID.length > 0){
		    $.ajax({
				'url':'<?php echo Yii::app()->createUrl('/recruitment/sendSMS');?>',
				'data':'batch='+<?php echo $_GET['batch'];?>+'&id='+arrID+'&sms_content='+sms_content,
				'type':'get',
				'beforeSend':function(){
					$("#send").button('loading');
				},
				'success':function(data){
					$("#send").button("reset");
					if(data==1){
						alert("发送成功。");
						window.location.href="<?php echo Yii::app()->createUrl('/recruitment/driverbatchadmin');?>";
					}
					else
						alert("操作失败！");
				},
				'cache':false		
			});
	    }else{
	    	alert("选择要通知的司机！");
	    }
    }else{
    	alert("请输入信息内容！");
    }
}

</script>
