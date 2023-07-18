<h1>客户车辆保险信息</h1>
<hr>
<?php $form = $this -> beginWidget('CActiveForm',array(
        'id'=>'custcarIinsure_form'
    )); ?>
    车牌号:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ciModel,'car_number'); ?> <br><br>
    所属保险公司:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ciModel,'insure_company'); ?><br><br>
    被保险人姓名:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <?php echo $form -> textField($ciModel,'insurer_name'); ?><br><br>
    被保险人身份证号码:&nbsp;&nbsp;&nbsp;<?php echo $form -> textField($ciModel,'insurer_cardid'); ?><br><br>
    车辆交强险保单号:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form -> textField($ciModel,'car_salino'); ?><br><br>
    车辆商业险保单号:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $form -> textField($ciModel,'car_businessno'); ?>
    <br>
    <input type="hidden" id="hasId2" name="hasId2">
    <input type="hidden"  name="CustcarInsure[id]" value="<?php echo $ciModel->id ?>">
    <input type="hidden" name="CustcarInsure[customer_id]" value="<?php echo $cid ?>">
    <a class="btn btn-info span2" name="carInsuresubmit_btn" id="carInsuresubmit_btn">提交</a><br>
<?php $this -> endWidget(); ?>

<script type="text/javascript">
    $(document).ready(function(){
        var _id = "<?php echo $ciModel->id ?>";//如果页面进来id不为空则说明已经存在信息
        if(_id){
            $("#hasId2").val(1);//存在数据
        }else{
            $("#hasId2").val(0);//不存在数据
        }
    });
    //异步提交表单处理
    $("#carInsuresubmit_btn").click(function(){
        var carNo = $("#CustcarInsure_car_number").val();
        if(!carNo){
            alert("车牌号必填!");
            $("#CustcarInsure_car_number").focus();
            return false;
        }
        var options = {
            url: '<?php echo Yii::app()->createUrl('complain/custcarInsure') ?>',
            type: 'post',
            dataType: 'text',
            data: $("#custcarIinsure_form").serialize(),
            success: function (data) {
                if (data == 'update'){
                    alert("数据修改成功");
                    $("#hasId2").val(1);//存在数据
                }else if(data){
                    alert("数据保存成功");
                    $("input[name='CustcarInsure[id]']").val(data);
                    $("#hasId2").val(1);//存在数据
                }else{
                    alert("数据库出现异常保存失败!");
                }
            }
        };
        $.ajax(options);
        return false;
    });


</script>