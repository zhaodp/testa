<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<title><?php echo $partner_name;?>登录平台</title>
<style>
*{margin:0;padding:0;border:0;list-style:none;/*-webkit-user-select:none;*/-webkit-tap-highlight-color:rgba(255,0,0,0);}
a{
	text-decoration:none!important;
	list-style: none outside none;
	overflow: visible;
    text-overflow: clip;
    white-space: normal;
}
body{background:#008ae6 url(/images/partner/bg.jpg) center top no-repeat;}
.top_box{
	width:950px;
	height:60px;
	margin:10px auto;
}
.top_box img{float:left;}
.top_box h1{
	float:left;
	color:#FFF;
	line-height:60px;
}
.main_box{
	width:460px;
	height:400px;
	padding-top:100px;
	background:url(/images/partner/ball.png) center top no-repeat;
	margin:30px auto;
}
.main_box p{
	display:block;
	width:230px;
	height:40px;
	margin:20px auto;
}
.main_box p input{
	display:block;
	width:230px;
	height:40px;
	border-radius:5px;
	text-indent:10px;
	line-height:40px;
	background-color:#FFFFFF;
	
}
span{
	display:block;
	width:130px;
	height:60px;
	font-size:60px;
	text-align:center;
	color:#FFF;
	font-weight:bold;
	margin:60px auto;
}
p.bottom_box{
	font-size:14px;
	text-align:center;
	color:#2c62a7;
	font-weight:bold;
}

</style>
</head>
<body>
<div class="top_box"><img src="/images/partner/e_logo_b.png"/><h1><?php echo $partner_name;?>登录平台</h1></div>
<div class="main_box">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'login-form',
        'enableClientValidation'=>false,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
        ),
        'htmlOptions' => array(
            'class' => 'form-horizontal'
        )
    )); ?>

    <p>
        <!--
        <input type="text" placeholder="输入用户名" />
        -->
        <?php echo $form->textField($model,'username', array('placeholder'=> '请输入用户名')); ?>
    </p>
    <p>
        <!--
        <input type="text" placeholder="输入密码" />
        -->
        <?php echo $form->passwordField($model,'password', array('placeholder'=>'请输入密码')); ?>
    </p>
    <p>
        <!--
        <input type="text" placeholder="输入验证码" style="float:left;width:110px;"/>
        <input type="text"  value="jxbn"  style="float:right;width:110px;"/>
        -->
        <?php echo $form->textField($model,'verifyCode', array('style'=>'float:left; width:80px', 'placeholder'=>'请输入验证码')); ?>
        <?php
        $this->widget('CCaptcha',array(
            'showRefreshButton' => false,
            'clickableImage' => true,
            'imageOptions' => array('class' => 'captche','title'=>'重新获取', 'style'=>'float:right; width:110px'),
        ));
        ?>
        <?php echo $form->error($model,'verifyCode'); ?>
    </p>
    <span style="cursor: pointer" id="submit">登录</span>
    <?php $this->endWidget(); ?>
</div>
<p class="bottom_box">Copyright © 2011-2013 edaijia.cn All Right Reserved 24小时热线 400-691-3939</p>  
</body>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#submit').click(function(){
            jQuery('#login-form').submit();
        });
    });
</script>
</html>