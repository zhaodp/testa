<h3><?php echo $model->title;?></h3>
<div class="span12 alert alert-info">
    <p><?php echo $model->content;?></p>
    <div class="well"><?php echo CHtml::encode(date('Y-m-d H:i', $model->created));?></div>
</div>
