<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="edaijia" content="2.0.0">
<title>e代驾 - 用户登录</title>
<?php 
$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->scriptMap=array();
$cs->registerCoreScript('jquery');
$cs->registerCssFile(SP_URL_CSS.'styleh5.css');
?>
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
</head>
<body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="/v2/">
          	<img src="<?php echo SP_URL_IMG;?>edj-logo-h5.png" border="0" width="100px"/>
          </a>
        </div>
      </div>
    </div>
    <?php  if ($model->errors){
        echo '<div class="container" style="padding-top: 15px; font-size: 0.3rem; margin-bottom: 0.2rem;">错误的用户名或密码 或者验证码不正确</div>';
     } ?>
    <div class="container">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'well',
			'enableClientValidation'=>true,
			'focus'=>array($model,'username'),
			'clientOptions'=>array(
				'validateOnSubmit'=>true,
			),
			'htmlOptions'=>array('class'=>'well','style'=>'background-color:#ffffff')
		)); ?>
        <table border="0" align="center">
          <tr>
            <td colspan="2"><label>用户名：</label><?php echo $form->textField($model,'username',array('placeholder'=>'用户名')); ?></td>
          </tr>
          <tr>
            <td colspan="2"><label>密码：</label><?php echo $form->passwordField($model,'password',array('placeholder'=>'密码')); ?></td>
          </tr>
            <tr id="captche_tr" style="display: none;">
                <td width = "126"><?php echo $form->textField($model,'verifyCode',array('class' => 'text verifycode_text','placeholder' => '输入验证码'));?></td>
                <td valign="bottom"><?php
                    $this->widget('CCaptcha',array(
                        'captchaAction' => '/site/captcha',
                        'showRefreshButton' => false,
                        'clickableImage' => true,
                        'imageOptions' => array('class' => 'captche','title'=>'重新获取'),
                    ));

                    ?>
                </td>
            </tr>
            <tr id="captche_2" style="display: none;">
                <td width = "126"  colspan="2"><?php echo $form->textField($model,'verifyCodeNew',array('class' => 'text ','placeholder' => '请输入动态验证码'));?></td>

            </tr>
          <tr>
            <td  colspan="2"><input type="submit" value="登录" class="btn btn-success" /></td>
          </tr>
        </table>
        <?php $this->endWidget(); ?>
	</div>
	<div id="foot">
    	<div style="text-align:center">Copyright @2012-2013 All Right Reserved <br/>24小时热线：<a href="tel:4008103939">400-810-3939</a></div>
	</div>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#type").val("");
            $(".submit").click(function(){
                var username = $("#LoginForm_username").val();
                var password = $("#LoginForm_password").val();

                if(username == ''){
                    alert("请输入用户名");
                    return false;
                }
                if(password == ''){
                    alert("请输入密码");
                    return false;
                }
            });

            $("#LoginForm_username").blur(function(){
                showVerifyCode();
            });

            $("#login-form").submit(function(){
                showVerifyCode();
            });
            function showVerifyCode( ){
                var username = $("#LoginForm_username").val();
                var re = /^[0-9a-zA-Z]{6,20}$/;
                if (username.search(re) == '-1') {
                    //alert(check);
                    if( username != '' ){
                        var url = '<?php echo Yii::app()->createUrl('adminuser/checkIsAdmin');?>&username='+username;
                        $.getJSON(url,function(result){

                            if(result.code == 1){
                                $("#captche_2").show();
                                $("#captche_tr").hide();
                            }else{
                                $("#captche_tr").show();
                                $("#captche_2").hide();
                            }
                        });
                    }

                    $("#type").val("admin");
                    return false;
                } else {
                    $("#captche_tr").hide();
                    $("#type").val("");
                    $("#LoginForm_verifyCode").val("");
                }
            }
        });
    </script>
</body>
</html>
