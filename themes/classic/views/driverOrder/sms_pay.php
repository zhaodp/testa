<h1>催付款</h1>
<hr/>
<div class="driver_info">
    <span class="info">姓名 : <?php echo $data->driver_name;?></span>
    <span class="info">手机号码:<?php echo $data->driver_phone;?></span>
    <span class="info">订单编号:<?php echo $data->order_number;?></span>
</div>

<div class="desc">
    <textarea id="sms_content" style="width:500px;height:100px;">
        师傅你好，感谢你成为e代驾的签约合作司机，收到工服及工卡后，你就可以上线接单了。现在你的工服及工卡已经准备好了，请你尽快登录司机端软件并在线充值付300元以上，200元装备押金将从信息费中扣除（解约时会退还）。收到你的付款后，我们将在3-5个工作日内发货。
    </textarea>
</div>
<input type="hidden" id="id" value="<?php echo $data->id;?>">
<div class="subm">
    <input type="submit" value="发送" class="btn btn-success" id="submit_btn">
    <input type="button" value="取消" class="btn" id="cancel">
</div>


<style>

    .driver_info{
        padding:20px;

    }
    .driver_info .info{
        padding-right:20px;
    }
    .desc{
        padding:20px;
    }
    .rednotice{
        color:red;
        padding:20px;
    }
    .subm{
        padding:20px;
    }
    .btn{
        margin-right:50px;
    }
</style>
<script>
    $(function(){
        $("#submit_btn").click(function(){
            var id = $('#id').val();
            var content = $('#sms_content').val();
            if(!content){
                alert('请填写崔付款短信内容后发送');return false;
            }

            jQuery.ajax(
                {
                    url:'<?php echo Yii::app()->createUrl('/driverOrder/smsPay',array('id'=>0));?>',
                    data:{
                        id : id,
                        content:content
                    },
                    type:"POST",
                    dataType:"json",
                    success:function(d) {
                        var res = eval(d);
                        if (res.code == 0) {
                            window.parent.$('#mydialog').dialog('close');
                            window.parent.$('copy_frame').attr('src','');
                            alert(d.msg);
                            //location.href='<?php echo Yii::app()->createUrl('driverOrder/admin');?>';
                        } else {
                            window.parent.$('#mydialog').dialog('close');
                            window.parent.$('copy_frame').attr('src','');
                            alert(d.msg);
                        }
                    }
                }
            );
        });

        $("#cancel").click(function(){
            $(window.parent.document).find(".ui-dialog-buttonset button").click();
        });
    });



</script>