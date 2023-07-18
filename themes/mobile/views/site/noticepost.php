<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1,minimum-scale=1.0, maximum-scale=1.0" />
<title>e代驾-公告详情</title>
<?php 
$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->scriptMap=array();
$cs->registerCoreScript('jquery');
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap.min.css');
$cs->registerCssFile(SP_URL_CSS.'edaijia.css');
$cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap-responsive.min.css');
?>
</head>
<body>
<div class="container">
<h4 style="text-align: center;padding:5px;"><?php echo $model->title;?></h4>
<div style="padding:0 10px;">
    <?php echo $model->content; ?>
</div>
</div>
</body>
</html>