<?php
$cs=Yii::app()->clientScript;
$cs->registerScriptFile("http://172.16.11.11/jws/sourceCode/code/md5.js",CClientScript::POS_HEAD);
$cs->registerScriptFile("http://172.16.11.11/jws/sourceCode/code/ccic2JwsPhone.js",CClientScript::POS_HEAD);
$cs->registerScriptFile("http://172.16.11.11/jws/sourceCode/code/jWebSocket.js",CClientScript::POS_HEAD);
$cs->registerScriptFile("http://172.16.11.11/jws/sourceCode/code/ccic2JwsClient.js",CClientScript::POS_HEAD);
$cs->registerScriptFile("http://172.16.11.11/jws/sourceCode/code/stateMachine.js",CClientScript::POS_HEAD);
$cs->registerScriptFile("http://172.16.11.11/jws/sourceCode/softphone/sipActive.js",CClientScript::POS_HEAD);
$cs->registerScriptFile("sto/classic/ccic/toolbar.js",CClientScript::POS_HEAD);
$cs->registerCssFile('sto/classic/ccic/css/toolbar.css');

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'cru-dialog',
    'options'=>array(
        'title'=>'客户来电',
        'autoOpen'=>false,
	    'modal'=>false,
        'width'=>900,
        'height'=>550,
		'buttons'=>array(
        	'关闭'=>'js:function(){$("#cru-dialog").dialog("close");window.location.reload(true);}'
		)
    ),
));

?>

<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>
<?php $this->endWidget();?>

<input type="hidden" id='beijing' value="0">
<input type="hidden" id='beijing' value="0">
<input type="hidden" id='outside' value="0">

<div style="padding-top:6px;left:8px;top:8px;height:100%;">
<table border="0" cellspacing="0" cellpadding="0" style="margin:0;">
<tr>
    <td align="center"><span style="background:url('sto/classic/ccic/css/images/tb1.png') no-repeat; width:24px; height:24px; display:block;" id="statusImg"></span></td>
    <td align="center" id="status" width="40" style="text-align:left;padding-left:2px;">离线</td>
    <td align="center" id="durationCell" style="display: none; padding-right: 10px; vertical-align: top">
            状态时长<br>
      <span id="duration" style="font-weight: bold"></span>
    </td>
    <td align="left" id="toolbarButton">
                    <input type="button" id="pause" style="border:none;width:57px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_zm.png') no-repeat;"/>
                    <input type="button" id="online" style="border:none;width:57px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_zx.png') no-repeat;"/>
<!--                <input type="button" class="split"/>-->
					<input type="button" id="answer" value="" style="border:none;width:57px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_link.png') no-repeat"/>
<!--                    <input type="button" id="unLink" value="" style="border:none;width:57px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_gd.png') no-repeat"/> -->
                    <input type="button" id="refused" value="" style="border:none;width:57px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_jj.png') no-repeat;"/>
                    <input type="button" id="hold" value="" style="border:none;width:57px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_bc.png') no-repeat"/>
                    <input type="button" id="unHold"  value="" style="border:none;width:80px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_bcjh.png') no-repeat;"/>
                    
                    <input type="button" id="consult" value="" style="border:none;width:57px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_zixun.png') no-repeat;"/>
                    <input type="button" id="consultBack" value="" style="border:none;width:80px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_zixunjh.png') no-repeat;display:none;"/>
                    <input type="button" id="consultTransfer" value="" style="border:none;width:80px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_zixunzj.png') no-repeat;display:none;"/>
                    <input type="button" id="consultThreeway" value="" style="border:none;width:80px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_zixunsf.png') no-repeat; display:none;"/>
                    <input type="button" id="transfer" value="" style="border:none;width:57px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_zy.png') no-repeat;"/>
                    
<!--                <input type="button" class="split"/>-->
                    <input type="text"   id="phoneCallText" value="" maxlength="20" style="display:inline-block;width:100px;height:28px;line-height:28px;font-family:verdana;border:solid 1px #ddd;vertical-align:top;margin-top:2px;"/>
                    <input type="button" id="phoneCallout" value="" style="border:none;width:80px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_wh.png') no-repeat;"/>
                    <input type="button" id="phoneCallCancel" value="" style="border:none;width:80px;height:33px;background:url('sto/classic/ccic/css/images/toobarBtn_whqx.png') no-repeat;"/>

</td>

<td width='20px'>
</td>

<td>
<input id="orderQueue" type="button" value="队列" onclick="javascript:OrderQueue();" class="btn btn-info span1" style="margin-right:0px;margin-top:-15px" disabled="true">
<input id="dispatchOrder" type="button" value="派单" onclick="javascript:addOrder();" class="btn btn-success span1" style="margin-right:0px;margin-top:-15px" disabled="true">
<input type="button" value="登录" id='login_btn' onclick="javascript:login();" class="btn btn-primary span1" style="margin-right:0px;margin-top:-15px">
<input type="button" value="签出" id='logout_btn' onclick="javascript:logout();" class="btn btn-danger span1" style="margin-right:0px;margin-top:-15px" disabled="true">
</td>

<td id="queueNumberCell" style="font-size: 20px; font-weight: bold; color: red; display: none;">
    <span style="margin-left: 10px;">队列:</span>
    <span id="queueNumber"></span>
</td>

</tr>
</table>
</div>

<?php 
    renderDialog("consult", "咨询");
    renderDialog("transfer", "转移");
?>

<?php function renderDialog($action, $actionName) { 
    $radioName = $action . "Type";
?>
<div id="<?php echo  $action ?>Dialog">
    <table>
        <tr>
            <td><?php echo $actionName ?>类型：</td>
            <td>
                <input name="<?php echo  $radioName ?>" type="radio" value="1" style="margin-top: 0px" checked>
                <label style="display:inline" for="consultType1">座席号</label>
                <!-- 暂不支持分机
                <input name="<?php echo  $radioName ?>" type="radio" value="2" style="margin-top: 0px">
                <label style="display:inline" for="consultType2">分机</label>
                 -->
                <input name="<?php echo  $radioName ?>" type="radio" value="0" style="margin-top: 0px">
                <label style="display:inline" for="consultType0">普通电话</label>
            </td>
        </tr>
        
        <tr>
            <td><?php echo $actionName ?>目标号码：</td>
            <td>
                <input name="<?php echo $action ?>Number" type="text">
            </td>
        </tr>
    </table>
</div>
<?php } ?>
