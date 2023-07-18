<h1>复制订单</h1>
<hr/><div class="driver_info">
<span class="info">姓名 : <?php echo $data->driver_name;?></span>
<span class="info">手机号码:<?php echo $data->driver_phone;?></span>
</div>

<div class="desc">
    为该司机生成一个新的装备工卡订单，新订单的衣服尺码、收货人、收货地址均不变。
    确定要复制订单吗？
</div>
<input type="hidden" id="id" value="<?php echo $data->id;?>">
<div class="rednotice">
    重要提示：此功能只能在装备快递丢失的情况下使用
</div>
<div class="subm">
    <input type="submit" value="确定" class="btn btn-success" id="submit_btn">
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

            jQuery.post(
                '<?php echo Yii::app()->createUrl('/driverOrder/copy',array('id'=>0));?>',
                {
                    id : id
                },
                function(d) {
                    if (d == 1) {
                        window.parent.$('#mydialog').dialog('close');
                        window.parent.$('copy_frame').attr('src','');

                        alert("复制成功。");
                        //window.parent.history.go(0);
                    } else {
                        alert("操作失败！");
                    }
                }
            );
        });

        $("#cancel").click(function(){
            $(window.parent.document).find(".ui-dialog-buttonset button").click();
        });
    });



</script>