<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>统一权限系统</title>
</head>
<?php
	if(!isset(Yii::app()->user->user_id)){
	    $this->redirect(array('site/login'));
	}

	$appv2 = AdminApp::model()->findByPk(2);
	$v2url = $appv2->url;
?>
<frameset rows="40,*" cols="*" frameborder="no" border="0" framespacing="0">
<frame src="<?php echo Yii::app()->createUrl('/default/top'); ?>" name="topFrame" scrolling="No" noresize="noresize" id="topFrame"/>
  <frameset cols="198,*" frameborder="no" border="0" framespacing="0">
    <frame src="<?php echo Yii::app()->createUrl('/default/left'); ?>" name="leftFrame" scrolling="Yes" noresize="noresize" id="leftFrame"/>
    <frame src="<?php echo $v2url.'/index.php?r=account/summary'; ?>" name="mainFrame" id="mainFrame"/>

<noframes>
<body>
<p>对不起，您的浏览器不支持“框架”！</p>
</body>
</noframes>

</frameset>

</html>
