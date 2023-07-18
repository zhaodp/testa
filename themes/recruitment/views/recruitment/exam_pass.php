<?php
/**
 * Created by ZhangTingyi
 * User: HP
 * Date: 13-6-5
 * Time: 下午3:00
 * To change this template use File | Settings | File Templates.
 */

$this->pageTitle = '在线考试  - e代驾';

?>
<div class="block">
	<div style="height:90px;"></div>
	<div class="page-header">
	<h2>在线考试</h2>
	</div>
<div class="row">
    <div class="span6" id="container">
        <h4>恭喜您通过了在线考试，公司会在近期以短信方式通知您面试，请注意查收。</h4>
        <h4>您的手机号是<?php echo $driver['mobile'];?>，如果您的手机号填写错误，请点<a href="javascript:void(0)" onclick="jQuery('#frm').show()">更改</a></h4>
        <h4>您也可以根据您的时间选择合适的面试时间，请点<a href="<?php  echo Yii::app()->createUrl("recruitment/queue", array('act'=>'interview', 'id_card'=>$driver['id_card'])); ?>" >这里</a></h4>
    </div>

</div>
<div class="row">
    <div class="span6" stle="width:640">
    <div style="display:none" id="frm">
        <h4>您现在的手机号为 <font color='red' ><?php echo $driver['mobile'];?></font></h4>
        <form id="pfrm">
            <input type="hidden" value="<?php echo $driver['id'];?>" name="id" />
            <input type="hidden" value="changemobile" name="change" />
            <input type='text' name="new_mobile" /><br>
            <input type="button" value="更改手机号码并关闭" onclick="changPhone()"/><br>
        </form>
    </div>
    </div>
</div>
<script>
    function changPhone() {
        var new_phone = jQuery('[name="new_mobile"]').val();
        if (new_phone.length == 0) {
            alert('请输入手机号码');
            return false;
        }
        if (!(/^1[3|4|5|8][0-9]\d{4,8}$/.test(new_phone))) {
            alert('请输入正确的手机号码');
            return false;
        }
        var post_data = jQuery('#pfrm').serialize();
        jQuery.post(
            '<?php echo Yii::app()->createUrl("recruitment/exam"); ?>',
            post_data,
            function(d) {
                if (d.status) {
                    var html = "<h4>您已成功修改手机号为"+new_phone+"，公司会在近期以短信方式通知您面试，请注意查收。<h4>";
                    jQuery('#container').html(html);
                    jQuery('#frm').remove();
                } else {
                    d.msg;
                }
            },
            'json'
        );
    }
</script>
