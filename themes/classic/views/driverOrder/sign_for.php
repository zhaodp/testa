<h1>已签收</h1>
<hr/>
<div class="driver_info">
    <span class="info">姓名 : <?php echo $data->driver_name;?></span>
    <span class="info">手机号码:<?php echo $data->driver_phone;?></span>
    <span class="info">订单编号:<?php echo $data->order_number;?></span>
</div>

<div class="desc">
    确认签收后，后台将自动对司机进行激活。确定要将此订单设置为“已签收”吗？
</div>
<input type="hidden" id="id" value="<?php echo $data->id;?>">
<div class="rednotice">
    重要提示：使用此功能前，请与司机电话确认装备已收到。
</div>
<div class="subm">
    <input type="submit" value="已签收" class="btn btn-success" id="submit_btn">
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

            jQuery.ajax(
                {
                    url:'<?php echo Yii::app()->createUrl('/driverOrder/signFor',array('id'=>0));?>',
                    data:{
                        id : id
                        },
                    type:"POST",
                    dataType:"json",
                    success:function(d) {
                        var res = eval(d);
                        if (res.code == 0) {
                            window.parent.$('#mydialog').dialog('close');
                            window.parent.$('copy_frame').attr('src','');
                            alert(d.msg);
                            window.parent.history.go(0);
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