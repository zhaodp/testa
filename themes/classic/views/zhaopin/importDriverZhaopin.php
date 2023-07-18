<?php if(!$batch){
		echo "<h2>导入成功!</h2>";
	}else{
	echo "<h2>当前批次号：".$batch."</h2>";
}?>
<hr/>
<form class="span12" enctype="multipart/form-data" action="<?php echo Yii::app()->createUrl('/zhaopin/importdriverzhaopin');?>" method="POST">
	
		<input name="batch" type="hidden" value = "<?php echo $batch;?>" />
		<div class="span3">
			 <label for="文件选择">文件选择：</label>
			 <input name="csv_goods" type="file" />
		</div>	
		<div class="span3">
			<?php echo CHtml::submitButton('导入',array('class'=>'btn btn-success','name'=>'import')); ?>
		</div>
	
</form>