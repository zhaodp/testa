<div class="span12 alert alert-success>">
<p><a href='<?php echo Yii::app()->createUrl('/notice/view',array('id'=>$data->id));?>'><?php echo $data->title; ?></a></p>
<p><?php echo date('Y-m-d',$data->created); ?></p>
</div>


