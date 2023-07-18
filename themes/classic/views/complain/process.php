<?php
    $cs=Yii::app()->clientScript;
    $cs->coreScriptPosition=CClientScript::POS_HEAD;
    $cs->scriptMap=array();
    $cs->registerCoreScript('jquery');
    $cs->registerScriptFile(SP_URL_STO.'jquery.confirm/jquery.confirm.js',CClientScript::POS_END);
    $cs->registerCssFile(SP_URL_STO.'jquery.confirm/jquery.confirm.css');
?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'complain-confirm-form',
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'post',
));
?>
<?php echo CHtml::hiddenField('closing'); ?>
<div>
    <h1>投诉详情</h1>
</div>
<div class="search-form thumbnail">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div class="span1">
                    <label>订单编号</label>
                    <?php echo CHtml::textField('order_id',$model->order_id,array('class'=>'span12','placeholder'=>'订单编号','readonly'=>'readonly')); ?>
                </div>
                <div class="span1">
                    <label>司机工号</label>
                    <?php echo CHtml::textField('driver_id',$model->driver_id,array('class'=>'span12','placeholder'=>'司机工号','readonly'=>'readonly')); ?>
                </div>
                <div class="span2">
                    <label>投诉人电话</label>
                    <?php echo CHtml::textField('driver_id',$model->phone,array('class'=>'span12','placeholder'=>'投诉人电话','disabled'=>'disabled')); ?>
                </div>
                <div class="span2">
                    <label>投诉人姓名</label>
                    <?php echo CHtml::textField('name',$model->name,array('class'=>'span12','placeholder'=>'客人姓名','readonly'=>'readonly')); ?>
                </div>
                <div class="span2">
                    <label>预约电话</label>
                    <?php echo CHtml::textField('customer_phone',$model->customer_phone,array('class'=>'span12','placeholder'=>'预约电话','readonly'=>'readonly')); ?>
                </div>
                <div class="span2">
                    <label>投诉来源</label>
                    <?php echo CHtml::dropDownList('source',$model->source,CustomerComplain::$source,array('empty'=>'全部','readonly'=>'readonly','style'=>'width: 130px;')) ?>
                </div>
                <div class="span2">
                    <label>一级分类</label>
                    <span id="firstSort"><?php echo $firstSort ?></span>
                    <?php  echo CHtml::dropDownList('complain_maintype',
                        $model->complain_type,
                        $typelist,
                        array(
                            'class'=>'span12',
                            'ajax' => array(
                                'type'=>'POST', //request type
                                'url'=>Yii::app()->createUrl('complain/getsubtype'),
                                'update'=>'#sub_type', //selector to update
                                'data'=>array('complain_maintype'=>'js:$("#complain_maintype").val()')
                            ))
                    );?>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span-12">
                <div class="span10">
                    <label>投诉详情</label>
                    <?php echo CHtml::textArea('detail',$model->detail,array('class'=>'input-xlarge','rows'=>'5','style'=>'width: 800px;','readonly'=>'readonly'));?>
                </div>
                <br />
                <div class="span2">
                    <label>二级分类</label>
                    <span id="secondSort"></span><?php echo $secondSort; ?></span>
                    <?php echo CHtml::dropDownList('sub_type',$secondComplain, $secondtypelist,array('class'=>'span12')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    <h1>投诉处理</h1>
</div>
<div class="search-form thumbnail">
    <div class="container-fluid">
        <!--        单选框-->
        <div class="row-fluid">
            <div class="span12">
                <div class="span4">
                    <?php
                    echo $form->radioButtonList(
                        $model,
                        'pnode',
                        array(1=>'联系司机',2=>'未联系上客人',3=>'已联系上客人',4=>'估损',7=>'处理',5=>'疑难案件',6=>'诉讼',8=>'完结'),
                        array('separator' => '&nbsp;&nbsp;',
                            'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:5px;'))
                    )
                    ?>
                    <hr>
                    <label>处理纪要:</label>
                    <?php echo CHtml::textArea('mark',null,array('class'=>'input-xlarge','rows'=>'4','style'=>'width: 350px;'));?><br><br>
                    <a class="btn btn-info span2" name="nodesubmit_btn" id="nodesubmit_btn">提&nbsp;&nbsp;交</a>
                </div>
                <!--        短信-->
                <div class="span2">
                    <label>接收号码</label>
                    <?php echo CHtml::textField('phone',$model->phone,array('class'=>'span12','style'=>'width: 200px;','placeholder'=>'接收号码')); ?>
                    <label>短信内容</label>
                    <?php echo CHtml::textArea('smscontent',null,array('class'=>'span12','rows'=>'6','style'=>'width: 200px;'));?>
                    <br><br>
                    <a class="btn btn-info span6" name="sendsms_btn" id="sendsms_btn">发送短信</a>
                </div>
                <!--        关闭确认投诉-->
                <div class="span6">
                    <div class="row-fluid">
                        <ul class="thumbnails">
                            <li class="span5">
                                <div class="thumbnail">
                                    <div class="caption">
                                        <div class="row-fluid">
                                            <div class="span11">
                                                <label>客户 <?php echo $vip;?></label>
                                                <input type="text" class="input span13" name="binding_phone" id="binding_phone"     value="<?php echo $recoupModel->recoup_customer; ?>" placeholder="VIP主卡号或者手机号码">
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <div class="span6">
                                                <label>金额</label>
                                                <input type="text" class="input span10" name="vip_cash" id="vip_cash" placeholder="充扣" value='<?php if($recoupModel->recoup_type!=2) echo $recoupModel->amount_customer; ?>'>
                                            </div>
                                            <div class="span6" style='display'>
                                                <label>优惠券</label>
                                                <select name="bonus" id="bonus" class="span9">
                                                    <option value="0">请选择</option>
                                                    <option value="10" <?php if($recoupModel->recoup_type==2 && $recoupModel->amount_customer==10) echo 'selected="selected"';  ?> >10优惠券</option>
                                                    <option value="20" <?php if($recoupModel->recoup_type==2 && $recoupModel->amount_customer==20) echo 'selected="selected"';  ?>>20优惠劵</option>
                                                    <option value="39" <?php if($recoupModel->recoup_type==2 && $recoupModel->amount_customer==39) echo 'selected="selected"';  ?>>39优惠券</option>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="row-fluid">
                                            <div class="span10">
                                                <input type="radio" value="1" name="cus_process_type"  class="span1" checked> 不处理
                                                <input type="radio" value="2" name="cus_process_type"  class="span1" <?php if($recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE2 || $recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND3 || $recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND4) echo "checked"; ?> > 补偿
                                                <input type="radio" value="3" name="cus_process_type"  class="span1" <?php if($recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE3 || $recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND3 || $recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND4) echo "checked"; ?> > 扣款

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="span5">
                                <div class="thumbnail">
                                    <div class="caption">
                                        <div class="row-fluid" >
                                            <div class="span10">
                                                <label>司机</label>
                                                <input type="text" class="input span10" name="new_driver_id" id="new_driver_id" placeholder="司机工号" value="<?php echo $recoupModel->driver_id ?>">
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <div class="span8">
                                                <label>金额</label>
                                                <input type="text" class="input span8" name="driver_cash" id="driver_cash"
                                                       placeholder="信息费充扣" value="<?php echo $recoupModel->amount_driver; ?>">
                                            </div>
                                        </div>
                                        <div class="row-fluid">
                                            <div class="span10">
                                                <input type="radio" value="1"  name="dri_process_type" class="span1" checked> 不处理
                                                <input type="radio" value="2"  name="dri_process_type" id="dri_recoup" class="span1" <?php if($recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE4 || $recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND3||$recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND3) echo "checked"; ?> > 补偿
                                                <input type="radio" value="3"  name="dri_process_type" id="dri_deduct" class="span1" <?php if($recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE5 || $recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND4||$recoupModel->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND4) echo "checked"; ?> > 扣款

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <p>
                    <div class="row-fluid">
                        <div class="span4">
                            <input type="hidden" name="re" value="<?php echo $re ?>">
                            <input type="hidden" name="cid" value="<?php echo $cid ?>">
                            <a class="btn btn-info span9" name="close_btn" id="close_btn">关闭投诉</a>
                        </div>
                        <div class="span4">
                            <input type="hidden" name="confirm_btn_d" value="" id="confirm_btn_d_id">
                            <a  class="btn btn-success span9" name="confirm_btn" id="confirm_btn">确认投诉</a>
                        </div>
                    </div>
                    </p>
                </div>

            </div>

        </div>
    </div>
</div>
<br> <a  class="btn btn-success span2" style="vertical-align:middle;" name="close_btn_process" id="close_btn_process">关&nbsp;&nbsp;闭</a><br><br>
<?php $this->endWidget(); ?>
<div class="search-form thumbnail">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div class="span2">
                    <br>
                    <a  class="btn btn-success span9"  name="complaindetail_btn" id="complaindetail_btn">投诉处理详情信息</a><br><br>
                    <a  class="btn btn-success span9"  name="careinfo_btn" id="careinfo_btn">交通事故案件信息</a><br><br>
                    <a  class="btn btn-success span9"  name="carinsurance_btn" id="carinsurance_btn">客户车辆保险信息</a><br><br>
                    <a  class="btn btn-success span9"  name="accidentexpense_btn" id="accidentexpense_btn">交通事故涉及费用</a><br><br>
                    <a  class="btn btn-success span9"  name="complaindata_btn" id="complaindata_btn">案件资料</a><br><br>
                    <a  class="btn btn-success span9"  name="complainfeedback_btn" id="complainfeedback_btn">查看司机反馈</a><br><br>
                </div>
                <div class="span10">
                    <div class="search-form thumbnail">
                        <div id="detail_div">
                            <div id="complaindetailDiv" style="display: none">
                            </div>
                            <div id="traffic_accidentDiv" style="display: none">
                                <?php echo $this->renderPartial("traffic_accident", array("taModel" => $taModel,'cid'=> $cid )) ?>
                            </div>
                            <div id="custcar_insureDiv" style="display: none">
                                <?php echo $this->renderPartial("custcar_insure", array("ciModel" => $ciModel,'cid'=> $cid )) ?>
                            </div>
                            <div id="caccident_costDiv" style="display: none">
                                <?php echo $this->renderPartial("caccident_cost", array("ccModel" => $ccModel,'cid'=> $cid )) ?>
                            </div>
                            <div id="complaindataDiv" style="display: none">
                                <div><span>案件资料区</span><span style="float: right;">资料文件：<input type="file" id="complain_material_upload" name="complain_material_upload" /></span></div>
                                <div style="height: 300px;padding: 20px 0 0 20px;" id="material_list"></div>
                                <div style="padding: 0 0 0 20px;"><input type="button" id="complain_material_delete" value="删除" /></div>
                            </div>
                            <div id="feedbackDiv" style="display: none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$cs=Yii::app()->clientScript;
$cs->registerScriptFile(SP_URL_IMG.'ajaxfileupload.js',CClientScript::POS_END);
?>
<script type="text/javascript">
    $(document).ready(function(){
        var isLook = '<?php echo $isLook ?>';
        if(isLook){
            //说明点击的是查看按钮进入 页面为只读页面
            $("#close_btn").hide();
            $("#confirm_btn").hide();
            $("#sub_type").hide();
            $("#complain_maintype").hide();
            $("#mark").val("<?php echo $recoupModel->mark; ?>");
        }else{
            $("#firstSort").hide();
            $("#secondSort").hide();

        }
//        $("#complaindetail_btn").onclick();
        $('#complaindetail_btn').trigger("click");
    });
    //发送短信
    $("#sendsms_btn").click(function(){
        var cid = '<?php echo $model->id ?>';//客户投诉id 既custmoer_complain的id
        var _phone = $("#phone").val();//电话号码
        var _smscontent = $("#smscontent").val();//短信内容
        var regPartton=/1[3-8]+\d{9}/;
        if(!_phone || _phone==null){
            alert("手机号码不能为空");
            return false;
        }else if(_phone.length != 11 || !regPartton.test(_phone)) {
            alert("手机号码格式不正确！");
            $("#phpne").focus();
            return false;
        }
        if(!_smscontent){
            alert("短信内容不能为空");
            $("#smscontent").focus();
            return false;
        }
        var sendSms_url = '<?php echo Yii::app()->createUrl('complain/sendSms');?>';
        $.get(
            sendSms_url, {'cid':cid,'phone' : _phone, 'smsContent':_smscontent},
            function(datas){
                if(datas){
                    alert("短信发送成功!");
                }else{
                    alert("短信发送失败请重新发送");
                }
            }
        );
    });
    //投诉处理提交按钮
    $("#nodesubmit_btn").click(function(){
        pnodeDeal()
    });
    //节点分类设置和节点提交的处理
    function pnodeDeal(){
        var cid = '<?php echo $model->id ?>';
        var maintType = $("#complain_maintype option:selected").val();//一级分类value
        var sub_type = $("#sub_type option:selected").val();//二级分类value
        var maintTypeText = $("#complain_maintype option:selected").text();//一级分类text
        var sub_typeText = $("#sub_type option:selected").text();//二级分类text

        var pnode = $('input[name="CustomerComplain[pnode]"]:checked').val();//选中的节点
        if(pnode == undefined){
            alert("请选择一个处理节点！");
            return false;
        }
        var mark = $("#mark").val().substr(0,500);
        if(maintType > -1){
            //选择一级分类必须选择二级分类
            if(sub_type < 0){
                alert("请选择二级分类");
                return false;
            }
        }
        if(mark.length == 0){
            alert("处理纪要不能为空！");
            $("#mark").focus();
            return false;
        }
        var params = {"maintType":maintType,"maintTypeText":maintTypeText,"sub_type":sub_type,"sub_typeText":sub_typeText,
            "pnode":pnode,"mark":mark,"cid":cid};
        var sendSms_url = '<?php echo Yii::app()->createUrl('complain/dealNode');?>';
        $.get(
            sendSms_url,
            params,
            function(datas){
                if(datas){
                    alert("投诉处理成功!");
                }else{
                    alert("投诉处理失败！");
                }
            }
        );
    }

    //关闭按钮返回上一步
    $('#close_btn_process').click(function(){
        history.back();
    });
     //关闭投诉
    $('#close_btn').click(function(){
        var val=validate();
        $('#confirm_btn_d_id').val('');
        if (val) {
            $.confirm({
                'title'		: '是否完结此投诉',
                'message'	: '关闭投诉的同时，是否完结此投诉呢?',
                'buttons'	: {
                    '是'	: {
                        'class'	: 'blue',
                        'action': function(){
                            $('#closing').val(1);
                            if(val){
                                $('#complain-confirm-form').attr('action','<?php echo Yii::app()->createUrl('complain/close') ?>');
                                $('#complain-confirm-form').submit();
                                $('#close_btn').attr('disabled',true);
                            }
                        }
                    },
                    '否'	: {
                        'class'	: 'gray',
                        'action': function(){
                            $('#closing').val(0);
                            if(val){
                                $('#complain-confirm-form').attr('action','<?php echo Yii::app()->createUrl('complain/close') ?>');
                                $('#complain-confirm-form').submit();
                                $('#close_btn').attr('disabled',true);
                            }

                        }	// Nothing to do in this case. You can as well omit the action property.
                    }
                }
            });
        }

    });
    //确认投诉
    $('#confirm_btn').click(function () {
        //确定投诉进入司机扣分
        $('#confirm_btn_d_id').val('1');
        var driver_id = $('#driver_id').val();
        var order_id= $('#order_id').val();
        if (driver_id == '' || order_id==0) {
            if (!confirm('没有定位订单，确认提交？'))
                return false;
        }
        var money = $('#vip_cash').val();
        if(money > 1000 ){
            alert('补偿金额最多1000元');
            return false;
        }

        var val = validate();
        var val2 = validate_rd();
        if (val && val2) {
            $.confirm({
                'title'		: '是否完结此投诉',
                'message'	: '确认投诉的同时，是否完结此投诉呢?',
                'buttons'	: {
                    '是'	: {
                        'class'	: 'blue',
                        'action': function(){
                            $('#closing').val(1);
                            if (val && val2) {
                                $("#complain-confirm-form").attr("action", "<?php echo Yii::app()->createUrl('complain/confirm') ?>");
                                $('#complain-confirm-form').submit();
                                $('#confirm_btn').attr('disabled', true);
                            }
                        }
                    },
                    '否'	: {
                        'class'	: 'gray',
                        'action': function(){
                            $('#closing').val(0);
                            if (val && val2) {
                                $("#complain-confirm-form").attr("action", "<?php echo Yii::app()->createUrl('complain/confirm') ?>");
                                $('#complain-confirm-form').submit();
                                $('#confirm_btn').attr('disabled', true);
                            }

                        }	// Nothing to do in this case. You can as well omit the action property.
                    }
                }
            });
        }



    });

    function validate(){
        var flag=true;
        var sub_type= $('#sub_type').val();
        var _mark = $("#mark").val().substr(0,500);
        if(sub_type<0){
            alert('请选择二级分类');
            flag=false;
        }
        if(_mark == ''){
            alert('请填写处理纪要内容');
            flag=false;
        }
        return flag;
    }
    function validate_rd(){
        var flag=true;
        var binding_phone= $('#binding_phone').val();
        var vip_cash= $('#vip_cash').val();
        var bonus= $('#bonus').val();
        var cus_type=$("input[name='cus_process_type']:checked").val();
        //客户补偿
        if(cus_type!=1){
            if(binding_phone==''){
                alert('请填写补偿用户VIP信息');
                flag=false;
            }
            if(vip_cash=='' && bonus==0){
                alert('请选择补偿方式');
                flag=false;
            }
            if(vip_cash!='' && bonus>0){
                alert('VIP充扣/优惠券不能同时选择');
                flag=false;
            }

        }

        var driver_id= $('#new_driver_id').val();
        var driver_cash= $('#driver_cash').val();
        var dri_type=$("input[name='dri_process_type']:checked").val();
        if(dri_type!=1){
            if(driver_id==''){
                alert('请填写司机工号');
                flag=false;
            }
            if(driver_cash==''){
                alert('请填写司机补偿扣款金额');
                flag=false;
            }

        }
        return flag;
    }


    $("#complaindetail_btn").click(function(){
        var cid = '<?php echo $model->id ?>';
        var sendSms_url = '<?php echo Yii::app()->createUrl('complain/dealDetails');?>';
        $.get(
            sendSms_url, {'cid':cid},
            function(datas){
                $("#complaindetailDiv").show();
                $("#complaindetailDiv").html(datas);

                $("#caccident_costDiv").hide();
                $("#traffic_accidentDiv").hide();
                $("#custcar_insureDiv").hide();
                $("#complaindataDiv").hide();
                $("#feedbackDiv").hide();
            }
        );
    });
    //交通事故信息展示
    $("#careinfo_btn").click(function(){
        $("#traffic_accidentDiv").show();

        $("#complaindetailDiv").hide();
        $("#custcar_insureDiv").hide();
        $("#caccident_costDiv").hide();
        $("#complaindataDiv").hide();
        $("#feedbackDiv").hide();
    });
    //客户车辆保险信息展示
    $("#carinsurance_btn").click(function(){
        $("#custcar_insureDiv").show();

        $("#complaindetailDiv").hide();
        $("#traffic_accidentDiv").hide();
        $("#caccident_costDiv").hide();
        $("#complaindataDiv").hide();
        $("#feedbackDiv").hide();
    });
    //交通事故涉及费用
    $("#accidentexpense_btn").click(function(){
        $("#caccident_costDiv").show();

        $("#complaindetailDiv").hide();
        $("#traffic_accidentDiv").hide();
        $("#custcar_insureDiv").hide();
        $("#complaindataDiv").hide();
        $("#feedbackDiv").hide();
    });
    //案件资料
    $("#complaindata_btn").click(function(){
        var cid = '<?php echo $model->id ?>';
        var url = '<?php echo Yii::app()->createUrl('complain/getcomplainmaterial');?>';
        $.get(url, {'cid':cid}, function(data){

                $("#complaindataDiv").show();
                $("#material_list").html(data);

                $("#complaindetailDiv").hide();
                $("#caccident_costDiv").hide();
                $("#traffic_accidentDiv").hide();
                $("#custcar_insureDiv").hide();
                $("#feedbackDiv").hide();
        });
    });
    //查看司机反馈
    $("#complainfeedback_btn").click(function(){
        var cid = '<?php echo $model->id ?>';
        var url = '<?php echo Yii::app()->createUrl('complain/feedback');?>';
        $.get(url, {'cid':cid}, function(data){
                $("#feedbackDiv").show();
                $("#feedbackDiv").html(data);

                $("#complaindetailDiv").hide();
                $("#caccident_costDiv").hide();
                $("#traffic_accidentDiv").hide();
                $("#custcar_insureDiv").hide();
                $("#complaindataDiv").hide();
            }
        );
    });

    $("#complain_material_upload").live('change',function(){
        var upload_url = 'index.php?r=complain/addmaterial&cid=<?php echo $cid; ?>';
        $.ajaxFileUpload({
            url: typeof upload_url != 'undefined' ? upload_url : '',
            type: 'post',
            secureuri: false,
            fileElementId: 'complain_material_upload',
            dataType: 'json',
            success: function(data, status) {
                var succ = data.succ,
                    errmsg = data.errmsg;
                if (succ == 1) {
                    var cid = '<?php echo $cid; ?>';
                    var url = '<?php echo Yii::app()->createUrl('complain/getcomplainmaterial');?>';
                    $.get(url, {'cid':cid}, function(data){
                        $("#material_list").html(data);
                    });
                } else {
                    alert('上传资料失败，原因：' + errmsg);
                }
            },
            error: function(data, status, e) {
                alert(e);
            }
        });
        return false;
    });

    $('#complain_material_delete').click(function(){
        var chk_value =[];
        $('input[name="checkbox_material"]:checked').each(function(){
            chk_value.push($(this).val());
        });
        if (chk_value.length == 0) {
            alert('请选择要删除的材料！');
        } else {
            var cid = '<?php echo $cid; ?>';
            var url = '<?php echo Yii::app()->createUrl('complain/delmaterial');?>';
            $.get(url, {'cid':cid,mids:chk_value}, function(ret){
                if (ret.succ==1) {
                    var url = '<?php echo Yii::app()->createUrl('complain/getcomplainmaterial');?>';
                    $.get(url, {'cid':cid}, function(data){
                        $("#material_list").html(data);
                    });
                } else {
                    alert('删除失败了');
                }
            },'json');
        }
    });
</script>