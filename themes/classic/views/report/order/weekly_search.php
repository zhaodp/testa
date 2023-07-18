<?php
$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get'
));?>

<div class='search-form'>
	<div class="span12">
	<?php
	$data_source = OrderStat::$data_source;
	if (is_array($data_source) && count($data_source)) {
		echo CHtml::dropDownList('data_source', isset($condition['data_source'])?$condition['data_source']:1, $data_source,array('class' => "span11"));
	}
	?>
	
	<?php 
	Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
	
	echo CHtml::label('开始日期：','order_start_time');
	$this->widget('CJuiDateTimePicker', array (
	    'id' => 'order_start_time',
		'name'=>'start_time', 
		'value'=>$condition['start_time'], 
		'mode'=>'date',
		'options'=>array (
		    'width' => '60',
			'dateFormat'=>'yy-mm-dd'
		),
		'htmlOptions'=>array(
	         'class'=>'span11'
	     ),
		'language'=>'zh'
	));
	echo CHtml::label('结束日期：','order_end_time');
	$this->widget('CJuiDateTimePicker', array (
		'name'=>'end_time', 
		'value'=>$condition['end_time'], 
		'mode'=>'date',
		'options'=>array (
			'dateFormat'=>'yy-mm-dd'
		), 
		'htmlOptions'=>array(
	         'class'=>'span11'
	     ),
		'language'=>'zh'
	));
	
	echo CHtml::label('城市：','city_id');
	echo CHtml::dropDownList('city_id', isset($condition['city_id'])?$condition['city_id']:1,Common::getOpenCity(),array('class' => "span11"));
	
	echo CHtml::label('时间段：','time_part');
	$time_part=array(
			''=>'全部',
			'7'=>'7-22点',
			'22'=>'22-23点',
			'23'=>'23-24点',
			'24'=>'24-7点'
	);
	echo CHtml::dropDownList('time_part', isset($condition['time_part'])?$condition['time_part']:'', $time_part,array('class' => "span11"));
	
	echo CHtml::label('渠道：','source');
	echo CHtml::dropDownList('source', '', array(''=>'全部'),array('class' => "span11"));
	
	echo CHtml::submitButton('搜索',array('class'=>'btn btn-success'));
	?>
    <br><br>
    <?php echo CHtml::Button('下载当前数据到excel',array('class'=>'btn btn-success','id'=>'down_excel_btn')); ?>
	</div>
</div>

<?php $this->endWidget(); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $("#down_excel_btn").click(function(){
           var data_source = $("#data_source").val();
            var start_time = $("#order_start_time").val();
            var end_time = $("#end_time").val();
            var city_id = $("#city_id").val();
            var time_part = $("#time_part").val();
            var source = $("#source").val();
            var download_execl = 1;
            //新页面打开开始下载
            url = '<?php echo Yii::app()->createUrl('/report/weekly')?>&data_source='+data_source+'&start_time='+start_time
            +'&end_time='+end_time+'&city_id='+city_id+'&time_part='+time_part+'&source='+source+'&download_execl='+download_execl;
            window.open(url);
        });
    });

</script>
