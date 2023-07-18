<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<script src="<?php echo SP_URL_IMG;?>bootstrap/js/bootstrap.min.js"></script>
<link href="<?php echo SP_URL_IMG;?>bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="<?php echo SP_URL_IMG;?>bootstrap/css/prettify.css" rel="stylesheet">
<link href="<?php echo SP_URL_CSS;?>edaijia.css" rel="stylesheet">
</head>
<body>
<?php 
	$this->beginContent('//layouts/nav');
	$this->endContent();
?>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span2">
			<?php 
				$route = Yii::app()->getController()->getRoute();
				$params = Yii::app()->getController()->getActionParams();
				$this->beginContent('//layouts/menu',array('route'=>$route,'params'=>$params)); 
				$this->endContent(); 
			?>
			</div>
			<div class="span10 well">
				<?php echo $content; ?>
			</div>
		</div>
		<div class="row-fluid">
		    <div class="span12" style="text-align:center">
		    	<hr class="divider"/>
		    	<p>Copyright@2011-2013 edaijia.cn All Right Reserved <br/>24小时热线：4006-91-3939</p>
		    </div>
		</div>
	</div>
</body>
</html>
