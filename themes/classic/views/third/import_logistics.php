<h1><?php echo $title?></h1>
<hr/>
<form class="span12" enctype="multipart/form-data" action="<?php echo Yii::app()->createUrl('/third/importLogRecord');?>" method="POST">
    <div class="span3">
        <label for="文件选择">请选择要导入的订单信息excel文件</label>
        <input name="import_data" type="file" />
        <input name="type" type="hidden" value="<?php echo $type ?>" />

    </div>
    <div class="span3">
        <?php echo CHtml::submitButton('导入数据',array('class'=>'btn btn-success','name'=>'import','data-loading-text'=>'正在导入中...')); ?>
    </div>
</form>

