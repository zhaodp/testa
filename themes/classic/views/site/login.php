<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>e代驾 - 用户登录</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo SP_URL_CSS;?>style2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo SP_URL_CSS;?>reset.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	#ie6-warning{font-family:'宋体';background:#FF0; position:absolute;top:0; left:0;font-size:18px; line-height:48px; color:#F00; padding:0 10px;}
	#ie6-warning img{float:right; cursor:pointer; margin-top:4px;}
	#ie6-warning a{text-decoration:none;}
</style>
<script type="text/javascript">
	if(window.top !== window.self){ window.top.location = window.location;} 
</script>
<?php  if ($model->errors){
    $m = $model->errors;
    if(is_array($m)){
        $msg = array_shift($m);
    }else $msg = $m;
    ?>
<script>
window.onload=function(){
	return showalert('<?php echo $msg[0];?>');
}

</script> 
<?php } ?>
</head>
<body>
<div class="l_logo">
<img src="<?php echo SP_URL_CSS;?>Logo_Login.png" />
<span>Hello,Wish you have a nice day :)</span>
</div>
<!--[if lte IE 6]>   <div id="ie6-warning"> <img src="x.gif" width="14" height="14" onclick="closeme();" alt="关闭提示" /> 您正在使用的IE浏览器版本太低，您需要将浏览器升级到 <a href="http://windows.microsoft.com/zh-CN/internet-explorer/downloads/ie-8" target="_blank">IE8</a>  或：<a href="http://chrome.360.cn/">360极速浏览器</a> 才能正常使用网站。</div> <script type="text/javascript">   function closeme(){    var div = document.getElementById("ie6-warning");    div.style.display ="none";} function position_fixed(el, eltop, elleft){   // check if this is IE6  if(!window.XMLHttpRequest)   window.onscroll = function(){   el.style.top = (document.documentElement.scrollTop + eltop)+"px";   el.style.left = (document.documentElement.scrollLeft + elleft)+"px";   }  else el.style.position = "fixed";  }   position_fixed(document.getElementById("ie6-warning"),0, 0);   </script> <![endif]-->
    <div id="login">
        <div class="login_bg">
            <div class="l_form">
                <div class="l_left">
                    <?php $form=$this->beginWidget('CActiveForm', array(
                        'id'=>'login-form',
                        'enableClientValidation'=>true,
                        'focus'=>array($model,'username'),
                        'clientOptions'=>array(
                            'validateOnSubmit'=>true,
                        ),
                    )); ?>
                    <input type="hidden" name = "type" id = "type" value="" />
                    <table width="282" border="0" align="center" cellspacing="0" cellpadding="0">
			<tr>
                                <td class="login-title" colspan="2">
                                   <!-- 欢迎使用e代驾V2系统-->
                                </td>
                            </tr>
                        <tr>
                            <td colspan="2"><?php echo $form->textField($model,'username',array('class'=>'text name_text','placeholder' => '输入用户名')); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?php echo $form->passwordField($model,'password',array('class'=>'text pwd_text','placeholder' => '输入密码')); ?>
                            </td>
                        </tr>
                        <tr id="captche_tr" style="display: none;">
                            <td width = "260"><?php echo $form->textField($model,'verifyCode',array('class' => 'text verifycode_text','placeholder' => '输入验证码'));?></td>
                            <td width ="100" valign="bottom"><?php
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
                            <td width = "360" colspan="2"><?php echo $form->textField($model,'verifyCodeNew',array('class' => 'text ','placeholder' => '请输入动态验证码','style'=>'width:360px;'));?>

                            </td>

                        </tr>
                        <tr id="BindPhone" style="display: none;">
                            <td width = "360" colspan="2">
                                <?php echo CHtml::textField('phone_num','',array('class' => 'text ','id'=>'phone_num','style'=>'width:260px;','placeholder' => '请输入短信验证码'));
                                echo CHtml::button('短信验证码',array('class'=>'btn','id'=>'getchecks'));?>
                            </td>
                        </tr>
                        <tr id="login_button">
                            <td colspan="2">
                                <input type="submit" id="submit" value="登录" class="submit btnSubmit"  style="cursor:pointer;"/>
                            </td>

                        </tr>

                        <tr id="getcodeButton" style="display:none;">
                            <td colspan="2">
                                <input type="button" id="getrdcode" value="登录" class="submit"  style="cursor:pointer;"/>
                                <span id="smsok" style="display:none;font-size:10px;">获取成功，请等待</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="help"><a class="texe" href="<?php echo Yii::app()->createUrl('site/login');?>&reset=1" title="重新绑定" style="text-decoration: underline;" id="rebind">重新绑定</a></td>
			    <td class="help help-right">
				<a title="请发送短信 “忘记密码” 到 106911892930" data-placement="right" data-toggle="tooltip" href="#">忘记密码？</a>
			    </td>
                        </tr>
                    </table>
                    <?php $this->endWidget(); ?>
                </div>
               <div class="l_right" id="qrcode_position" style="display: none;"><!--  -->
<!--                    <div >-->
<!--                        <img id="qrcode" src="" width="200">-->
<!--                    </div>-->
<!--                    <a href="--><?php //echo Yii::app()->createUrl('site/login');?><!--" style="padding-left:60px;">已绑定完毕</a>-->

                </div>

            </div>
        </div>
        <div class="l_foot">
            Copyright @ <?php echo date('Y');?> edaijia.cn All Right Reserved <br/> 24小时热线：400-810-3939
        </div>
    </div>
<?php 
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    'options'=>array(
        //'title'=>'登录错误提示',
        'autoOpen'=>false,
		'width'=>'600',
		'height'=>'400',
		'modal'=>true,
		'htmlOptions'=>array('style'=>'display:none'),
		'buttons'=>array(
            'OK'=>'js:function(){$("#mydialog").dialog("close");}',    
        ),
    ),
));
echo '<div id="xsnazzy" style="display:none;">
		<div class="l"></div>
        <div class="m">
        	<div class="meg" id="err_msg">用户名或密码不正确或您已被屏蔽！</div>
        </div>
        <div class="r"></div>
    </div> ';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>


<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'helpdialog',
    'options'=>array(
        'title'=>'登陆帮助',
        'autoOpen'=>false,
        'width'=>'740',
        'height'=>'790',
        'modal'=>true,
        'htmlOptions'=>array('style'=>'display:none'),
        'buttons'=>array(
            '已经绑定二维码'=>'js:function(){$("#helpdialog").dialog("close");location.href="'.Yii::app()->createUrl('site/login').'";}',
        ),
    ),
));
echo '<div id="help" style="display:none;">
        <div class="image_div" id="err_msg"><img src="http://pic.edaijia.cn/v2/help.png"></div>
        <div id="bind_qrcode" style="text-align:center;overflow: hidden;"> <img id="qrcode" src="" width="200" style="display:inline-block;"></div>
    </div> ';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<script type="text/javascript">
    var has_request = false;
    $(document).ready(function(){
       $("#type").val("");
        $("#login-form").keypress(function(e) {
            if (e.which == 13) {
                if(has_request == false){
                    showVerifyCode();
                    return false;
                }
            }
        });
        
        $("body").on('keyup',function(event) {
            if(event.keyCode==13){
                $(".btnSubmit").click();
            }
        });

        $("#submit").click(function(){
           var username = $("#LoginForm_username").val();
           var password = $("#LoginForm_password").val();

           if(username == ''){
               showalert("请输入用户名");
               return false;
           }
           if(password == ''){
               showalert("请输入密码");
               return false;
           }
       });

       $("#LoginForm_username").blur(function(){
           showVerifyCode();
       });

       $("#login-form").submit(function(){
           showVerifyCode();
       });
       function showVerifyCode(  ){
           var username = $("#LoginForm_username").val();
           var re = /^[0-9a-zA-Z]{6,20}$/;
           var reset = '<?php  echo isset($_GET['reset']) ? (int)$_GET['reset'] : '0';?>';
           if (username.search(re) == '-1') {
               //alert(check);
               if( username != '' ){
                   username = encodeURIComponent(username);
                   var url = '<?php echo Yii::app()->createUrl('adminuser/checkIsAdmin');?>&username='+username;
                   $.getJSON(
                       url,
                       function(result){
                           if(result.code == 1){
                               if(result.data.first_login == 1 || reset == '1'){
                                   //console.log(result.data.first_login);
                                   $("#login_button").hide();
                                   $("#getcodeButton").show();
                                   $("#BindPhone").show();
                                   $("#captche_tr").hide();
                               } else {
                                   $("#captche_2").show();
                                   $("#captche_tr").hide();
                                   $("#getcodeButton").hide();
                                   $("#BindPhone").hide();
                               }
                           } else {
                               $("#captche_tr").show();
                               $("#captche_2").hide();
                               $("#getcodeButton").hide();
                               $("#BindPhone").hide();
                           }
                           if(result.data.value == 1 || result.data.value == 0){
                               $('#rebind').html('绑定账户');
                           }else{
                               $('#rebind').html('重新绑定');
                           }
                   });
               }

               $("#type").val("admin");
               return false;
           } else {
               $("#captche_tr").hide();
               $("#captche_2").hide();
               $("#getcodeButton").hide();
               $("#type").val("");
               $("#LoginForm_verifyCode").val("");
               $("#BindPhone").hide();

           }
       }
        //忘记密码
        $('a[data-toggle=tooltip]').tooltip();

        //发送手机验证码
        $('#getchecks').click(function(){
            var username = $("#LoginForm_username").val();
            var password = $("#LoginForm_password").val();
            var phone = $('#phone_num').val();


            if(username == ''){
                showalert("请输入用户名");
                return false;
            }
            if(password == ''){
                showalert("请输入密码");
                return false;
            }

            url = '<?php echo Yii::app()->createUrl('site/getSmsCode');?>&username='+username + '&password=' + password;
            $.getJSON(
                url,
                function(result){
                    if(result.code == 1){
                        var msg = '验证码已发手机' + result.data + '，请在十分钟内输入';
                        $('#smsok').html(msg);
                        $('#smsok').show();
                    } else {
                        showalert(result.message);
                        return false;
                    }
                });

            return false;

        });

        //发送获取二维码
        $('#getrdcode').click(function(){
            var username = $("#LoginForm_username").val();
            var password = $("#LoginForm_password").val();
            var phone = $('#phone_num').val();


            if(username == ''){
                showalert("请输入用户名");
                return false;
            }
            if(password == ''){
                showalert("请输入密码");
                return false;
            }

            if(phone == ''){
                showalert("请输入短信验证码");
                return false;
            }

            url = '<?php echo Yii::app()->createUrl('site/getRdCode');?>&username='+username + '&password=' + password + '&smscode='+phone;
            $.getJSON(
                url,
                function(result){
                    if(result.code == 1){
                        $('#qrcode').attr('src',result.data);
                        $('#help').show();
                        $("#helpdialog").dialog("open");
                    } else {
                        showalert(result.message);
                        return false;
                    }
                });

            return false;

        });

        //getrdcode
    });


    function showalert(message){
        $('#err_msg').html(message);
        $('#xsnazzy').show();
        $("#mydialog").dialog("open");
        return false;
    }

</script>


</body>
</html>
