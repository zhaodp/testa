<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'complain-recoup-form',
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'post',
)); ?>

<div class="container-fluid">
    <div class="row-fluid">

        <!--右侧-->
        <div class="span12">


            <div class="row-fluid">
                <div class="span12">
                    <label>处理意见</label>
                    <?php echo CHtml::textArea('mark',$mark,array('class'=>'input-xlarge','rows'=>'6','style'=>'width: 585px; height: 95px;','disabled'=>'disabled'));?>
                </div>
            </div>

            <div class="row-fluid">
                <ul class="thumbnails">
                    <li class="span5">
                        <div class="thumbnail">
                            <div class="caption">
                                <div class="row-fluid">
                                    <div class="span11">
                                        <label>VIP卡号或客户手机号码</label>
                                        <input type="text" class="input span11"  name="binding_phone" id="binding_phone" value="<?php echo $model->recoup_customer; ?>" placeholder="请输入客户手机号或vip卡号">
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span6">
                                        <label>金额</label>
                                        <input type="text" class="input span10" name="vip_cash" id="vip_cash" placeholder="VIP充扣" value="<?php if($model->recoup_type!=2) echo $model->amount_customer; ?>">
                                    </div>
                                    <div class="span6" style='display:none'>
                                        <label>优惠券</label>
                                        <select name="bonus" id="bonus" class="span9">
                                            <option value="0">请选择</option>
                                            <option value="10" <?php if($model->recoup_type==2 && $model->amount_customer==10) echo 'selected="selected"';  ?> >10优惠券</option>
                                            <option value="20" <?php if($model->recoup_type==2 && $model->amount_customer==20) echo 'selected="selected"';  ?>>20优惠劵</option>
                                            <option value="39" <?php if($model->recoup_type==2 && $model->amount_customer==39) echo 'selected="selected"';  ?>>39优惠券</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="row-fluid">
                                    <div class="span10">
                                        <input type="radio" value="1" name="cus_process_type"  class="span1" checked> 不处理
                                        <input type="radio" value="2" name="cus_process_type"  class="span1" <?php if($model->process_type==CustomerComplainRecoup::PROCESS_TYPE2 || $model->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND3 || $model->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND4) echo "checked"; ?> > 补偿
                                        <input type="radio" value="3" name="cus_process_type"  class="span1" <?php if($model->process_type==CustomerComplainRecoup::PROCESS_TYPE3 || $model->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND3 || $model->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND4) echo "checked"; ?> > 扣款

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
                                        <input type="text" class="input span10"  name="new_driver_id" id="new_driver_id" placeholder="司机工号" value="<?php echo $model->recoup_driver ?>">
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span8">
                                        <label>金额</label>
                                        <input type="text" class="input span8" name="driver_cash" id="driver_cash"
                                               placeholder="信息费充扣" value="<?php echo $model->amount_driver; ?>">
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span10">
                                        <input type="radio" value="1"  name="dri_process_type" class="span1" checked> 不处理
                                        <input type="radio" value="2"  name="dri_process_type" id="dri_recoup" class="span1" <?php if($model->process_type==CustomerComplainRecoup::PROCESS_TYPE4 || $model->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND3||$model->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND3) echo "checked"; ?> > 补偿
                                        <input type="radio" value="3"  name="dri_process_type" id="dri_deduct" class="span1" <?php if($model->process_type==CustomerComplainRecoup::PROCESS_TYPE5 || $model->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND4||$model->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND4) echo "checked"; ?> > 扣款

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
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    <input type="hidden" name="mark" value="<?php echo $mark ?>">
                    <a  class="btn btn-success span9 " name="confirm_btn" id="confirm_btn" >确认</a>
                    
                </div>
                <div class="span4">
<!--                    <a  class="btn btn-success span9" name="confirm_btn" id="confirm_btn">确认</a>-->
                <a class="btn btn-info span9" name="reject_btn" id="reject_btn">驳回</a>
                </div>

            </div>
            </p>
        </div>

    </div>
</div>


<?php $this->endWidget(); ?>
<script type="text/javascript">
    <!--
    $(function(){

        //确认投诉
        $('#confirm_btn').click(function(){
            if (!confirm('确认提交？'))
                return false;
            var money = $('#vip_cash').val();
            if(money > 1000){
                alert('补偿金额最高1000元');
                return false;
            }
            var val2=validate_rd();
            if(val2){
                $("#complain-recoup-form").attr("action", "<?php echo Yii::app()->createUrl('complain/dorecoup') ?>");
                $('#complain-recoup-form').submit();
                $('#confirm_btn').attr('disabled',true);
            }
        });

        $('#reject_btn').click(function(){
            if (!confirm('确认驳回？'))
                return false;
            $("#complain-recoup-form").attr("action", "<?php echo Yii::app()->createUrl('complain/rejectdorecoup') ?>");
            $('#complain-recoup-form').submit();
            $('#reject_btn').attr('disabled',true);
            
        });


        function validate(){
            var flag=true;
            var sub_type= $('#sub_type').val();
            var mark= $('#mark').val().length;
            if(sub_type<0){
                alert('请选择分类');
                flag=false;
            }
            if(mark==0){
                alert('请填写处理意见');
                flag=false;
            }
            return flag;
        }
        function validate_rd(){
            var flag=true;
            var vip_cash= $('#vip_cash').val();
            var bonus= $('#bonus').val();
            var cus_type=$("input[name='cus_process_type']:checked").val();
            //客户补偿
            if(cus_type!=1){
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

    });
    //-->
</script>


