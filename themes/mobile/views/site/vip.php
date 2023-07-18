<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <title>e代驾 - VIP申请</title>
    <meta name="description" content="e代驾，提供酒后代驾、商务代驾，服务城市开通北京上海杭州广州深圳，正规服务，专业代驾，费用最低39起步。e代驾，易代驾！电话4006-91-3939" />
    <meta name="keywords" content="e代驾,北京代驾公司,酒后代驾公司,长途代驾,北京汽车代驾,代驾服务公司" />
    <?php
    $cs=Yii::app()->clientScript;
    $cs->coreScriptPosition=CClientScript::POS_HEAD;
    $cs->scriptMap=array();
    $cs->registerCoreScript('jquery');

    $cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap.css');
    $cs->registerCssFile(SP_URL_IMG.'bootstrap/css/bootstrap-responsive.css');
    $cs->registerCssFile(SP_URL_STO.'www/css/edaijia.css');
    ?>
</head>
<style type="text/css">
    body{
        background:#e0e8ed;
    }
    .span5_div_apply{
        color:#999;
        line-height:180%;
        letter-spacing:1px;
    }
    #main .container-fluid{
        padding-top:30px;
    }
    .span7{
        float:left;
    }
    .span4{
        _float:right;
    }
    #apply_vip,#yw0,#VipApply_verifyCode{
        _margin-left:165px;
    }
</style>
<script type="text/javascript">
    $(function(){
        $('#apply_vip').click(function(){
            if($('#VipApply_name').val()==''){
                alert('申请人姓名不能为空！');
                return false;
            }
            if($('#VipApply_phone').val()==''){
                alert('申请人电话不能为空！');
                return false;
            }
            if($('#VipApply_mail').val()!=''){
                if(!/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test($('#VipApply_mail').val())){
                    alert('邮件格式不对！');
                    return false;
                }
            }
            if($('#WeddingApply_run_time').val()==''){
                alert('请填写举办日期！');
                return false;
            }
            if($('#city_id').val()=='0'){
                alert('请选择办理城市！');
                return false;
            }
            if($('#VipApply_book_money').val()!=''){
                if(!/^\d+$/.test($('#VipApply_book_money').val())){
                    alert('充值金额必须是整数！');
                    return false;
                }
            }
            if($('#VipApply_verifyCode').val()==''){
                alert('请输入验证码！');
                return false;
            }
            $('#apply-vip-form').submit();
            $('#apply_vip').attr('disabled','true');
        });
        $('#type').change(function(){
            if($('#type').val()=='1'){
                $('.company_name_type').css('display','none');
            }else if($('#type').val()=='0'){
                $('.company_name_type').css('display','block');
            }
        });
    });
</script>
<body>
<div id="header">
    <div class="block head">
        <a href="/" class="logo"><img src="<?php echo SP_URL_STO;?>www/images/logo.png" width="320" height="45" border="0" /></a>
        <ul class="nav">
            <li><a href="/">首页</a></li>
            <li><a href="/vip/" class="actives">VIP办理</a></li>
            <li><a href="http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/entry/">司机在线报名</a></li>
            <li><a href="/about/">关于我们</a></li>
            <li><a href="/faq/">FAQ</a></li>
        </ul>
    </div>
</div>
<div class="blank0"></div>
<div class="block clearfix">
    <div class="c_title">VIP申请</div>
</div>
<div class="blank"></div>
<div id="main" class="block clearfix form-horizontal">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span7">
                <?php $form = $this->beginWidget('CActiveForm', array(
                    'id'=>'apply-vip-form',
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>true,
                    'focus'=>array($model,'VipApply'),
                ));
                ?>
                <?php CHtml::$afterRequiredLabel = '';?>
                <?php CHtml::$beforeRequiredLabel = '<span style="color:red;">*</span> ';?>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'name',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'name',array('class'=>'input-xlarge info','placeholder'=>'申请人姓名')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'phone',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'phone',array('class'=>'input-xlarge info','placeholder'=>'申请人电话')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'type',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo CHtml::dropDownList('type',$model->type, VipApply::$apply_type,array('class'=>'info')); ?>
                    </div>
                </div>
                <div class="control-group company_name_type">
                    <?php echo $form->labelEx($model,'company_name',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'company_name',array('class'=>'input-xlarge info','placeholder'=>'公司名称')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'mail',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'mail',array('class'=>'input-xlarge info','placeholder'=>'邮件地址')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'city_id',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php
                        $cityArr= RCityList::model()->getOpenCityList();
                        $cityArr= array_merge(array(0=>'请选择城市'),$cityArr);
                        echo CHtml::activeDropDownList($model,'city_id', $cityArr,array('class'=>'info'));
                        ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'book_money',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'book_money',array('class'=>'input-xlarge info','placeholder'=>'充值金额')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'mark',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo CHtml::activeTextArea($model,'mark',array('rows'=>6,'class'=>'input-xlarge info','placeholder'=>'备注')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label for="VipApply_verifyCode" class="control-label required"><span style="color:red;">* </span>验证码</label>
                    <div class="controls">
                        <?php
                        echo $form->textField($model,'verifyCode',array('maxlength'=>4,'class'=>'span4','placeholder'=>'验证码'));
                        ?>
                        <?php
                        $this->widget("CCaptcha",array('buttonLabel' => '换一张'));
                        ?>
                    </div>
                    <label for="WeddingApply_verifyCode" class="control-label required"> </label>
                    <div class="controls">
                        <?php echo $form->error($model,'verifyCode',array('style'=>'color:red'));?>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <a class="btn btn-primary btn-large wedding_vip_submit" id="apply_vip">提交申请</a>
                    </div>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            <div class="span4 span5_div_apply">
                为了方便您更加有效的利用我们提供的服务资源，节省您的宝贵时间，减少多次结算之麻烦，增加优惠幅度，特向您提供集团帐户下，最高端VIP客户体验。只需验证您提供的手机号，系统自动结算，并向您推送短信账单。
                <br/><br/>
                表格提交后，我们会在1-2个工作日内与您联系。</br>
		VIP业务咨询联系方式</br>
		联系人：孟欣</br>
		联系电话：010-64392767（工作日10点-18点）</br>
		E-mail：mengxin@edaijia.cn</br>
            </div>
        </div>
    </div>
    <div class="c_content_bai"></div>
</div>
<div class="blank"></div>
<div id="footer" class="block">
    <div class="foot_nav"><a href="/about/">关于e代驾</a><a href="http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/">e代驾招募</a><a href="/hezuo/">服务与合作</a><a href="http://www.edaijia.cn/v2/">司机专区</a></div>
    <div class="copyright">Copyright &copy; 2011-2013 edaijia.cn All Right Reserved 版权所有 京ICP备13048976号-1</div>
</div>
</body>
</html>
