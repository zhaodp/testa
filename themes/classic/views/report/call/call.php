<?php
$this->pageTitle = '客服接单统计';

Yii::app()->clientScript->registerScript('search', "
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('call-grid', {
		data: $(this).serialize()
	});
    getCallTotal();
	return false;
});
");
?>
<h1>呼叫中心统计</h1>
<div class="search-form" >
	<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'htmlOptions'=>array('class'=>'form-inline'),
)); ?>
	<div class="controls controls-row">
		<div class="span2">
		<?php echo $form->label($model,'客服名字'); ?>
		<?php echo $form->textField($model,'name', array('class'=>"span12", 'id' => 'name')); ?>
		</div>
		
		<div class="span2">
		<?php echo $form->label($model,'开始时间'); //开始时间 用report_time 代替 ?>
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'CallPhoneReport[report_time]', 
				'model'=>$model,  //Model object
				'value'=>'',
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh',
				'htmlOptions'=>	array('class'=>"span12", 'id' => 'start_time')
			));
		?>
    	</div>
    	
    	<div class="span2">
		<?php echo $form->label($model,'结束时间'); //结束时间  用created代替?>
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'CallPhoneReport[created]', 
				'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh',
				'htmlOptions'=>	array('class'=>"span12", 'id' => 'end_time')
			));
		?>
    	</div>
    	
    	<div class="span2">
            <br />
    		<?php echo CHtml::submitButton('搜索',array('class'=>'btn')); ?>
    	</div>
	</div>

    <div>默认当前时间往前推一天</div>

<?php $this->endWidget(); ?>
</div>

<h4 id="call_total"></h4>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'call-grid',
    'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
		 array(
			'name'=>'客服名字',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data->name'),
		 array(
			'name'=>'接电话',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data->call_count'),
		array(
			'name'=>'订单',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data->order_count'),
		array(
			'name'=>'派单',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data->dispatch_count'),
		 array(
			'name'=>'统计时间',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data->report_time'),
     ),
));
?>

<script type="text/javascript">
    function getCallTotal(){
        var name = $("#name").val();
        var start_time = $("#start_time").val();
        var end_time = $("#end_time").val();

        $.ajax({
            'url':'<?php
			echo Yii::app()->createUrl('/report/callTotal');
			?>',
            'data':'name='+name + '&start_time=' + start_time + '&end_time=' + end_time,
            'type':'get',
            'dataType':'json',
            'success':function(data){
                var str = '';
                if(data.call_count !== null){
                    str = data.name + "共接" + data.call_count + "电话，其中有" + data.order_count + "个电话成订单；派了" +
                                data.dispatch_count + "个单.";
                }else{
                    str = "你的搜索条件内没有记录";
                }
                $("#call_total").html(str);
            },
            'cache':false
        });

    }
</script>