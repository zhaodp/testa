<li class="nav-header">公司公告</li>
<li <?php if($route=='notice/index' && @$params['category']==0) echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/notice/index',array('category'=>0));?>"><i class="icon-home"></i>近期公告</a></li>
<li <?php if($route=='notice/index' && @$params['category']==1) echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/notice/index',array('category'=>1));?>"><i class="icon-book"></i>培训教程</a></li>
<!--<li class="nav-header">运营数据</li>-->
<!--<li <?php if($route=='report/online') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/online');?>"><i class="icon-random"></i>司机在线</a></li>-->
<li class="nav-header">订单管理</li>
<li <?php if($route=='order/driver') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/order/driver');?>"><i class="icon-list"></i>报单</a></li>
<li <?php if($route=='order/create') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/order/create');?>"><i class="icon-cog"></i>补单</a></li>
<li <?php if($route=='invoice/index') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/invoice/index');?>"><i class="icon-envelope"></i>发票</a></li>
<li <?php if($route=='account/driverhistory') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/account/driverhistory');?>"><i class="icon-file"></i>对账单</a></li>
<li class="nav-header">优惠卡</li>
<li <?php if($route=='driver/bonus') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/bonus');?>"><i class="icon-list"></i>发卡收入明细</a></li>
<li <?php if($route=='bonusType/driverbonusall') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/driverbonusall');?>"><i class="icon-list"></i>司机联盟-发卡赚钱排行榜</a></li>
<li class="nav-header">客户关系</li>
<!-- <li <?php //if($route=='comments/index') echo 'class="active"'?>><a href="<?php //echo Yii::app()->createUrl('/comments/index');?>"><i class="icon-pencil"></i>客户评价</a></li> -->
<li <?php if($route=='commentSms/index') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/commentSms/index');?>"><i class="icon-pencil"></i>客户评价</a></li>
<li <?php if($route=='client/callpostion') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/client/callpostion');?>"><i class="icon-headphones"></i>客户分布</a></li>
<?php
$road_exam = new DriverRoadExam();
$is_examiner = $road_exam->checkDriverIsExaminer(Yii::app()->user->id);
?>
<?php if ($is_examiner) {?>
<li class="nav-header">路考后台</li>
<li <?php if($route=='recruitment/road') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('recruitment/road');?>"><i class="icon-book"></i>路考后台</a></li>
<?php } ?>

<li class="divider"></li>
<li <?php if($route=='profile/info') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/profile/info');?>"><i class="icon-user"></i>个人信息 (<?php echo Yii::app()->user->id;?>)</a></li>
<li <?php if($route=='profile/changepasswd') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/profile/changepasswd');?>"><i class="icon-wrench"></i>修改密码</a></li>

<li class="divider"></li>
<li><a href="<?php echo Yii::app()->createUrl('/site/logout');?>"><i class="icon-off"></i>退出</a></li>
