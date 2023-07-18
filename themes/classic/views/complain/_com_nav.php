<?php $currentFun=$this->id.'/'.$this->action->id; ?>
<ul class="nav nav-pills">
    <li <?php if($currentFun=='complain/list') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/list'); ?>">客户投诉管理</a></li>
    <li <?php if($currentFun=='driverComplaint/admin') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('driverComplaint/admin'); ?>">司机投诉管理</a></li>
    <li <?php if($currentFun=='complain/recoup') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/recoup'); ?>">投诉补扣款</a></li>
    <li <?php if($currentFun=='complain/monitor') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/monitor'); ?>">客户投诉监控</a></li>
    <li <?php if($currentFun=='complain/monitoroperator') echo 'class="active"'; ?> ><a href="<?php echo Yii::app()->createUrl('complain/monitoroperator'); ?>">客户投诉监控（任务人）</a></li>
</ul>
