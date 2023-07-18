<?php
/* @var $this PartnerController */
/* @var $model Partner */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'partner-form',
	'enableAjaxValidation'=>false,
)); ?>
    <p class="note text-info">带 <span class="required">*</span> 是必填项.</p>

    <?php echo $form->errorSummary($model); ?>
    <div class="hero-unit" style="padding: 10px;margin-bottom: 20px">
        <h3>基本信息</h3>
        <div class="row-fluid">
            <div class="span9">
                <div class="span3">
                    <div class="row-fluid">
                        <?php echo $form->labelEx($model,'name'); ?>
                        <?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100)); ?>
                        <?php echo $form->error($model,'name'); ?>
                    </div>
                </div>

                <div class="span3">
                    <div class="row-fluid">
                        <?php echo $form->labelEx($model,'city'); ?>
                        <?php $cityList = Dict::items('city');/*$cityList[0] = '请选择城市';*/ echo $form->dropDownList($model,'city', $cityList); ?>
                        <?php echo $form->error($model,'city'); ?>
                    </div>
                </div>

                <div class="span3">
                    <div class="row-fluid">
                        <?php echo $form->labelEx($model,'contact'); ?>
                        <?php echo $form->textField($model,'contact',array('size'=>50,'maxlength'=>50)); ?>
                        <?php echo $form->error($model,'contact'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span9">
                <div class="span3">
                    <div class="row-fluid">
                        <?php echo $form->labelEx($model,'phone'); ?>
                        <?php echo $form->textField($model,'phone',array('size'=>15,'maxlength'=>15, 'value' => '')); ?>
                        <?php echo $form->error($model,'phone'); ?>
                    </div>
                </div>
                <div class="span3">
                    <div class="row-fluid">
                        <?php echo $form->labelEx($model,'seat_number'); ?>
                        <?php echo $form->textField($model,'seat_number',array('size'=>11,'maxlength'=>11, 'value' => '100')); ?>
                        <?php echo $form->error($model,'seat_number'); ?>
                    </div>
                </div>
                <div class="span3">
                    <div class="row-fluid">
                        <?php echo $form->labelEx($model,'channel_id'); ?>
                        <?php echo $form->textField($model,'channel_id',array('size'=>5,'maxlength'=>5)); ?>
                        <?php echo $form->error($model,'channel_id'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span9">
                <div class="row-fluid">
                    <?php echo $form->labelEx($model,'address'); ?>
                    <?php echo $form->textArea($model,'address',array('rows'=>3,'class'=>'span9')); ?>
                    <?php echo $form->error($model,'address'); ?>
                </div>
            </div>
        </div>

                <div class="row-fluid">
            <div class="span3">
                <div class="row-fluid">
                    <?php echo $form->labelEx($model,'send_sms'); ?>
                    <?php echo $form->radioButtonList($model,'send_sms',array('1'=>'发送','0'=>'不发送'), array('separator'=>'', 'template'=>'<label class="radio inline">{input}   {label}</label>')); ?>
                    <?php echo $form->error($model,'send_sms'); ?>
                </div>
            </div>
            <div class="span3">
                <div class="row-fluid">
                    <?php echo $form->labelEx($model,'remark'); ?>
                    <?php echo $form->radioButtonList($model,'remark',array('1'=>'显示','0'=>'不显示'), array('separator'=>'', 'template'=>'<label class="radio inline">{input}   {label}</label>')); ?>
                    <?php echo $form->error($model,'remark'); ?>
                </div>
            </div>
          <div class="span3">
                  <div class="row-fluid">
                     <?php echo $form->labelEx($model,'show_balance'); ?>
                     <?php echo $form->radioButtonList($model,'show_balance',array('1'=>'显示','0'=>'不显示'), array('    separator'=>'', 'template'=>'<label class="radio inline">{input}   {label}</label>')); ?>
                     <?php echo $form->error($model,'show_balance'); ?>
                 </div>
          </div>

        </div>
        
        <div class="row-fluid">
            <div class="span9">
                <div class="row-fluid">
                    <?php if ($model->logo) {?>
                        <img src="<?php echo $model->logo; ?>" />
                    <?php } ?>
                    <?php echo $form->hiddenField($model,'logo'); ?>
                    <?php echo $form->error($model,'logo'); ?>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span9">
                <div class="row-fluid">
                    <a href="javascript:void(0)" id="uploadLogo" class="btn" class="btn">上传LOGO</a>
                </div>
            </div>
        </div>

    </div>

    <div class="hero-unit" style="padding: 10px;margin-bottom: 20px">
        <h3>配置信息</h3>
        <h4>短信配置</h4>
        <div class="row-fluid">
            <div class="span9">
                <div class="span3">
                    <div class="row-fluid">
                        <?php echo $form->labelEx($model,'sms_call'); ?>
                        <?php echo $form->textField($model,'sms_call',array('size'=>30,'maxlength'=>30)); ?>
                        <?php echo $form->error($model,'sms_call'); ?>
                    </div>
                </div>
            </div>
        </div>

        <h4>结算配置</h4>

        <div class="row-fluid">
            <div class="span9">
                <div class="span3">
                    <div class="row-fluid">
                        <?php echo CHtml::dropDownList('user_pay_sort', '', $paySort)?>
                    </div>
                </div>
                <div class="span3" id="pay_method">

                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span9" id="change_pay_sort">

            </div>
        </div>
        <div class="row-fluid">
            <div class="span9" id="change_label">

            </div>
        </div>
    </div>

	<div class="row-fluid buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存',array('class' => 'btn btn-success span3')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->


<iframe style="display:none; border:none;" src="" name="yframe"></iframe>

<div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal hide fade" id="myModal" style="display: block;">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
        <h3 id="myModalLabel">合作商家LOGO上传</h3>
    </div>
    <div class="modal-body">
        <form target="yframe" method="post" enctype="multipart/form-data" action="<?php echo Yii::app()->createUrl('image/imgupload', array('type'=>'img', 'base_path'=>'partner','CKEditor'=>'KnowledgeData_content','CKEditorFuncNum'=>'1','langCode'=>'zh-cn', 'call_back_self'=>1, 'call_back_fun'=>'imageUploadCallBack'));?>">
            <input type="file" name="upload" size="38">
            <input type="submit" value="上传" class="btn" />
        </form>
    </div>
    <div class="modal-footer">
        <button data-dismiss="modal" class="btn">关闭</button>
        <!--
        <button class="btn btn-primary">Save changes</button>
        -->
    </div>
</div>

<script type="text/javascript">
    $(function(){
        jQuery('#uploadLogo').click(function(){
            jQuery('#myModal').modal('show');
        });
        /*
        *表单提交验证
        * */
        $("#partner-form").submit(function(){
            /*var partnerCity = $("#Partner_city option:selected").val();
            if(partnerCity == 0){
                alert('请选择城市');
                return false;
            }*/
            var seatNumber = $("#Partner_seat_number").val();
            if(parseInt(seatNumber) > 999){
                alert('坐席数量最多999个');
                return false;
            }
            var userPaySort = $("#user_pay_sort option:selected").val();
            if(userPaySort == 0){
                alert('请选择付费类型');
                return false;
            }else if(userPaySort == 1){
                var userPayMethod = $("#user_pay_method option:selected").val();
                if(userPayMethod == 0){
                    alert('请选择付费方式');
                    return false;
                }else if(userPayMethod == 1){
                    var vipCard  = $("#Partner_vip_card").val();
                    var vipCheck = $("#Partner_vip_card").attr('vip-check');
                    if(vipCard == ''){
                        alert('VIP手机号不能为空');
                        return false;
                    }
                    if(vipCheck != 'yes'){
                        alert('VIP手机号不可用');
                        return false;
                    }
                    if($("#vip_confirm").attr('checked') != 'checked'){
                        alert('请确认该合作商家是否使用此VIP账户结算');
                        return false;
                    }
                }else{
                    var bonusSn = $("#Partner_bonus_sn").val();
                    var bonusPhone = $("#Partner_bonus_phone").val();
                    var bonusSnCheck = $("#Partner_bonus_sn").attr('bonus-sn-check');
                    var bonusPhoneCheck = $("#Partner_bonus_phone").attr('bonus-phone-check');
                    if(bonusSn == ''){
                        alert('优惠码不能为空');
                        return false;
                    }
                    if(bonusSnCheck == 'no'){
                        alert('优惠码不可用');
                        return false;
                    }
                    if(bonusPhone == ''){
                        alert('优惠券数量不能为空');
                        return false;
                    }
                    if(bonusPhoneCheck == 'no'){
                        alert('优惠券绑定电话不可用');
                        return false;
                    }
                }
            }else{
               var sharingAmount = $("#Partner_sharing_amount").val();
               if(sharingAmount == ''){
                    alert('成单分成金额不能为空');
                    return false;
               }
               if($("#divided_confirm").attr('checked') != 'checked'){
                   alert('请确认该合作商家是否使用报单分成进行结算');
                   return false;
               }
            }
        });
        //初始化DIV为隐藏状态
        $("#change_pay_sort").hide();
        $("#change_label").hide();
        $("#pay_method").hide();

        /*
        * 结算配置-付费类型交互
        * */
        $("#user_pay_sort").change(function(){
            var paySort = $("#user_pay_sort option:selected").val();
            if(paySort == 1) {

                $("#change_pay_sort").hide();
                $("#change_pay_sort").html('');
                $("#change_label").hide();
                $("#change_label").html('');
                $("#pay_method").show();

                $("#pay_method").html('<div class="span3">'+
                    '<div class="row-fluid">'+
                        '<select id="user_pay_method" name="user_pay_method">'+
                        '<option value="0">请选择付费方式</option>'+
                        '<option value="1">VIP全额免单</option>'+
                        '<option value="2">优惠券减免</option>'+
                        '</select>'+
                    '</div>'+
                '</div>');

            }else if(paySort == 2){

                $("#change_pay_sort").show();
                $("#change_label").show();
                $("#pay_method").hide();
                $("#pay_method").html('');

                $("#change_pay_sort").html('<div class="span3">' +
                    '<div class="row-fluid">' +
                        '<?php echo $form->labelEx($model,'sharing_amount'); ?>
                        <?php echo $form->textField($model,'sharing_amount',array('size'=>10,'maxlength'=>10, 'value' => '')); ?>
                        <?php echo $form->error($model,'sharing_amount'); ?>'+
                    '</div>' +
                '</div>');

                $("#change_label").html('<div class="row-fluid">' +
                    '<?php echo CHtml::checkBox('divided_confirm')?>
                <?php echo CHtml::label("请确认该合作商家将使用报单分成进行结算", "divided_confirm", array('class'=>'checkbox inline', 'style' => 'padding-left:0px;'))?>'+
                '</div>');
            }else{

                $("#change_pay_sort").hide();
                $("#change_pay_sort").html('');
                $("#change_label").hide();
                $("#change_label").html('');
                $("#pay_method").hide();
                $("#pay_method").html('');
            }
        });

        /*
        * 结算配置-预付费-付费方式交互
        * */
        $("#user_pay_method").live('change', function(){
            var payMethod = $("#user_pay_method option:selected").val();
            if(payMethod == 1){

                $("#change_pay_sort").show();
                $("#change_label").show();

                $("#change_pay_sort").html('<div class="span3">' +
                    '<div class="row-fluid">' +
                        '<?php echo $form->labelEx($model,'vip_card'); ?>
                        <?php echo $form->textField($model,'vip_card',array('size'=>11,'maxlength'=>11, 'vip-check' => 'no')); ?>
                        <?php echo $form->error($model,'vip_card'); ?>'+
                    '</div>' +
                '</div>'+
                '<div class="span6" id="check_vip_status"></div>');

                $("#change_label").html('<div class="row-fluid">' +
                    '<?php echo CHtml::checkBox('vip_confirm')?>
                <?php echo CHtml::label("请确认该合作商家将使用此VIP账户结算", "vip_confirm", array('class'=>'checkbox inline', 'style' => 'padding-left:0px;'))?>'+
                '</div>');

            }else if(payMethod == 2){

                $("#change_pay_sort").show();
                $("#change_label").show();

                $("#change_pay_sort").html('<div class="span3">' +
                    '<div class="row-fluid">' +
                        '<?php echo $form->labelEx($model,'bonus_sn'); ?>
                        <?php echo $form->textField($model,'bonus_sn',array('size'=>30,'maxlength'=>30, 'bonus-sn-check' => 'no')); ?>
                        <?php echo $form->error($model,'bonus_sn'); ?>'+
                    '</div>' +
                '</div>'+
                '<div class="span3">' +
                    '<div class="row-fluid">'+
                    '<?php echo $form->labelEx($model,'bonus_phone'); ?>
                    <?php echo $form->textField($model,'bonus_phone',array('value' => '', 'bonus-phone-check' => 'no')); ?>
                    <?php echo $form->error($model,'bonus_phone'); ?>'+
                    '</div>' +
                '</div>'+
                '<div class="span6" id="check_bonus_status"></div>');

                $("#change_label").html('<div class="row-fluid">' +
                    '<?php echo CHtml::checkBox('bonus_confirm')?>
                    <?php echo CHtml::label("请确认该合作商家将使用此优惠劵减免", "bonus_confirm", array('class'=>'checkbox inline', 'style' => 'padding-left:0px;'))?>'+
                '</div>');

            }else{

                $("#change_pay_sort").hide();
                $("#change_pay_sort").html('');
                $("#change_label").hide();
                $("#change_label").html('');
            }
        });
        $("#Partner_vip_card").live('blur', function(){
            var vipCard = $(this).val();
            if(vipCard != ''){
                $.ajax({
                    'url':'<?php echo Yii::app()->createUrl('/partner/checkVipno');?>',
                    'data':{'vip_card':vipCard},
                    'type':'get',
                    'dataType':'json',
                    'cache':false,
                    'success':function(data){
                        if(data.status == 1){
                            $("#Partner_vip_card").attr('vip-check', 'yes');
                            $('#check_vip_status').html(
                                '<div class="row-fluid">'+
                                    '<?php echo $form->label($model, '&nbsp'); ?>'+
                                    '<p class="text-success">' + data.message + '，余额' + data.balance + '元</p>'+
                                '</div>');
                        }else if(data.status == 3){
                            $('#check_vip_status').html(
                                '<div class="row-fluid">'+
                                    '<?php echo $form->label($model, '&nbsp'); ?>'+
                                    '<p class="text-error">' + data.message + '，欠费' + data.balance + '元</p>'+
                                '</div>');
                        }else if(data.status == 2) {
                            $('#check_vip_status').html(
                                '<div class="row-fluid">'+
                                    '<?php echo $form->label($model, '&nbsp'); ?>'+
                                    '<p class="text-error">VIP账户被禁用</p>'+
                                '</div>');
                        }else if(data.status == 99){
                            $('#check_vip_status').html(
                                '<div class="row-fluid">'+
                                    '<?php echo $form->label($model, '&nbsp'); ?>'+
                                    '<p class="text-error">'+data.message+'</p>'+
                                    '</div>');
                        }else{
                            $('#check_vip_status').html(
                                '<div class="row-fluid">'+
                                    '<?php echo $form->label($model, '&nbsp'); ?>'+
                                    '<p class="text-error">VIP账户不存在，请重新输入或 <a href="" target="_blank"> 点击开通VIP</a></p>'+
                                '</div>');
                        }
                    }
                });
            }
        });

        $("#Partner_bonus_sn").live('blur', function(){
            var bonusSn = $(this).val();
            if(bonusSn != ''){
                $.ajax({
                    'url':'<?php echo Yii::app()->createUrl('/partner/checkVipno');?>',
                    'data':{'bonus_sn':bonusSn},
                    'type':'get',
                    'dataType':'json',
                    'cache':false,
                    'success':function(data){
                        if(data.status == 1){
                            $("#Partner_bonus_sn").attr('bonus-sn-check', 'yes');
                        }else if(data.status == 99){
                            $("#check_bonus_status").html(
                                '<div class="row-fluid">'+
                                    '<?php echo $form->label($model, '&nbsp'); ?>'+
                                    '<p class="text-error">'+data.message+'</p>'+
                                '</div>'
                            );
                        }else{
                            $("#check_bonus_status").html(
                                '<div class="row-fluid">'+
                                    '<?php echo $form->label($model, '&nbsp'); ?>'+
                                    '<p class="text-error">'+data.message+'</p>'+
                                '</div>'
                            );
                        }
                    }
                });
            }
        });

        $("#Partner_bonus_phone").live('blur', function(){
            var bonusPhone = $(this).val();
            if(bonusPhone != ''){
                $.ajax({
                    'url':'<?php echo Yii::app()->createUrl('/partner/checkVipno');?>',
                    'data':{'bonus_phone':bonusPhone},
                    'type':'get',
                    'dataType':'json',
                    'cache':false,
                    'success':function(data){
                        if(data.status == 1){
                            $("#Partner_bonus_phone").attr('bonus-phone-check', 'yes');

                        }else{
                            $("#check_bonus_status").html(
                                '<div class="row-fluid">'+
                                    '<?php echo $form->label($model, '&nbsp'); ?>'+
                                    '<p class="text-error">'+data.message+'</p>'+
                                '</div>'
                            );
                        }
                    }
                });
            }
        });
    })

    function imageUploadCallBack(fileurl, message) {
        fileurl = fileurl.replace('_400', '');
        if (fileurl.length <= 0) {
            alert(message);
            return false;
        }
        var img_html = '<img src="'+fileurl+'" />';
        var img = jQuery(img_html);
        jQuery('#Partner_logo').before(img);
        jQuery('#Partner_logo').val(fileurl);
        jQuery('#myModal').modal('hide');
    }
</script>
