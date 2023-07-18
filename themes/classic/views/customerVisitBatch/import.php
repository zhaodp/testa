<?php if(!$batch){
		echo "<h2>导入成功!</h2>";
	}else{
	echo "<h2>当前批次号：".$batch."</h2>";
}?>
<hr/>
<form class="span12" enctype="multipart/form-data" action="<?php echo Yii::app()->createUrl('/customerVisitBatch/import');?>" method="POST">
	
		<input id='batch_id' name="batch_id" type="hidden" value = "<?php echo $batch_id;?>" />
		<div class="span3">
			 <label for="file">文件选择：</label>
			 <input id='file' name="file" type="file" />
			 <label>注:导入内容格式是名字+手机号码</label>
		</div>	
		<div class="span3">
			<?php echo CHtml::submitButton('导入',array('class'=>'btn btn-success','name'=>'import')); ?>
		</div>
</form>