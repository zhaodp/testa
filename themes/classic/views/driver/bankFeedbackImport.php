<h2>导入银行返回信息</h2>
<hr/>
<form class="span12" enctype="multipart/form-data" action="<?php echo Yii::app()->createUrl('/driver/bankFeedbackImport');?>" method="POST">
		<div class="span3">
			 <label for="文件选择">文件选择(仅限制上传.CSV格式文件)：</label>
			 <input name="csv_driverbank" type="file" />
		</div>	
		<div class="span3">
			<?php echo CHtml::submitButton('导入数据',array('class'=>'btn btn-success','name'=>'import','data-loading-text'=>'正在导入中...')); ?>
		</div>
</form>