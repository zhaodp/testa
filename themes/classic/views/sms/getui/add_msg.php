<?php
    $this->pageTitle = '新增个推消息';
    if(Yii::app()->user->hasFlash('alert')){
        Yii::app()->clientScript->registerScript('alert', 'alert("'.Yii::app()->user->getFlash('alert').'");');
    }
?>
<div class="row span10">
    <div class="span12 hide"></div>
<div class="span2"><h1><?php echo $this->pageTitle; ?></h1></div>
<div class="span2"><br><?php echo CHtml::link('返回推送列表' , array('sms/getuimsg'));?></div>
</div>
<div class="row-fluid span11 " style="height:50px;">
    <div id="all_driver_warnning" class="alert alert-block hide" style="margin:auto;width:350px;">
        <!--<button type="button" class="close" onclick="$('#all_driver_warnning').toggle();return false;">&times;</button>-->
        <h4 id="blink">注意: 您将推送消息至全体用户!</h4>
        <script>
            setInterval(function(){$('#blink').css('color',($('#blink').css('color')=='rgb(255, 0, 0)'?'blue':'red') )},300)
        </script>
    </div>
</div>
<?php
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'post',
    'htmlOptions'=>array('class'=>'row span10','onsubmit'=>'return submit_form()'),
));

Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
$city = Dict::items('city');
//添加权限判断，用户只能看到自己所在的城市，所属城市为0的用户可以看到所有城市
$userCity = Yii::app()->user->getCity();
$hasLimit = $userCity > 0 ? TRUE : FALSE;
if($hasLimit){
    $city = array($userCity=>$city[$userCity]);
}
?>

<style>
    .same-row {background-color:rgb(240,240,240);}
</style>
<table>
    <tr>
        <td>城市：</td>
        <td>
            <select id='sms_city_id' name='city_id' style='width:80px;'>
            <?php
			foreach ($city as $k=>$v)
			{
				echo "<option value='".$k."'>".$v."</option>";
			}
			?>
        </td>
            </tr>
            <tr class="same-row">
        <td>
            指定司机：</td>
        <td>
            <?php
                $radio = array('1'=>'全部（推送全部司机选择此项）','2'=>'指定司机（给个别用户单独推送信息时选择此项）');
                echo CHtml::radioButtonList('driver_limit',false,$radio,array(
                    'labelOptions'=>array(
                        'class'=>'radio inline',
                        'style'=>'padding-left:5px;'
                    ),
                    'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
                    'template'=>'{input}{label}',
                    'onchange'=>'
                        $hide_limit="#hide_driver_limit" + $(this).val();
                        $limit=".driver_limit" + $(this).val();
                        $(".driver_limit").html("").hide();
                        $($limit).html($($hide_limit).html()).show();
                        if($(this).val() == 2){
                            $("#all_driver_warnning").hide();
                            $("#sms_type").val("msg").attr("disabled",true);    //消息类型固定为[msg],不可修改
                        }else{
                            $("#all_driver_warnning").show();
                            $("#sms_city_id").change();     //重新获取热点区域
                            $("#sms_type").val("msg").attr("disabled",false);
                        }
                    ',
                ));
            ?>
        </td>
            </tr>
            <tr class="driver_limit1 driver_limit same-row"></tr>
            <tr id='driver_id' class="driver_limit2 driver_limit same-row"></tr>
            <tr>
                <td>消息类型：</td>
                <td>
                    <select id='sms_type' name='type_select' style='width:120px;' onchange="$('#sms_type_hide').val($(this).val())">
                <option value='msg'>消息</option>
                <option value='notice'>公告</option>
                <?php
				if (130 == Yii::app()->user->user_id) {
					echo "<option value='cmd'>指令</option>";
				}
				?>
			</select>
                    <input id='sms_type_hide' type="hidden" name="type" value="">
                </td>
            </tr>
	<tr>
		<td>级别：</td>
		<td>
		    <select id='sms_level' name='level' style='width:120px;'>
				<option value='3'>及时送达</option>
	            <option value='2'>中级</option>
	            <option value='1'>正常</option>
            </select>
            <span style="font-weight:bold;color:red;margin-left:20px;">及时送达：</span>当前时间打开客户端能收到
            <span style="font-weight:bold;color:red;margin-left:20px;">中级：</span>当前时间开机能收到
            <span style="font-weight:bold;color:red;margin-left:20px;">正常：</span>30分钟内开机即能收到
		</td>
	</tr>
    <tr>
        <td>消息内容：<br/>(最多100个字符)</td>
        <td><textarea name='content' style='width:400px;height:100px;'></textarea><br /></td>
    </tr>
    <tr>
        <td>预计发送时间：</td>
        <td>
		<?php
		$this->widget('CJuiDateTimePicker', array (
		    'id' => 'report_pre_send_time',
			'name'=>'pre_send_time', 
			'mode'=>'datetime',
			'options'=>array (
			    'width' => '60',
			    'mode'=>'datetime',
				'dateFormat'=>'yy-mm-dd'
			),
			'htmlOptions'=>array(
		         'style'=>'width:100px;'
		     ),
			'language'=>'zh'
		));
		?>
        </td>
    </tr>
    <tr>
        <td colspan=2 align='center'><?php echo CHtml::submitButton('提交');?></td>
    </tr>
</table>
<?php
$this->endWidget();
?>
<script>
$(document).ready(function(){
	$("#sms_mark").change(function(){
		if($("#sms_city_id").val() == 0 && $("#sms_mark").val() == 0 && $("#sms_area").val() == 0){
			$("#driver_id").show();
		}else {
			$("#driver_id").hide();
			$("#sms_driver_ids").attr("value",'');
		}
	});
	$("#sms_area").change(function(){
		if($("#sms_city_id").val() == 0 && $("#sms_mark").val() == 0 && $("#sms_area").val() == 0){
			$("#driver_id").show();
		}else {
			$("#driver_id").hide();
			$("#sms_driver_ids").attr("value",'');
		}
	});
        $('input[name="driver_limit"]:eq(1)').click().change();     //页面初始化后选定【指定司机】选项
        $('#sms_type').change();                                    //初始化消息类型
});
function submit_form(){
    if($('input[name="driver_limit"]:checked').val() == "2"){       //验证工号是否为空
        if($('#sms_driver_ids').val().replace(/(\s*)/g, '') == ""){
            alert("请输入工号");
            return false;
        }else{
            return true;
        }
    }
}
$('#sms_city_id').bind({change:function(){                          //修改城市，热点区域联动
    if($('input[name="driver_limit"]:checked').val() == 1){
        $.get(
            '<?php echo Yii::app()->createUrl('sms/addgetuimsg') ?>',
            {changeCity:$('#sms_city_id').val()},
            function(data){
                $('#span_hot_area').html(data);
            }
        )
    }
}});
//添加筛选条件后隐藏提示信息
function addCriteria(){
    var $show = 1;
    $('.driver_select_all').each(function(){
        if($(this).val() > 0){
            $show = 0;
        }
    });
    if(!$show){
        $("#all_driver_warnning").hide();
    }else{
        $("#all_driver_warnning").show();
    }
}
</script>


<!--模版-->
<table class="hide">
    <tr id="hide_driver_limit1"><td></td>
        <td colspan="2">司机状态:
            <select class="driver_select_all" id='sms_mark' name='mark' style='width:80px;' onchange="addCriteria()">
                <option value='0'>全部</option>
                <option value='1'>正常</option>
                <option value='2'>屏蔽</option>
                <option value='3'>上班</option>
                <option value='4'>下班</option>
            </select>
            &nbsp;&nbsp;&nbsp;&nbsp;热点区域：
            <span id="span_hot_area"></span>
        </td>
    </tr>
    <tr id="hide_driver_limit2">
        <td>司机工号：<br/>(一行一号码<br/>最多200个)</td>
        <td><textarea id='sms_driver_ids' name='driver_ids' style='width:400px;height:150px;'></textarea><br /></td>
    </tr>
</table>