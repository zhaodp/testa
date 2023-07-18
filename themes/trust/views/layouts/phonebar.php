<?php
$cs=Yii::app()->clientScript;
$cs->registerScriptFile(SP_URL_HOJO.'js/icallcenter/global.js',CClientScript::POS_HEAD);
$cs->registerScriptFile(SP_URL_HOJO.'hojo/hojo.js',CClientScript::POS_HEAD);
$cs->registerCssFile(SP_URL_HOJO.'css/pages.css');

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

<script type="text/javascript">
hojo.registerModulePath("icallcenter", "../js/icallcenter");
hojo.require("icallcenter.logon");
hojo.require("hojo.io.script");

hojo.addOnLoad(function () {
    //var loginName = icallcenter.logon.getUrlValue("loginName");
    //var password = icallcenter.logon.getUrlValue("password");
    //var loginType = icallcenter.logon.getUrlValue("loginType");

    var loginName = "<?php echo Yii::app()->user->agent['agent_name'];?>";
    var password = "<?php echo Yii::app()->user->agent['password'];?>";
    var loginType = "Local";

    icallcenter.logon.startLogon(loginName, password, loginType);
});

function relogin(){
    var loginName = "<?php echo Yii::app()->user->agent['agent_name'];?>";
    var password = "<?php echo Yii::app()->user->agent['password'];?>";
    var loginType = "Local";

    icallcenter.logon.startLogon(loginName, password, loginType);
}

hojo.addOnWindowUnload(function (){
	if(phone) {
		phone.destroy(true);
	}
	sleep(2);
});

function destroy() {
	if(confirm("点击确认等待提示“签出成功”后再关闭浏览器！")){
		if(phone) {
			phone.destroy();
			//location.href = "index.php?r=notice/index&category=0";
			sleep(6);
		}
		alert('坐席签出成功！');
		window.close();
		return true; 
	}
}

function sleep(seconds){
var d1 = new Date();
var t1 = d1.getTime();
	for (;;){
		var d2 = new Date();
		var t2 = d2.getTime();
		if (t2-t1 > seconds*1000){
			break; 
		}
	}
}

<?php if (Yii::app()->session['wait'] ==2){?>
//$(window).bind('beforeunload',function(){
//return '您输入的内容尚未保存，确定离开此页面吗？';
//});

window.onload = function(){
    var allowUnload = true;
     
    window.onbeforeunload = function(e){
	    //allowUnload will allow us to see if user recently clicked something if so we wont allow the beforeunload.
    	if(allowUnload){
		    //message to be returned to the popup box.
	    	var message = '确认离开派单页面吗？',
    		e = e||window.event;
	    	if(e)
   				e.returnValue=message; // IE
    		return message; // Safari
    	}
    };
    document.getElementsByTagName('body')[0].onclick = function(){
    allowUnload = false;
    setTimeout(function(){ allowUnload = true; },500);
    };
};
<?php }?>
</script>
<input type="hidden" id='beijing' value="0"></input>
<input type="hidden" id='outside' value="0"></input>

<!--softphonebar start-->
	<div id="softphonebar" style="">
    <div class="alert alert-success" style="padding:0px;">
		<div class="barBox" id="callStatus">
			<input type="text" id="icallcenter.dialout.input" placeholder="输入手机号码或工号" onKeyDown="if(event.keyCode == 13){softphoneBar.dialout(hojo.byId('icallcenter.dialout.input').value)}" class="inp1 fl" style="width:120px" />
			<div class="softphone_timer">
				<div id="softphonebar.peerState"></div>
				<div id="softphonebar.peerTimeState" class="peerTimeState">00:00:00</div>
			</div>
			<a href="#" class="DialEnable" id="DialEnable" style="" onclick="softphoneBar.dialout(hojo.byId('icallcenter.dialout.input').value)"></a>
			<a href="#" class="DialDisable" id="DialDisable" style="display: none"></a>
			<a href="#" class="HangupEnable" id="HangupEnable" style="display: none" onClick="javascript:phone.hangup();"></a>
			<a href="#" class="HangupDisable" id="HangupDisable"></a>
			<a href="#" class="HoldEnable" id="HoldEnable" style="display: none" onClick="phone.hold();"></a>
			<a href="#" class="HoldDisable" id="HoldDisable" ></a>
			<a href="#" class="HoldGetEnable" id="HoldGetEnable" style="display: none" onClick="phone.unhold();"></a>
			<a href="#" class="TransferEnable" id="TransferEnable" style="display: none" onClick="javascript:softphoneBar.toTransfer();"></a> 
			<a href="#" class="TransferDisable" id="TransferDisable" ></a>
			<a href="#" class="TransferEnable" id="ConsultTransferEnable" style="display: none" onclick="javascript:phone.transfer('912345','external', {})"></a> 
			<a href="#" class="TransferDisable" id="ConsultTransferDisable" style="display: none"></a>
			<a href="#" class="ConsultEnable" id="ConsultEnable" style="display: none" onclick="javascript:softphoneBar.toConsult('<?php echo isset($data['phone'])?trim($data['phone']):'';?>');"></a> 
			<a href="#" class="ConsultDisable" id="ConsultDisable" ></a>
			<a href="#" class="StopConsultEnable" id="StopConsultEnable" style="display: none" onclick="javascript:phone.stopConsult();"></a> 
			<a href="#" class="ThreeWayCallEnable" id="ConsultThreeWayCallEnable" style="display: none" onclick="javascript:phone.threeWayCall('912345')"></a> 
			<a href="#" class="ThreeWayCallDisable" id="ConsultThreeWayCallDisable" style="display: none"></a>
		</div>
		<div class="barBox" style="margin-top: 10px;margin-left:10px" id="peerStatus">
			<a href="#" id="IdleEnable" class="IdleEnable" ></a>
			<a href="#" onclick="javascript:phone.setBusy(false,'0')" id="IdleDisable" class="IdleDisable" style="display: none"></a>
			<a href="#" id="RestEnable" class="RestEnable" style="display: none"></a>
			<a href="#" onclick="javascript:phone.setBusy(true,'2')" id="RestDisable" class="RestDisable" ></a>
			<a href="#" id="BusyEnable" class="BusyEnable" style="display: none"></a>
			<a href="#" onclick="javascript:phone.setBusy(true,'1')" id="BusyDisable" class="BusyDisable" ></a>
			<h3 class="alert alert-error" style="float: right; padding: 0px 5px 0px 5px; margin-top: -2px;">0</h3>
			<div style="float:right;margin:4px;padding:5px;text-align:right;">
				<input type="button" value="队列" onclick="javascript:OrderQueue();" class="btn btn-info span1" style="margin-right:0px;margin-top:-15px">
				<input type="button" value="派单" onclick="javascript:addOrder();" class="btn btn-success span1" style="margin-right:0px;margin-top:-15px">
			    <input type="button" value="签出" onclick="javascript:destroy();" class="btn btn-danger span1" style="margin-right:0px;margin-top:-15px">
			</div>
	    </div>
		<div style="clear:both"></div>
	</div>
</div>
<!--softphonebar end-->