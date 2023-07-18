<?php
$extra_price = array(
    10 => array(19, 25, 27, 33, 61, 103), //加价10元文案
    4 => array(74), //加价4元文案
    );
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
<?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id' => 'customer_invoice_dialog',
        'options' => array(
            'title' => '城市详情',
            'autoOpen' => false,
            'width' => '900',
            'height' => '580',
            'modal' => true,
            'buttons' => array(
                '关闭' => 'js:function(){$("#customer_invoice_dialog").dialog("close");  $(".search-form form").submit();} '
            ),
        ),
    ));
        echo '<div id="customer_invoice_dialog_div"></div>';
        echo '<iframe id="cru-frame-customer-invoice" width="100%" height="100%" style="border:0px"></iframe>';
    $this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'order-queue-create-form',
    'enableAjaxValidation'=>false,
)); ?>
    <div class="row-fluid">
        <div class="span4">
            <div class="input-prepend input-append">
                <span class="add-on">所在城市</span>
                <?php 
//                    $city = Dict::items('city');
//                    $city[0] = '无法定位城市';
//                    echo $form->dropDownList($model,'city_id',$city,array('style'=>'width:120px'));
                ?>
                <?php
                        $user_city_id = Yii::app()->user->city;

                        if ($user_city_id != 0) {
                            $city_list = array(
                                '城市' => array(
                                    $user_city_id => Dict::item('city', $user_city_id)
                                )
                            );
                            $city_id = $user_city_id;
                        } else {
                            $city_id = $model->city_id;
                            $city_list = CityTools::cityPinYinSort();
                        }
                        $this->widget("application.widgets.common.DropDownCity", array(
                            'cityList' => $city_list,
                            'name' => 'OrderQueue[city_id]',
                            'value' => $city_id,
                            'type' => 'modal',
                            'htmlOptions' => array(
                                'style' => 'width: 85px; cursor: pointer;',
                                'readonly' => 'readonly',
                            ),
                            'defaultText'=>'无法定位城市',
                            'callback' => 'function mycall(city_id,city_name){ autocomplete(city_name);local_search(city_name);}',
                        ));
                ?>
                &nbsp;&nbsp;<?php echo CHtml::button('城市详情', array ('class'=>'btn btn-info','id'=>'citydetail','tabindex'=>6));?>
            </div>
            
            <?php if(in_array($city_id, $extra_price[10])){ ?>
                <span style="color:red;">夜间业务400加价10元(VIP除外)</span>
            <?php } ?>
            <?php if(in_array($city_id, $extra_price[4])){ ?>
                <span style="color:red;">夜间业务400加价4元(VIP除外)</span>
            <?php } ?>
            <label>您好，e代驾！很高兴为您服务！请问您现在在什么位置？</label>
            <div class="input-prepend input-append">
                <span class="add-on">所在位置</span>
                <?php echo $form->textField($model,'address',array('style'=>'width:150px')); ?>
                <?php echo $form->error($model,'address'); ?>
            </div>
            <div>
            <?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'driver_search_dialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '查询附近的司机',
        'autoOpen' => false,
        'width' => '900',
        'height' => '580',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#driver_search_dialog").dialog("close");  $(".search-form form").submit();} '
        ),
    ),
));
echo '<div id="driver_search_dialogdiv"></div>';
echo '<iframe id="cru-frame-driver-search" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
echo CHtml::link('查询司机', 'javaScript:void(0);', array('onClick' => 'driverSearchDialogdivInit(\'' . Yii::app()->createUrl("driver/search") . '\')', 'class' => 'btn', 'style' => 'margin-left:10px;'));
//echo CHtml::button('查询司机', array('class'=>'btn btn-info', 'id'=>'driver_search_id', 'style'=>'height:30px;','onclick'=>'$("#mydialog").dialog("open"); return false;'));
                //$driver_search_url=$this->createUrl('driver/search',array('map_address'=>$model->address,'city'=>$model->city_id)); 
                //echo CHtml::linkButton('查询司机',array('href'=>$driver_search_url,'target'=>'_blank','id'=>'search_driver')); 
            ?>
            </div>
        </div>
        
        <div class="span4">
            <div class="input-prepend input-append">
                <span class="add-on">客户电话</span>
                <?php echo $form->textField($model,'phone',array('style'=>'width:110px')); ?>
                <?php echo $form->error($model,'phone'); ?>
                <?php 
                      echo CHtml::button('验证归属', array('class'=>'btn btn-info', 'id'=>'partner_verify_box', 'style'=>'height:30px;'));
                      //添加查看成单数量 Modify BY AndyCong 2013-04-16
                      //echo CHtml::Button('查看',array('class'=>'btn','id'=>'search_order_num','style'=>'width:40px;height:30px;',"onclick"=>"check_user_order()"));
                      //黑名单用户加说明 Modify BY AndyCong 2013-04-16
                      if (Order::model()->checkBlackCustomer(trim($model->phone))) {
                          echo '<div style="color:red;font-size:14px">*黑名单用户</div>';
                      }
                    
                ?>
            </div>
            
            <div class="input-prepend input-append">
                <span class="add-on">联系电话</span>
                <?php echo $form->textField($model,'contact_phone',array('style'=>'width:150px')); ?>
                <?php echo $form->error($model,'contact_phone'); ?>
            </div>
            
            <label for="OrderQueue_name">请问您贵姓？</label>
            <div class="input-prepend input-append">
                <span class="add-on">客户名称</span>
                <?php echo $form->textField($model,'name',array('style'=>'width:150px')); ?>
                <?php echo $form->error($model,'name'); ?>
                <div align="right">
                    <?php echo CHtml::radioButtonList('sex', null, 
                            array('先生' => '先生', '女士' => '女士'),
                            array('separator'=> '', 
                                  'labelOptions' => array('style' => 'display:inline'))) ?>
                </div>
            </div>
            
            
        </div>
        
        <div class="span4">
            <div class="input-prepend input-append">
                <span class="add-on">几位司机</span>
                <?php echo $form->textField($model,'number',array('style'=>'width:150px')); ?>
                <?php echo $form->error($model,'number'); ?>
            </div>
            
            <label for="OrderQueue_booking_time" id="tlt">请问您什么时间出发呢？(20分钟到达，高峰时间40分钟到达)</label>
            <div class="input-prepend input-append">
                <span class="add-on">出发时间</span>

                <?php echo $form->textField($model,'booking_time_day',array('style'=>'width:75px')); ?>
                <?php echo $form->textField($model,'booking_time_time',array('style'=>'width:61px')); ?>

                <?php echo $form->error($model,'booking_time'); ?>
            </div>
            
            <div class="input-prepend input-append">
                <span class="add-on">订单备注</span>
                <?php echo $form->textField($model,'comments',array('style'=>'width:150px')); ?>
                <?php echo $form->error($model,'comments'); ?>
            </div>
        </div>
        
        <div class="input-prepend input-append" style="display: none">
            <span class="add-on">订单渠道</span>
            <?php echo $form->textField($model,'channel',array('style'=>'width:150px')); ?>
            <?php echo $form->error($model,'channel'); ?>
        </div>
        
        <!--此处做法比较恶心，但没想到好的处理办法-->
        <div class="input-prepend input-append" style="display: none">
            <input type="hidden" id="p_id_card" name="ping_an[id_card]" value="" />
            <input type="hidden" id="p_password" name="ping_an[password]" value="" />
        </div>
        <?php echo $form->hiddenField($model,'agent_id'); ?>
        <?php echo $form->hiddenField($model,'callid'); ?>
    </div>
    
    <div style="margin-bottom: 8px">
        <span>稍后司机会跟您联系，感谢使用e代驾！再见！</span>
        <?php echo CHtml::submitButton('提交订单', array ('class'=>'btn btn-success','tabindex'=>6));?>
    </div>
    
    <div>
        <div id="map_canvas" style="height:400px;border:solid 1px gray;" class="span6 "></div>
        <div id="map_poilist" style="height:400px;border:solid 1px gray;overflow-x:scroll;" class="span3"></div>
        <div id="lib_poilist" style="height:400px;border:solid 1px gray;overflow-x:scroll;" class="span3">
            <div style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                <ol style="list-style: none outside none; padding: 0pt; margin: 0pt;"></ol>
            </div>
        </div>
    </div>
    
    <input type="hidden" id="dispatchType" name="dispatchType" value="<?php echo $dispatchType ?>" />
    <input type="hidden" id="ringTime"  name="ringTime" value="<?php echo $ringTime ?>" />
    
    <?php $this->endWidget(); ?>
</div><!-- form -->

<script type="text/javascript">
function driverSearchDialogdivInit(href) {
    href=href+'&city='+$('#OrderQueue_city_id').val()+'&map_address='+$('#OrderQueue_address').val();
    $("#cru-frame-driver-search").attr("src", href);
    $("#driver_search_dialog").dialog("open");
    return false;
}

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
    var chk= checkInput();
    if(chk){
        $('input[type="submit"]').button('loading');
        $('#order-queue-create-form').submit();
    }else{
        return false;
    }
});

function checkInput(){
    var flag=true;
    //验证用户电话、手机、座机
    var phone=$("#OrderQueue_phone").val();
    var contact_phone=$("#OrderQueue_contact_phone").val();
    var name=$("#OrderQueue_name").val();
    var address=$("#OrderQueue_address").val();
    phone=  phone.replace('-','');
    contact_phone= contact_phone.replace('-','');

    if(phone.length >0){
        if(isNaN(phone)){
            alert('请正确填写客户电话!');
            flag= false;
        }
    }else{
        alert('客户电话，不能为空!');
        flag= false;
    }

    if(contact_phone.length>0){
        if(isNaN(contact_phone)){
            alert('请正确填写客户联系电话!');
            flag= false;
        }
    }

    //位置
    if(address.length<=0){
        alert('请正确填写客户地址!');
        flag= false;
    }
    if(address.length == 1){
        alert('客户地址字数太少太模糊请更改!');
        flag= false;
    }
    //客户名称
    if(name.length<=0 || name=='' || name==null){
        alert('请填写客户名称!');
        flag= false;
    }

    var $driverNumInput = $("#OrderQueue_number");
    if (!/^[1-9]$/.test($driverNumInput.val())) {
        alert('司机数量只能是1到9');
        $driverNumInput.focus();
        return false;
    }

    return flag;

}

$('body').on('blur','#OrderQueue_booking_time_time',function(){
    var mday=$('#OrderQueue_booking_time_day').val();
    var mtime=$('#OrderQueue_booking_time_time').val();
    booking_time = str2date(mday+" "+mtime+":00");
    now = new Date();

    if(booking_time < now){
        $('#OrderQueue_booking_time_time').focus();
        alert('预约时间不能早于当前时间！');
    }

});

Date.prototype.format = function(format)
{
var o = {
            "M+" : this.getMonth()+1, //month
            "d+" : this.getDate(), //day
            "h+" : this.getHours(), //hour
            "m+" : this.getMinutes(), //minute
            "s+" : this.getSeconds(), //second
            "q+" : Math.floor((this.getMonth()+3)/3), //quarter
            "S" : this.getMilliseconds() //millisecond
        }
    if(/(y+)/.test(format))
    format=format.replace(RegExp.$1,(this.getFullYear()+"").substr(4 - RegExp.$1.length));
    for(var k in o)
    if(new RegExp("("+ k +")").test(format))
    format = format.replace(RegExp.$1,RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length));
    return format;
}

function updateDepartureTime() {
    var d = $("#OrderQueue_booking_time_day").val();
    var t = $("#OrderQueue_booking_time_time").val();
    var dtstr = d+' '+t;
    dtstr = dtstr.replace(/-/g,'/');
    var dt = new Date(dtstr);
    dt.setTime(dt.getTime() + 60000);
    $("#OrderQueue_booking_time_day").val(dt.format("yyyy-MM-dd"));
    $("#OrderQueue_booking_time_time").val(dt.format("hh:mm"));
}
//如果不修改,下单时间每分钟自增
var departureTimer = window.setInterval("updateDepartureTime()", 60000);
$("#OrderQueue_booking_time_day, #OrderQueue_booking_time_time").keypress(function() {
     clearInterval(departureTimer);
});

/**
*  从地址库中搜索 本文件中有三处修改
**/
;(function($){

    var $address = $("#OrderQueue_address");

    $address.keydown(function(e){
        if(e.keyCode==13){
            refreshAddressPool();
        }
    });

    $address.blur(function(){
        refreshAddressPool();
    });

    function refreshAddressPool() {
        var searchStr=$('#OrderQueue_address').val();
        if(searchStr == "") return;
        if($("#OrderQueue_city_id").val()!=0){
            searchStr+="&AddressPool[city_id]="+$("#OrderQueue_city_id").val();
        }

        $.get('index.php?r=client%2Fgetlocinfo&AddressPool[address]='+searchStr
            ,function(res){
            var s = '<ol>';
            res=$.parseJSON(res);
            for (var i=0;i<res.length && i<20;i++){
                oneres=res[i];
//              var city = $("#OrderQueue_city_id option[value="+oneres.city_id+"]").text();改为弹出框后没法获得其text值了，通过回调将城市名赋给cityName全局变量
                //var city = cityName;
		var city = $("input[name='city_list']").val();
                s +='<li style="margin: 2px 0pt; padding: 0pt 5px 0pt 3px; cursor: pointer; overflow: hidden; line-height: 17px;" data-addr="'+oneres.address+'" data-city="'+oneres.city_id+'" data-lng="'+oneres.lng+'" data-lat="'+oneres.lat+'">';
                s +='<span style="width:1px;background:url(<?php echo SP_URL_STO;?>www/images/red_labels.gif) 0 '+(2-i*20)+'px no-repeat;padding-left:10px;margin-right:3px"></span>';
                s +='<span class="placeTitle" style="color:#00c;text-decoration:underline">'+oneres.address+'</span>';
                s +='<span style="color:#666;"> - '+city+'</span>';
                s +='</li>';
            }
            s +='</ol>';
            s=$(s).find("li").click(function(){
                
                if($("#OrderQueue_city_id").val()!=$(this).data('city')){
                    if(!confirm("电话归属地和所选地点不在同一城市，是否继续派单？")){
                        $('#OrderQueue_address').val("");
                        return;
                    }
                }
                var point = new BMap.Point($(this).data("lng"),$(this).data("lat"));
                map.setCenter(point);
                searchkeyword=$(this).data('addr');
                map.addOverlay(new BMap.Marker(point));
                $('#OrderQueue_address').val($(this).find(".placeTitle").text());
            });
            $("#lib_poilist ol").html("").append(s);
        });
    }
})(jQuery);
</script>

<!--第三方合作鉴权弹框-->
<div id="partnerModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="partnerModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 id="partnerModalLabel">验证商家</h4>
    </div>
    <div class="modal-body" id="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true" id="close">关闭</button>
        <button class="btn btn-primary" id="continue" style="display: none">继续</button>
    </div>
</div>

<div id="control" style="display: none">
    <div>
        <div class="btn-group">
            <button class="btn" channel="03002" id="i_car_club">I车会</button>
            <button class="btn" channel="03004" id="ping_an" >平安保险</button>
        </div>
        <div class="alert alert-info" id="p_info" style="display:none">
        </div>
    </div>
</div>
<script type="text/html" id="ping_an_container">
    <p><strong>请提示客户告知卡号和密码</strong></p>
    <form class="form-horizontal">
        <div class="control-group">
            <label class="control-label" for="inputEmail">卡号</label>
            <div class="controls">
                <input type="text" id="ping_an_id_card" placeholder="卡号">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputPassword">密码</label>
            <div class="controls">
                <input type="password" id="ping_an_password" placeholder="密码">
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <input type="button" class="btn" id="ping_an_verify" value="验证" />
            </div>
        </div>
    </form>
</script>
<script type="text/javascript">
        $("#citydetail").click(function(){
            var citySelectValue = $("#OrderQueue_city_id").val();//被选中的城市value
            citySelectValue = (citySelectValue == '') ? 0 : citySelectValue;//弹出框未定位城市id为''
            var citydetail_url = '<?php echo Yii::app()->createUrl('client/citydetail');?>'+"&cvalue="+citySelectValue;
            $("#cru-frame-customer-invoice").attr("src", citydetail_url);
            $("#customer_invoice_dialog").dialog("open");
            return false;
        });
    /**
     * 第三方合作所需要JS
     */
    var ajax_url = '<?php echo Yii::app()->createUrl('client/partner');?>';
    var driver_search_url='<?php echo Yii::app()->createUrl('driver/search');?>';
    jQuery(document).ready(function() {

        //jQuery('#OrderQueue_address').change(function(){
        //    jQuery('#search_driver').attr('href',driver_search_url+"&address="+jQuery('#OrderQueue_address').val());
        //});

        //jQuery('#driver_search_id').click(function(){
          //  window.open(driver_search_url+'&city='+$('#OrderQueue_city_id').val()+'&map_address='+$('#OrderQueue_address').val());
        //});
        jQuery('#partner_verify_box').click(function() {
            var html = jQuery('#control').html();
            var o = jQuery(html);
            jQuery('#partnerModal').find('#modal-body').html(html);
            $('#partnerModal').modal('show');
        });

        jQuery('#ping_an').live('click', function(){
            jQuery(this).addClass('btn-inverse');
            jQuery(this).siblings('.btn').attr('class', 'btn');
            jQuery('#p_info').html(jQuery('#ping_an_container').html());
            jQuery('#p_info').show();
        });

        jQuery('#ping_an_verify').live('click', function(){
            var id_card = jQuery('#ping_an_id_card').val();
            var password = jQuery('#ping_an_password').val();
            var channel = '03004';
            if (id_card.length <= 0) {
                alert('请输入卡号');
                return false;
            }
            if (password.length <=0 ) {
                alert('请输入密码');
                return false;
            }
            var post_data = {
                'act' : 'verify',
                'channel' : channel,
                'params' : {
                    'id_card' : id_card,
                    'password' : password
                }
            };
            jQuery(this).val('验证中...');
            jQuery(this).attr('disabled', 'disabled');
            jQuery.get(
                ajax_url,
                post_data,
                function(d) {
                    if (d.status) {
                        var result = d.msg;
                        if (!result.status) {
                            alert(result.msg);
                        } else {
                            var html = "<p>此用户为平安保险客户，还可使用代驾服务<strong>"+result.msg+"</strong>次</p>";
                            jQuery('#p_info').html(html);
                            jQuery('#continue').show();
                            jQuery('#close').hide();
                            jQuery('#continue').bind('click', function(){
                                jQuery('#p_password').val(password);
                                jQuery('#p_id_card').val(id_card);
                                jQuery('#OrderQueue_channel').val(channel);
                                jQuery('#OrderQueue_name').val('平安会员');
                                $('#partnerModal').modal('hide');
                                jQuery('#partner_verify_box').hide();
                            });
                        }
                    }
                    jQuery('#ping_an_verify').val('验证');
                    jQuery('#ping_an_verify').removeAttr('disabled');

                },
                'json'
            );
        });

        jQuery('#i_car_club').live('click', function() {
            jQuery(this).addClass('btn-inverse');
            jQuery(this).siblings('.btn').attr('class', 'btn');
            var phone = jQuery('#OrderQueue_phone').val();
            var channel = jQuery(this).attr('channel');
            var usenum = jQuery('#OrderQueue_number').val();
            if (phone.length <=0) {
                alert('请输入客户电话');
                return false;
            }
            if (channel.length <=0) {
                alert('用户渠道不正确 ');
                return false;
            }
            jQuery('#modal-body').find('#p_info').html('查询中.....');
            jQuery('#modal-body').find('#p_info').show();
            var post_data = {
                'act' : 'verify',
                'channel' : channel,
                'params' : {
                    'phone' : phone,
                    'usenum' : usenum
                }
            }
            jQuery.get(
                ajax_url,
                post_data,
                function(d) {
                    if (d.status) {
                        var str = '';
                        var msg = d.msg;
                        var result = msg.result;
                        if (result.verify == 0) {
                            str += '<p>此用户'+phone+"为I车会会员。</p>";
                            if (parseInt(result.ablenum) > 0) {
                                str += "<p>请提示用户，此客户可享有免除10公里代驾服务费特权1次，其他费用正常收取。</p>";
                                str += "<p>稍后以短信形式将优惠?发放给客户，请客户注意查收。</p>";
                                jQuery('#modal-body').find('#p_info').html(str);
                                jQuery('#modal-body').find('#p_info').show();
                                jQuery('#continue').bind('click',function() {
                                    verify_succeed_callback(channel, 'I车会会员');
                                });
                                jQuery('#continue').show();
                                jQuery('#close').hide();
                            } else {
                                str += "<p>服务次数已经用尽，此优惠政策24小时内只限使用一次，此单无法进行优惠。</p>"
                                jQuery('#modal-body').find('#p_info').html(str);
                                jQuery('#modal-body').find('#p_info').show();
                                jQuery('#continue').hide();
                                jQuery('#close').show();
                            }
                        } else {
                            str += "<p>此用户"+phone+"不是I车会会员。请用户核实电话或重新验证其他商家。</p>";
                            jQuery('#modal-body').find('#p_info').html(str);
                            jQuery('#modal-body').find('#p_info').show();
                            jQuery('#continue').hide();
                            jQuery('#close').show();
                        }
                    }
                },
                'json'
            );
        })

        function verify_succeed_callback(channel, name) {
            jQuery('#OrderQueue_channel').val(channel);
            jQuery('#OrderQueue_name').val(name);
            jQuery('#OrderQueue_number').val(1);
            var o = jQuery('<label>根据协议，此单只可派遣一名司机</label>');
            jQuery('#tlt').before(o);
            jQuery('#OrderQueue_number').attr('readonly', 'readonly');
            $('#partnerModal').modal('hide');
            jQuery('#partner_verify_box').hide();
        }
    });
</script>

<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=504b96e42c0a4b4cdbfd27cbd9a7053d"></script>
<script type="text/javascript">
var map = new BMap.Map("map_canvas");
var opts = {anchor: BMAP_ANCHOR_TOP_RIGHT, offset: new BMap.Size(2, 2)};
map.enableScrollWheelZoom();
map.addControl(new BMap.NavigationControl(opts));           
map.centerAndZoom(new BMap.Point(116.404, 39.915), 15);

window.openInfoWinFuns = null;
var options = {
    /*LocalSearch检索完成后的回调函数。
    参数：results: LocalResult或Array<LocalResult> 如果是多关键字检索，回调函数参数返回一个LocalResult的数组，*/
    onSearchComplete: function(results){
        // 点击地址库搜索的直接跳过百度的结果
        if(typeof(searchkeyword)!="undefined"&&$("#OrderQueue_address").val()==searchkeyword){searchkeyword="";return;}
//      alert('搜索地址后的状态:'+local.getStatus()+"本次搜索的关键词："+results.keyword+"搜索所在的城市："+results.city+"搜索所在的省份："+results.province);
        switch (local.getStatus()){
            case BMAP_STATUS_CITY_LIST:
            case BMAP_STATUS_UNKNOWN_LOCATION://未知的位置在百度地图中不存在
                alert("注意：["+results.keyword+"] 该位置在所选城市的百度地图中无法定位,可能为无效的地址。请确认是否有误!");
                document.getElementById("map_poilist").innerHTML = "";//清空百度地图里面的搜索记录
                break;
            case BMAP_STATUS_INVALID_REQUEST:
                break;
            case BMAP_STATUS_SUCCESS:
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
                //下面代码为判断该搜索地址是否为所选城市匹配 如不匹配则清空所在位置的框让其重新输入
                var _searchCity = results.city;//本次检索所在的城市  如北京市
                var _selCity = $("input[name='city_list']").val();//选择的城市 如 北京  附：襄阳和襄樊认定为一个城市(百度地图为襄樊我们系统为襄阳)
                if(_searchCity == "襄樊市"){
                    _searchCity = "襄阳市";
                }
                if(_searchCity.indexOf(_selCity) == -1){
                    addrPropmt = "["+results.keyword+"] 定位于 ["+_searchCity+"] 可能不是所在城市 ["+_selCity+"] 的位置请更换!";
                    alert(addrPropmt);
                    $("#OrderQueue_address").val('');
                    $("#OrderQueue_address").focus();
                    document.getElementById("map_poilist").innerHTML = "";//清空百度地图里面的搜索记录
                }
                break;
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
//创建一个搜索类实例LocalSearch，其中location表示检索区域 LocalSearch(location:Map|Point|String[, opts:LocalSearchOptions])
var local = new BMap.LocalSearch(map, options);
local.disableFirstResultSelection();//禁用自动选择第一个检索结果。

</script>
 
