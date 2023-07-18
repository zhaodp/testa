短信内容
<textarea rows="3" cols="80" class="span12" id="sms_content"></textarea>
<input type="submit" value="提交" class="btn btn-success" id="submit_btn">

<?php if ($data) {?>
    <table class="table table-striped">
        <tr>
            <td>报名流水/工号</td>
            <td>姓名</td>
            <td>身份证号</td>
            <td>城市</td>
            <td>状态</td>
            <td>操作</td>
        </tr>
        <?php
        foreach ($data as $detail) {
            ?>
            <tr id="<?php echo $detail['id'];?>" class="list">
                <td><?php echo $detail['id'];?></td>
                <td><?php echo $detail['name'];?></td>
                <td><?php echo $detail['id_card'];?></td>
                <td><?php echo $detail['city'];?></td>
                <td><?php echo $detail['status'];?></td>
                <td><a href="javascript:void(0)" class="deluser">删除</a></td>
            </tr>
        <?php } ?>
    </table>
<?php }?>
<script>
    $(function(){


        $("#submit_btn").click(function(){
            var arrID = new Array();
            var num = $(".table tr").length;
            var sms_content = $('#sms_content').val();
            if(sms_content.length<5){
                alert("请输入短信内容");
                return false;
            }

            jQuery('.list').each(function(){
                arrID.push(jQuery(this).attr('id'));
            });
            if (arrID.length <= 0) {
                alert('没有符合条件的司机');
                return false;
            }

            jQuery.post(
                '<?php echo Yii::app()->createUrl('/material/sendMsg');?>',
                {
                    id : arrID,
                    sms_content : sms_content
                },
                function(d) {
                    if (d == 1) {
                        alert("发送成功。");
                        $(window.parent.document).find(".ui-dialog-buttonset button").click();
                    } else {
                        alert(d);return false;
                        alert("操作失败！");
                    }
                }
            );
        });

        $(".deluser").click(function(){
            id = jQuery(this).parents('tr').attr('id');
            jQuery('#'+id).remove();
        });


    });

    Array.prototype.indexOf = function(val) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] == val) return i;
        }
        return -1;
    };
    Array.prototype.remove = function(val) {
        var index = this.indexOf(val);
        if (index > -1) {
            this.splice(index, 1);
        }
    };

</script>