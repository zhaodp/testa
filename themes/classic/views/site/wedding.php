<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <title>e代驾 - 婚庆代驾申请</title>
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
#apply_wedding,#yw0,#WeddingApply_verifyCode{
    _margin-left:165px;
}
</style>
<script type="text/javascript">
    $(function(){
        $('#apply_wedding').click(function(){
            if($('#WeddingApply_name').val()==''){
                alert('申请人姓名不能为空！');
                return false;
            }
            if($('#WeddingApply_phone').val()==''){
                alert('联系电话不能为空！');
                return false;
            }
            if($('#run_time').val()!=''){
                if($('#run_time').val()==''){
                    alert('请选择举办日期！');
                    return false;
                }
            }
            if($('#city_id').val()=='0'){
                alert('请选择举办城市！');
                return false;
            }
            if($('#WeddingApply_detail_site').val()==''){
                alert('请填写宴会举办详细地址！');
                return false;
            }
            if(!/^\d+$/.test($('#WeddingApply_number').val())){
                alert('参加人数必须是数字！');
                return false;
            }
            if($('#WeddingApply_mark').val()==''){
                alert('请填写宴会流程和主要内容！');
                return false;
            }
            if($('#WeddingApply_verifyCode').val()==''){
                alert('请输入验证码');
                return false;
            }
            $('#wedding-apply-form').submit();
            $('#apply_wedding').attr('disabled','true');
        });
        $('#run_time').focus(function(){
            $('#run_time').attr('readonly','readonly');
        });
        $('#run_time').blur(function(){
            $('#run_time').removeAttr('readonly');
        });
    });
</script>
<body>
<div id="header">
    <div class="block head">
        <a href="/" class="logo"><img src="<?php echo SP_URL_STO;?>www/images/logo.png" width="320" height="45" border="0" /></a>
        <ul class="nav">
            <li><a href="/">首页</a></li>
            <li><a href="/vip/">VIP办理</a></li>
            <li><a href="http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/entry/">司机在线报名</a></li>
            <li><a href="/about/">关于我们</a></li>
            <li><a href="/faq/">FAQ</a></li>
        </ul>
    </div>
</div>
<div class="blank0"></div>
<div class="block clearfix">
    <div class="c_title">婚庆代驾申请</div>
</div>
<div class="blank"></div>
<div id="main" class="block clearfix form-horizontal">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span7">
                <?php $form = $this->beginWidget('CActiveForm', array(
                    'id'=>'wedding-apply-form',
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>true,
                )); ?>
                <?php CHtml::$afterRequiredLabel = '';?>
                <?php CHtml::$beforeRequiredLabel = '<span style="color:red;">*</span> ';?>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'name',array('class'=>'control-label')) ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'name',array('class'=>'input-xlarge info','placeholder'=>'申请人姓名')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'phone',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'phone',array('class'=>'input-xlarge info','placeholder'=>'联系电话')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'wedding_type',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo CHtml::dropDownList('wedding_type',$model->wedding_type, WeddingApply::$wedding_types,array('class'=>'info')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'run_time',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php
                        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                        $this->widget('CJuiDateTimePicker', array (
                            'attribute'=>'run_time',
                            'model'=>$model,
                            'name'=>$model->run_time,
                            'mode'=>'date',  //use "time","date" or "datetime" (default)
                            'options'=>array (
                                'dateFormat'=>'yy-mm-dd',
                                'minDate'=>'new Date()',
                            ),  // jquery plugin options
                            'language'=>'zh',
                            'htmlOptions'=>array(
                                'placeholder'=>"选择宴会日期",
                                'class'=>'input-xlarge info',
                            ),
                        ));
                        ?>
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
                    <?php echo $form->labelEx($model,'hotels',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'hotels',array('class'=>'input-xlarge info','placeholder'=>'举办酒店')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'detail_site',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'detail_site',array('class'=>'input-xlarge info','placeholder'=>'详细地址')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'number',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo $form->textField($model,'number',array('class'=>'input-xlarge info','placeholder'=>'参加人数')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <?php echo $form->labelEx($model,'mark',array('class'=>'control-label')); ?>
                    <div class="controls">
                        <?php echo CHtml::activeTextArea($model,'mark',array('rows'=>6,'class'=>'input-xlarge info','placeholder'=>'流程和主要内容')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label for="WeddingApply_verifyCode" class="control-label required"><span style="color:red;">* </span>验证码</label>
                    <div class="controls">
                        <?php
                        echo $form->textField($model,'verifyCode',array('class'=>'span4','maxlength'=>4,'placeholder'=>'验证码'));
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
                        <a class="btn btn-primary btn-large wedding_vip_submit" id="apply_wedding">提交申请</a>
                    </div>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            <div class="span4 span5_div_apply">
                婚宴/酒会因为开车不能喝酒，欢乐的时刻不能尽兴？现在，只要您的婚宴/酒会主题创意独特、规模够大、气氛够嗨，就有机会得到e代驾赞助的现场代驾服务。如您的婚宴/酒会需要我们赞助代驾服务，请详细填写下列表格。
                <br/><br/>
                申请提交成功后我们会在3-5个工作日内与您联系。
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
