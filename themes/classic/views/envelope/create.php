<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'envelope-form',
    'enableAjaxValidation' => false,
));
?>

<?php echo $form->errorSummary($model); ?>

<div class="row-fluid">
    <div class="row-fluid">
        <div class="span5">
            <?php echo $form->labelEx($model, 'start_date'); ?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'EnvelopeInfo[start_date]',
                // additional javascript options for the date picker plugin
                'mode' => 'datetime',
                'options' => array(
                    'showAnim' => 'fold',
                    'dateFormat' => 'yy-mm-dd',
                ),
                'htmlOptions' => array(
                    'style' => 'height:20px;',
                ),
                'value' =>'',
                'language' => 'zh',
            ));
            ?>
            <?php echo $form->error($model, 'start_date'); ?>
        </div>

        <div class="span6">
            <?php echo $form->labelEx($model, 'end_date'); ?>
            <?php
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'EnvelopeInfo[end_date]',
                'mode' => 'datetime',
                // additional javascript options for the date picker plugin
                'options' => array(
                    'showAnim' => 'fold',
                    'dateFormat' => 'yy-mm-dd',
                ),
                'htmlOptions' => array(
                    'style' => 'height:20px;',
                ),
                'value' => '',
                'language' => 'zh',
            ));
            ?>
            <?php echo $form->error($model, 'end_date'); ?>
        </div>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'envelope_name'); ?>
        <?php echo $form->textField($model, 'envelope_name', array('size' => 60, 'maxlength' => 20)); ?>
        <?php echo $form->error($model, 'envelope_name'); ?>
    </div>

    <div class="row-fluid">
        <?php echo $form->labelEx($model, 'envelope_type'); ?>
        <?php echo $form->dropDownList($model, 'envelope_type', $dict) ?>
        <?php echo $form->error($model, 'envelope_type'); ?>
    </div>


    <div class="row-fluid" style='margin-bottom:10px;'>
        <?php echo $form->labelEx($model, 'envelope_role'); ?>
        <?php
        $envelope_role = array(
            array(
                'code' => '2',
                'percent' => '0'
            ),
            array(
                'code' => '5',
                'percent' => '0'
            ),
            array(
                'code' => '10',
                'percent' => '0'
            ),
        );

        foreach ($envelope_role as $role) {

            ?>
            <?php echo $role['code']; ?>元 &nbsp; <input type="text" code="<?php echo $role['code'] ?>" id="envelope_role_<?php echo $role['code'] ?>"
                                                        onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,\'\')" name="<?php $role['percent']; ?>"
                                                        style="width: 40px">% &nbsp;&nbsp;&nbsp;&nbsp;
        <?php
        }
        ?>
        <?php echo $form->error($model, 'envelope_role'); ?>
    </div>
    <div class="row-fluid">
        <input id="hiddenRole" name="EnvelopeInfo[envelope_role]" type="hidden" value="">
        新增金额: <input id="addAcount" value=""  style="width: 60px" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">&nbsp;&nbsp;<?php echo CHtml::button('确定', array('id'=>'btnAddAcount')) ?>
    </div>
    <div class="row-fluid">
        <br/>
       选择城市
    </div>

    <div class="row-fluid">
<input type="hidden" id="selectCity" name="selectCity" value="">
        <span class="span8" style="margin-left: -2px;">
            <input type="checkbox" name="all" id="che_all" value="1">&nbsp;&nbsp;全选
                <br />
            <?php

            $citys = $dictCity;
            unset($citys[0]);
            $count = 0;
            foreach ($citys as $key=>$item){
                //$checked = in_array($key,$city)?true:false;
                $checked=false;
                echo CHtml::checkBox("city[]",$checked,array("value"=>$key,"id"=>'envelope_city_'.$key,'class'=>'city_id'))."&nbsp;&nbsp;".$item.'&nbsp;&nbsp;&nbsp;&nbsp;';
                $count++;
                if($count%9 == 0)
                    echo "<br />";
            }
            ?>
            <br/><br/>
        </span>

    </div>

</div>


<div class="row-fluid" style="margin-top:20px;">
    <div class="span12">
        <div class="span4"></div>
        <div class="span8">
            <?php echo CHtml::button('保存', array('class' => 'btn btn-primary span3', 'id' => 'EnvelopeSbt')); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo CHtml::button('取消', array('class' => 'btn btn-danger span3', 'id' => 'EnvelopeCancel')) ?>
        </div>
    </div>
</div>
<!-- form -->

<?php $this->endWidget(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#btnAddAcount").click(function () {
            var value=$('#addAcount').val();
            if(value==0|| value>100){
                alert('金额只能0到100之间的整数!');
                return;
            }


            if($("#envelope_role_"+value).length > 0){
                alert('该金额已经存在!');
                return;
            }

            var add='   '+value+'元 <input code="'+value+'" id="envelope_role_'+value+'" style="width: 40px" name="envelope_role_'+value+'" >%';

            $("#envelope_role_"+value).live("keyup",function(){
                this.value=this.value.replace(/\D/g,'');
            });


            var parent=$(this).parent().prev();
            parent.append(add);
        });

        $("#EnvelopeSbt").click(function () {

            if($('#EnvelopeInfo_start_date').val()==''|| $('#EnvelopeInfo_start_date').val()==''){
                alert('请选择时间!');
                return;
            }
            if($('#EnvelopeInfo_envelope_name').val()==''){
                alert('红包名称不能为空!');
                return;
            }
            var length=$('#EnvelopeInfo_envelope_name').val().length;
            if(length>10){
                alert('红包名称长度不能超过10!');
                return;
            }
            var role=$('input[id^=envelope_role_]');
            var num=0;
            var enve_role='';
            var sum=0;
            role.each(function(index,data){
                num+=Number($(data).val());
                if(enve_role!=''){
                    enve_role+='-';
                }
                enve_role+=$(data).attr('code')+':'+$(data).val();

                sum+=(Number($(data).attr('code'))*Number($(data).val()))/100;
            });
            if(num!=100){
                alert('百分比之和必须等于100!');
                return;
            }

            $('#hiddenRole').val(enve_role);


            var cityList=$('input[id^=envelope_city_]');

            var enve_city='';
            cityList.each(function(index,data){
                if($(this).is(':checked')) {
                    enve_city +='-'+ $(data).attr('value');
                }
            });
            if(enve_city==''){
                alert('请选择城市!');
                return;
            }
            enve_city=enve_city.substring(1);
            $('#selectCity').val(enve_city);


            if(window.confirm('该红包平均金额'+sum+'元，你确定要继续创建红包么？')){
                $('#envelope-form').submit();
            }

        });


        $('#che_all').click(function(){
            if($(this).is(':checked')){
                $("input[id^=envelope_city_]").each(function(){
                    $(this).attr("checked","true")
                });
            }else{
                $("input[id^=envelope_city_]").each(function(){
                    $(this).removeAttr("checked");
                });
            }
        })


        $('#EnvelopeCancel').click(function(){
            history.go(-1);
        })
    });
</script>