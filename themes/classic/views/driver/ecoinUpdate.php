<?php
$this->breadcrumbs=array(
    'Vips'=>array('index'),
    'Update',
);
?>
<div class="form span11">
    <div class="row">
        <span class="required">*</span>说明:只能填写-10000到10000之间的整数值
    </div>
    <br>

    <div class="row">
        司机工号：<?php echo $data->driver_id; ?>
    </div>
    <div class="row">
        当前e币：<?php echo $data->total_wealth; ?>
    </div>
    <div class="row">
        增加e币<span class="required">*</span>：<input type='text' name='add_wealth' id="add_wealth" value="0">
    </div>
    <div class="row">
        原因<span class="required">*</span>：<input type='text' name='reason' id="reason">
    </div>
    <input type="submit" value="修改" class="btn btn-success" id="submit_btn">
</div>


<script>
    $(function () {
        $("#submit_btn").click(function () {
            var add_wealth = $.trim($('#add_wealth').val());
            var reason = $.trim($('#reason').val());
            if (add_wealth == "" || add_wealth == '0') {
                alert("e币数量不能为空或0");
                return;
            }
            if (reason == "" || reason == "null") {
                alert("原因不能为空");
                return;
            }
            var add_wealth_num = parseInt(add_wealth);
            if (add_wealth_num > 10000 || add_wealth_num<-10000) {
                alert("奖赏或处罚的e币数不能超过10000");
                return;
            }
            $.ajax({
                type: 'POST',
                url: "<?php echo Yii::app()->createUrl("driver/ecoinUpdate"); ?>",
                data: {driver_id: '<?php echo $data->driver_id;?>',add_wealth: add_wealth_num,reason: reason},
                cache: false,
                success: function (data) {
                    if (data > 0) {
                        alert('修改成功');
                        $.fn.yiiGridView.update('ecoin-driver-grid');
                        //window.parent.$('#update_ecoin_dialog').dialog('close');
                        //$(".search-form form").submit();
                        /*$.fn.yiiGridView.update('ecoin-driver-grid');
                         $('#update_ecoin_dialog').dialog('close');*/
                    } else {
                        alert('修改失败');
                    }
                }
            });
        });
    });
</script>