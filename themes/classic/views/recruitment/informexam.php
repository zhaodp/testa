<?php
if ($action == DriverRecruitment::SMS_TYPE_EXAM) {
?>
面试时间：
<?php
$this->widget('zii.widgets.jui.CJuiDatePicker',array(
    'attribute'=>'visit_time',
    'language'=>'zh_cn',
    'id' => 'batch',
    'name'=>'batch',
    'options'=>array(
        'showAnim'=>'fold',
        'showOn'=>'both',
        'buttonImageOnly'=>true,
        //'minDate'=>'new Date()',
        'dateFormat'=>'yymmdd',
        'changeYear'=>true,
        'changeMonth'=> true,
    ),
    'htmlOptions'=>array(
        'style'=>'width:110px',
    ),
));
?>
<span id="waring"></span>
<?php
}
?>
<br>
短信内容
<textarea rows="3" cols="80" class="span12" id="sms_content"></textarea>
<input type="submit" value="提交" class="btn btn-success" id="submit_btn">

<?php
$driver_data = $dataProvider->getData();
?>
<?php if (is_array($driver_data) && count($driver_data )) {?>
<table class="table table-striped">
    <tr>
        <td>报名流水</td>
        <td>姓名</td>
        <td>身份证号</td>
        <td>居住城市</td>
        <td>司机状态</td>
        <td>操作</td>
    </tr>
    <?php
    foreach ($driver_data as $data) {
    ?>
    <tr id="<?php echo $data['id'];?>" class="list">
        <td><?php echo Yii::app()->controller->getRecruitmentQueueNumber($data['id'], $data['city_id']);?></td>
        <td><?php echo $data['name'];?></td>
        <td><?php echo $data['id_card'];?></td>
        <td><?php echo Yii::app()->controller->getRecruitmentCity($data['city_id']);?></td>
        <td><?php echo Yii::app()->controller->getRecruitmentStatus($data['status']);?></td>
        <td><a href="javascript:void(0)" class="deluser">删除</a></td>
    </tr>
    <?php } ?>
</table>
<?php }?>
<script>
$(function(){

    var other_id = <?php echo count($disqualification);?>;

    var other_id_list = <?php echo is_array($disqualification) && count($disqualification) ? json_encode($disqualification) : '{}';?>;

    jQuery.each(other_id_list, function(i,v){
        jQuery('#'+v).css('color', 'red');
        jQuery('#'+v).attr('func', 'no');
    });

    $("#submit_btn").click(function(){
		var arrID = new Array();
		var num = $(".table tr").length;
		var batch=$("#batch").val();
		var sms_content = $('#sms_content').val();
        var action =  '<?php echo $action;?>';
		//if(other_id.length!=0 && action=='exam'){ alert("目前有"+other_id.length+"位司机目前不能进行路考，请重新确认列表里的司机，");return false;}
		if(sms_content.length<5){ alert("请输入短信内容");return false;}

        jQuery('.list').each(function(){
            if (jQuery(this).attr('func') != 'no') {
                arrID.push(jQuery(this).attr('id'));
            }
        });
        if (arrID.length <= 0) {
            alert('没有符合条件的司机');
            return false;
        }
        if (action == <?php echo DriverRecruitment::SMS_TYPE_EXAM;?> && batch=='') {
            alert('请选择面试日期');
            return false;
        }
        jQuery.post(
            '<?php echo Yii::app()->createUrl('/recruitment/sendExamSMS');?>',
            {
                batch : batch,
                id : arrID,
                sms_content : sms_content,
                action : action
            },
            function(d) {
                if (d == 1) {
                    alert("发送成功。");
                    $(window.parent.document).find(".ui-dialog-buttonset button").click();
                } else {
                    alert("操作失败！");
                }
            }
        );
	});

    $(".deluser").click(function(){
		//curr_num = $(".table tr").index($(this).parent().parent());
		id = jQuery(this).parents('tr').attr('id');
	    if (parseInt(id) > 0) {
            jQuery('#'+id).remove();
        }
	});

    //查看选择批次下是不是已经有面试
    jQuery('#batch').datepicker({
        dateFormat : 'yymmdd',
        onSelect: function(dateText, inst) {
            var batch = dateText;
            jQuery.get(
                '<?php echo Yii::app()->createUrl('/recruitment/ajax');?>',
                {
                    action : 'getBatchCount',
                    batch : batch
                },
                function(d) {
                    if (d['msg'] > 0) {
                        jQuery('#waring').html('<font color="red">所选日期已有面试，确认将下方司机加入此批面试。</font>');
                    } else {
                        jQuery('#waring').html('');
                    }
                },
                'json'
            )
        }
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