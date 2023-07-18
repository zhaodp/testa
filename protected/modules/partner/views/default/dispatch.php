<?php 
$this->pageTitle = '手工派单';

$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->scriptMap=array();
$cs->registerScriptFile('http://api.map.baidu.com/api?v=1.3',CClientScript::POS_HEAD);

?>
<style type="text/css">
.container-fluid {padding:0px;}
.row-fluid input {width:150px;}
</style>
<div class="row-fluid">
	<div class="span7 well" style="padding:5px\9;">
		<?php echo $this->renderPartial('_dispatch_order', array('model'=>$model, 'partner'=>$partner, 'show_preferential'=>$show_preferential)); ?>
	</div>
	<div class="span5" id="client_queue" style="margin-left : 10px;">

	</div>
</div>

<script type="text/javascript">
var city;

$('#OrderQueue_city_id').change(function(){
	autocomplete();
	local_search();
});

$('body').on('blur','#OrderQueue_booking_time',function(){
	booking_time = str2date($(this).val()+":00");
	now = new Date();

	if(booking_time < now){
		alert('预约时间不能早于当前时间！');
		$('#OrderQueue_booking_time').focus();
	}
});	


$(document).ready(function(){
    $('input#OrderQueue_address').keydown(function(e){
    	if (e.keyCode == 13) {
    		local.setLocation(city);
        	map.clearOverlays();
			local.search($(this).val());
    	}			
	});

    $('input#OrderQueue_address').blur(function(e){
		local.setLocation(city);
    	map.clearOverlays();
		local.search($(this).val());
	});

    $('input#OrderQueue_address').focus();
	autocomplete();
	local_search();
})

function local_search(){
	city = $("#OrderQueue_city_id").find("option:selected").text();
	map.centerAndZoom(city, 15);
}

function autocomplete(){
	var location = $("#OrderQueue_city_id").find("option:selected").text();
    var ac = new BMap.Autocomplete(
        	{"input" : "OrderQueue_address",
        	 "location" : location
        });

    ac.addEventListener("onhighlight", function(e) {
    	var str = "";
    	if(e.fromitem.value){
        	var _value = e.fromitem.value;
    	}
        var value = "";
        if (e.fromitem.index > -1) {
            value = _value.business;
        }    
        
        value = "";
        if (e.toitem.index > -1) {
            _value = e.toitem.value;
            value = _value.business;
        }
        //$('input#OrderQueue_address').val(_value.business);
    });

    var myValue;
    //鼠标点击下拉列表后的事件
    ac.addEventListener("onconfirm", function(e) {
    	var _value = e.item.value;
    	//  _value.province +  _value.city +  _value.district +  _value.street + 
    	myValue = _value.business;
    	$('input#OrderQueue_address').val(_value.business);
    });   	
}

function sendprice(phone){
	if (phone == ''){
		alert ('电话信息不正确，请重新派单。');
		return false;
	}

	$('#sendpricebtn').attr("onclick",'alert("价格表已经在发送途中....")');

	if($("#OrderQueue_city_id").find("option:selected").val()==0){
		alert('未知城市！请先确定城市。');
		return false;
	}
    jQuery.get(
       '<?php echo Yii::app()->createUrl('business/default/sendprice')?>',
        {
            'phone' : phone,
            'city_id' : $("#OrderQueue_city_id").find("option:selected").val()
        },
        function(data){
            if (data == phone){
                alert ('价格表成功发送到手机' + phone);
            } else {
                alert ('价格表发送不成功。');
                $('#sendpricebtn').attr("disabled",false);
            }
        }

    );
}

function str2date (c_date) {
    if (!c_date)
        return "";
    var tempArray = c_date.split("-");
    if (tempArray.length != 3) {
        alert("你输入的日期格式不正确,正确的格式:2000-05-01 02:54:12");
        return 0;
    }
    var dateArr = c_date.split(" ");
    var date = null;
    if (dateArr.length == 2) {
        var yymmdd = dateArr[0].split("-");
        var hhmmss = dateArr[1].split(":");
        date = new Date(yymmdd[0], yymmdd[1] - 1, yymmdd[2], hhmmss[0], hhmmss[1], hhmmss[2]);
    } else {
        date = new Date(tempArray[0], tempArray[1] - 1, tempArray[2], 00, 00, 01);
    }
    return date;
};

function cancelQueue(id){
    if(confirm("确认取消此订单？")){
        $.ajax({
            'url':'<?php echo Yii::app()->createUrl('business/default/cancelqueue');?>',
            'data':'id='+id,
            'type':'get',
            'success':function(data){
                alert('订单已经取消。');
                $.fn.yiiGridView.update('orderqueue-grid', {
                    data: $(this).serialize()
                });
            },
            'cache':false
        });
    }
}
</script>