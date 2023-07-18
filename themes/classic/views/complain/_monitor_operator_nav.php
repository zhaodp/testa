<?php
$type = Yii::app()->request->getQuery('type');
$type = empty($type)?'stat_operator':$type;
$currentFun = $this->action->id.'_'.$type;
?>
<ul class="nav nav-pills">
    <li <?php if($currentFun=='monitoroperator_stat_operator') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/monitoroperator&type=stat_operator'); ?>">投诉处理时效报表(投诉任务人)</a></li>
    <li <?php if($currentFun=='monitoroperator_process_operator') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/monitoroperator&type=process_operator'); ?>">投诉处理情况(投诉任务人)</a></li>
</ul>