<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
	'id'=>'mydialog',
	'options'=>array(
		'title'=>'成单记录',
		'autoOpen'=>false,
		'width'=>'600',
		'height'=>'300',
		'modal'=>true,
		'buttons'=>array(
			'关闭'=>'js:function(){$("#mydialog").dialog("close");}'))));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'order-queue-create-form',
    'enableAjaxValidation'=>false,
)); ?>
    <div class="row-fluid">
        <div class="span4">
            <div class="input-prepend input-append">
                <label for="OrderQueue_name">请确认您的手机号</label>
				<span class="add-on">联系电话</span>
				<?php echo $form->textField($model,'contact_phone', array('class'=>'span7')); ?>
				<?php echo $form->error($model,'contact_phone'); ?>
		    </div>

            <div class="input-prepend input-append">
                <label for="OrderQueue_name">请问您需要几名司机</label>
				<span class="add-on">几位司机</span>
				<?php echo $form->textField($model,'number',array('class'=>'span7')); ?>
				<?php echo $form->error($model,'number'); ?>
		    </div>

			<label for="OrderQueue_booking_time"></label>
		    <div class="input-prepend input-append">
                <label for="OrderQueue_name">请问您什么时间出发呢？<!--(20分钟到达，高峰时间40分钟到达)--></label>
				<span class="add-on">出发时间</span>
				<?php echo $form->textField($model,'booking_time',array('class'=>'span8')); ?>
				<?php echo $form->error($model,'booking_time'); ?>
			</div>
        </div>
	    <div class="span4">
		    <div class="input-prepend input-append">
                <label for="OrderQueue_name">请确认您的所在城市</label>
				<span class="add-on"">所在城市</span>
				<?php
					$city = Dict::items('city');
					$city[0] = '无法定位城市';
					echo $form->dropDownList($model,'city_id',$city,array('class'=>'span7'));
				?>
		    </div>

		    <div class="input-prepend input-append" style="display: none">
                <!--
				<span class="add-on">客户电话</span>
				-->
				<?php echo $form->hiddenField($model,'phone'); ?>
				<?php //echo $form->error($model,'phone'); ?>
				<?php
				      //添加查看成单数量 Modify BY AndyCong 2013-04-16
				      //echo CHtml::Button('查看',array('class'=>'btn','id'=>'search_order_num','style'=>'width:40px;height:30px;',"onclick"=>"check_user_order()"));
				      //黑名单用户加说明 Modify BY AndyCong 2013-04-16
				      /*
                      if (Order::model()->checkBlackCustomer(trim($model->phone))) {
				          echo '<span class="add-on" style="color:red;">*黑名单用户</span>';
				      }
				      */
				?>
		    </div>


			<div class="input-prepend input-append">
                <label for="OrderQueue_name">请问您现在在什么位置</label>
				<span class="add-on">所在位置</span>
				<?php echo $form->textField($model,'address',array('class'=>'span8')); ?>
				<?php echo $form->error($model,'address'); ?>
			</div>

            <div class="input-prepend input-append">
                <?php if ($partner['remark']) {?>
                <label for="OrderQueue_name">订单备注</label>
                <?php echo $form->textField($model,'comments',array('style'=>'width:195px')); ?>
                <?php echo $form->error($model,'comments'); ?>
                <?php } ?>
            </div>

		</div>
	    <div class="span4">

		    <label for="OrderQueue_name">请问您怎么称呼</label>
		    <div class="input-prepend input-append">
				<span class="add-on">客户名称</span>
				<?php echo $form->textField($model,'name',array('class'=>'span7')); ?>
				<?php echo $form->error($model,'name'); ?>
		    </div>

            <label for="OrderQueue_name"><?php echo $partner['pay_sort']==Partner::PAY_SORT_BONUS ? '客户优惠' : '&nbsp;';?></label>
            <div class="" style="line-height: 25px; height: 30px; margin-bottom: 12px;">
                <label class="checkbox">
                    <?php if ($show_preferential) {?>
                    <input style="width:20px;" type="checkbox" name="preferential" id="preferential" value="1" checked="checked"><?php echo '优惠券减免';?>
                    <?php }  ?>
                    <span id="waring"></span>
                    <br>
                    <span id="used" style="font-color:red"></span>
                </label>
			</div>
			<label for="OrderQueue_booking_time"> 稍后司机会跟您联系 </label>
			<div>
				<?php echo CHtml::submitButton('提交订单', array ('class'=>'btn btn-success span5','tabindex'=>6));?>
			</div>
		</div>
		<?php echo $form->hiddenField($model,'agent_id'); ?>
		<?php echo $form->hiddenField($model,'callid'); ?>
    </div>
	<div>
		<div id="map_canvas" style="height:400px;border:solid 1px gray" class="span8"></div>
		<div id="map_poilist" style="height:400px;border:solid 1px gray" class="span4"></div>
	</div>
	<?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
var map = new BMap.Map("map_canvas");
var opts = {anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(2, 2)};
map.enableScrollWheelZoom();
map.addControl(new BMap.NavigationControl(opts));
map.centerAndZoom(new BMap.Point(116.404, 39.915), 15);

window.openInfoWinFuns = null;
var options = {
  //renderOptions:{map: map},
  onSearchComplete: function(results){
    // 判断状态是否正确
    if (local.getStatus() == BMAP_STATUS_SUCCESS){
        var s = [];
        s.push('<div>');
        s.push('<div style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">');
        s.push('<ol style="list-style: none outside none; padding: 0pt; margin: 0pt;">');
        openInfoWinFuns = [];
        for (var i = 0; i < results.getCurrentNumPois(); i ++){
            var marker = addMarker(results.getPoi(i).point,i);
            var openInfoWinFun = addInfoWindow(marker,results.getPoi(i),i);
            openInfoWinFuns.push(openInfoWinFun);
            // 默认打开第一标注的信息窗口
            var selected = "";
            if(i == 0){
                selected = "background-color:#f0f0f0;";
                openInfoWinFun();
            }
            s.push('<li id="list' + i + '" style="margin: 2px 0pt; padding: 0pt 5px 0pt 3px; cursor: pointer; overflow: hidden; line-height: 17px;' + selected + '" onclick="openInfoWinFuns[' + i + ']()">');
            s.push('<span style="width:1px;background:url(<?php echo SP_URL_STO;?>www/images/red_labels.gif) 0 ' + ( 2 - i*20 ) + 'px no-repeat;padding-left:10px;margin-right:3px"> </span>');
            s.push('<span style="color:#00c;text-decoration:underline">' + results.getPoi(i).title.replace(new RegExp(results.keyword,"g"),'<b>' + results.keyword + '</b>') + '</span>');
            s.push('<span style="color:#666;"> - ' + results.getPoi(i).address + '</span>');
            s.push('</li>');
            s.push('');
        }
        s.push('</ol></div></div>');
        document.getElementById("map_poilist").innerHTML = s.join("");
    }
  }
};

//添加标注
function addMarker(point, index){
  var myIcon = new BMap.Icon("http://api.map.baidu.com/img/markers.png", new BMap.Size(23, 25), {
    offset: new BMap.Size(10, 25),
    imageOffset: new BMap.Size(0, 0 - index * 25)
  });
  var marker = new BMap.Marker(point, {icon: myIcon});
  map.addOverlay(marker);
  return marker;
}
// 添加信息窗口
function addInfoWindow(marker,poi,index){
    var maxLen = 10;
    var name = null;
    if(poi.type == BMAP_POI_TYPE_NORMAL){
        name = "地址：  "
    }else if(poi.type == BMAP_POI_TYPE_BUSSTOP){
        name = "公交：  "
    }else if(poi.type == BMAP_POI_TYPE_SUBSTOP){
        name = "地铁：  "
    }
    // infowindow的标题
    var infoWindowTitle = '<div style="font-weight:bold;color:#CE5521;font-size:14px">'+poi.title+'</div>';
    // infowindow的显示信息
    var infoWindowHtml = [];
    infoWindowHtml.push('<table cellspacing="0" style="table-layout:fixed;width:100%;font:12px arial,simsun,sans-serif"><tbody>');
    infoWindowHtml.push('<tr>');
    infoWindowHtml.push('<td style="vertical-align:top;line-height:16px;width:38px;white-space:nowrap;word-break:keep-all">' + name + '</td>');
    infoWindowHtml.push('<td style="vertical-align:top;line-height:16px">' + poi.address + ' </td>');
    infoWindowHtml.push('</tr>');
    infoWindowHtml.push('</tbody></table>');
    var infoWindow = new BMap.InfoWindow(infoWindowHtml.join(""),{title:infoWindowTitle,width:200});
    var openInfoWinFun = function(){
        marker.openInfoWindow(infoWindow);
        for(var cnt = 0; cnt < maxLen; cnt++){
            if(!document.getElementById("list" + cnt)){continue;}
            if(cnt == index){
                document.getElementById("list" + cnt).style.backgroundColor = "#f0f0f0";
            }else{
                document.getElementById("list" + cnt).style.backgroundColor = "#fff";
            }
        }
    }
    marker.addEventListener("click", openInfoWinFun);
    return openInfoWinFun;
}

var local = new BMap.LocalSearch(map, options);
local.disableFirstResultSelection();

function check_user_order(){
	phone = $('input#OrderQueue_phone').val();
	if(phone!=''){
		$.ajax({
			'url':'<?php
			echo Yii::app()->createUrl('/client/phoneordernum');
			?>',
			'data':{'phone':phone},
			'type':'get',
			'success':function(data){
				$('#dialogdiv').html(data);
			},
			'cache':false
		});
		$("#mydialog").dialog("open");
	}
	return false;
}

$('input[type="submit"]').click(function(){
    $('input[type="submit"]').button('loading');
    $('#order-queue-create-form').submit();
});

/**
 * trim 扩展方法
 * @returns {string}
 * @constructor
 */
String.prototype.Trim = function() {
    var m = this.match(/^\s*(\S+(\s+\S+)*)\s*$/);
    return (m == null) ? "" : m[1];
}

/**
 * 验证手机号码
 * @returns {boolean}
 */
String.prototype.isMobile = function() {
    return (/^(?:13\d|15\d|14\d|18\d)-?\d{5}(\d{3}|\*{3})$/.test(this.Trim()));
}

function getSurplus(partner_id) {
    jQuery.get(
        '<?php echo $partner['show_balance'] ? Yii::app()->createUrl('business/default/ajax') : '';?>',
        {
            'act' : 'get_surplus',
            'partner_id' : partner_id
        },
        function(d) {
            if (d.status) {
               jQuery('#waring').html(d.str);
            }
        },
        'json'
    );
}

jQuery(document).ready(function(){

    <?php if($partner['show_balance']) { echo 'getSurplus('.$partner['id'].');'; }?>

    jQuery('#OrderQueue_contact_phone').blur(function(){
        var phone = jQuery(this).val();
        if (phone.length<=0) {
            return false;
        }
        if (!phone.isMobile()) {
            alert('联系电话有误');
            return false;
        }
        jQuery.get(
            '<?php echo Yii::app()->createUrl('business/default/ajax');?>',
            {
                'act' : 'get_phone_location',
                'phone' : phone
            },
            function(d){
                if (d.status) {
                    jQuery('#OrderQueue_city_id').find('option').each(function(i,v){
                        if (jQuery(this).val() == d.msg) {
                            jQuery(this).attr('selected', 'selected');
                            local_search();
                        }
                    });
                }
            },
            'json'
        )

        <?php
            if ($partner['pay_sort'] == Partner::PAY_SORT_BONUS) {
        ?>
        jQuery.get(
            '<?php echo Yii::app()->createUrl('business/default/ajax'); ?>',
            {
                'act' : 'get_bonus_used_num',
                'phone' : phone
            },
            function (d) {
                if (d.status) {
                    var str = '此用户已使用'+ parseInt(d.msg)+'张';
                    jQuery('#used').html(str);
                }
            },
            'json'
        );
        <?php
            }
        ?>

        jQuery.get(
            '<?php echo Yii::app()->createUrl('business/default/clientQueue');?>',
            {
                'phone' : phone
            },
            function(d) {
                jQuery('#client_queue').html(d);
            }
        );

    });

    jQuery('#OrderQueue_number').blur(function(){
        var driver_num = jQuery(this).val();
        if (parseInt(driver_num) >= 10) {
            alert('每单预约司机人数不能超过10人 ');
        }
    });

});

</script>
