<?php
    $type = Yii::app()->request->getQuery('type');
    $type = empty($type)?'stat_type':$type;
    $currentFun = $this->action->id.'_'.$type;
?>
<ul class="nav nav-pills">
    <li <?php if($currentFun=='monitor_stat_type') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/monitor&type=stat_type'); ?>">投诉处理时效报表(投诉分类)</a></li>
    <!-- <li <?php if($currentFun=='monitor_stat_operator') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/monitor&type=stat_operator'); ?>">投诉处理时效报表(投诉任务人)</a></li> -->
    <li <?php if($currentFun=='monitor_process_type') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/monitor&type=process_type'); ?>">投诉处理情况(投诉分类)</a></li>
    <!-- <li <?php if($currentFun=='monitor_process_operator') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/monitor&type=process_operator'); ?>">投诉处理情况(投诉任务人)</a></li> -->
</ul>
