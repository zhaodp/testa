<div class="well" style="padding: 19px 2px;">
	<ul class="nav nav-list">
<?php 
	echo $route;
	print_r($params);
?>
	<li class="divider"></li>
	<li><a href="<?php echo Yii::app()->createUrl('/docs/contact');?>"><i class="icon-off"></i>联系我们</a></li>
	</ul>
</div>