<div class="well" style="padding: 19px 2px;">
	<ul class="nav nav-list">
		<li <?php if($route=='zhaopin/index') echo 'class="active"'?>><a href="/"><i class="icon-headphones"></i>首页</a></li>
		<li class="nav-header">司机招聘</li>
		<li <?php if($route=='zhaopin/baoming') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/baoming');?>"><i class="icon-headphones"></i>在线报名</a></li>
		<li <?php if($route=='zhaopin/tongzhi') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/tongzhi');?>"><i class="icon-headphones"></i>培训通知</a></li>
		<li <?php if($route=='api/docs') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/docs/?cat=docs&ver=3');?>"><i class="icon-headphones"></i>xxxx</a></li>
		<li class="nav-header">关于e代驾</li>
		<li <?php if($route=='zhaopin/youshi') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/docs?cat=test');?>"><i class="icon-headphones"></i>e代驾优势</a></li>
		<li <?php if($route=='zhaopin/charges') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/charges');?>"><i class="icon-headphones"></i>收费标准</a></li>
		<li class="nav-header">应用接入</li>
		<li <?php if($route=='api/docs') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/docs?cat=changes');?>"><i class="icon-headphones"></i>会员服务协议</a></li>
		<li class="divider"></li>
		<li><a href="<?php echo Yii::app()->createUrl('/contact');?>"><i class="icon-off"></i>联系e代驾</a></li>
	</ul>
</div>