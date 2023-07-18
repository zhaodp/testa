<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-7-10
 * Time: 下午2:59
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '在线预约面试';
?>
<style>
    .bo {
        background: url("http://concierge.apple.com/resources/styles/../images/timepicker_seperator.png") repeat-y scroll left top transparent;
        float: left;
        height: 355px;
        padding: 0 14px 0 16px;
        position: relative;
        cursor: pointer;
    }

    .da {
        text-align: center;
        font-weight: bold;
        margin-bottom: 2px;
        line-height: 5px;"
    }

    .de {
        color: #FFFFFF;
        font-size: 15px;
        font-weight: normal;
    }

    .thumbnail {
        background-color: #FFFFFF;
        border: 1px solid #DDDDDD;
        border-radius: 4px 4px 4px 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.055);
        display: block;
        line-height: 20px;
        margin-top: 18px;
        padding: 4px;
        transition: all 0.2s ease-in-out 0s;
    }
</style>
<div class="block">
	<div style="height:67px;"></div>
	<section id="agreement" class="agreement">
		<div class="page-header">
			<h2><?php echo $this->pageTitle; ?></h2>
		</div>
		<div>
		</div>
	</section>
</div>

<form class="form-inline" action="" method="get">
    <?php
    $city_list = Dict::items('city');
    $city_list[0] = '请选择城市';
    ?>
    <?php echo CHtml::dropDownList('city_id', $city_id, $city_list); ?>
    <input type="hidden" name="act" value="interview" />
    <input type="submit" class="btn" value="查询"/>
</form>

<?php
$time_list = array();
foreach (DriverInterviewTime::$moring as $k=>$v) {
    $time_list[$k] = $v;
}
foreach (DriverInterviewTime::$afternoon as $k=>$v) {
    $time_list[$k] = $v;
}
?>

    <div class="button_container" id="buttonBarC" style=" color: #333333;">
        <div class="hero-unit" style="background-color: #737B89; padding: 20px">
            <div class="row" id="container">

            </div>
        </div>
    </div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">预约面试时间</h3>
    </div>
    <div class="modal-body">
        <p>您已经选择在<strong id="date"></strong><strong id="time"></strong>参加面试</p>
        <div>
            请输入身份证号，确认预约：
            <div><input type="text" name="id_card" ></div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        <button class="btn btn-primary" id="submit">确认预约</button>
    </div>
</div>

    <!-- Modal -->
    <div id="myWaring" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">提示</h3>
        </div>
        <div class="modal-body">
            <p>One fine body…</p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">确认</button>
        </div>
    </div>


    <!-- Modal -->
    <div id="myUpdate" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">提示</h3>
        </div>
        <div class="modal-body">

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" func="interview_update">确认修改</button>
            <button class="btn" data-dismiss="modal" aria-hidden="true">不修改</button>
        </div>
    </div>


<script>
    var interview_data = <?php echo json_encode($interview_time_data);?>;
    var data_count = <?php echo is_array($interview_time_data) && count($interview_time_data) ? count($interview_time_data) : 0;?>;
    var time_list = <?php echo json_encode($time_list);?>;
    var city_id = <?php echo intval($city_id);?>;
    var code = 1;
    var ajax_url = '<?php echo Yii::app()->createUrl('/recruitment/queue'); ?>';
    $(function () {
        if (data_count > 0) {
            jQuery.each(interview_data, function(i,v){
                var html = '<div class="span2 bo">'+
                    '<h3 class="da">'+
                        '<a class="de">'+
                            v['date'] +
                        '</a>'+
                    '</h3>';
                    jQuery.each(v['info'], function(d,t){
                        var str = '预约已满';
                        var m = t;
                        if (parseInt(v['interview_num'] - m) > 0) {
                            str = parseInt(v['interview_num'] - m) + '可用';
                            html += '<div class="thumbnail" onclick="initModal('+"'"+v['date']+"'"+','+d+')">';
                        } else {
                            html += '<div class="thumbnail" style="background-color: #9BA2AC;">';
                        }
                            html += '<div style="text-align: center">'+time_list[d]+'</div>'+
                                    '<div style="text-align: center">'+str+'</div>'+
                               '</div>';
                    });
                html +='</div>';
                var o = jQuery(html);
                jQuery('#container').append(o);
            });
        } else if(city_id>0) {
            var html = "<div style='line-height: 300px; text-align: center'><strong>没有可用预约时间，请联系当地司机管理部门。</strong></div>";
            jQuery('#container').append(html);
        } else {
            jQuery('#buttonBarC').hide();
        }
    })

    function initModal(date, time) {
        var mydate = new Date(date);
        //var html = jQuery('#myModal').html();
        //var o = jQuery(html);
        var o = jQuery('#myModal');
        //window['o'] = o;

        o.find('#date').html(mydate.format("yyyy年MM月dd日"));
        o.find('#time').html(time+'时');
        o.find('#submit').unbind('click', function(){});
        o.find('#submit').bind('click', function(){
            var id_card = o.find('[name="id_card"]').val();
            if (id_card.length != 18) {
                alert('请输入正确身份证号');
                return false;
            }
            jQuery.get(
                ajax_url,
                {
                    date : date,
                    time : time,
                    id_card : id_card,
                    act : 'booking',
                    select_city_id : city_id
                },
                function(d) {
                    if (d.status) {
                        alert('预约成功');
                        //window.location.reload();
                        o.modal('hide');
                    } else {
                        o.modal('hide');
                        var html = '';
                        switch(d.msg){
                            case '1101':
                                html +="<p>您还没有进行报名，请点击<a href='http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>/signup' >这里</a>进行报名</p>";
                                break;

                            case '1102':
                                html += "<p>您还没有通过在线考试，请点击<a href='http://zhaopin.<?php echo Common::getDomain(SP_HOST);?>'>这里</a>进行在线考试。</p>";
                                break;

                            case '1103':
                                html += "<p>您已通过面试，请耐心等待路考/签约通知。谢谢！</p>";
                                break;

                            case '1104':
                                html += "<p>您已通过面试及路考，请耐心等待签约通知。谢谢！</p>";
                                break;

                            case '1105':
                                html += "<p>您已经签约</p>";
                                break;

                            case '1106':
                                html += '<p>状态有误，不能预约面试</p>';
                                break;

                            case '1107':
                                var info = d.data;
                                var already = new Date(info.date);
                                html += '<p>您已有预约在'+already.format("yyyy年MM月dd日")+info.time+'时，是否修改至'+mydate.format("yyyy年MM月dd日")+time+'时，确认修改请点击“确认修改”按钮，不更改面试时间请点”不修改“</p>';
                                break;

                            case '1108':
                                html += '<p>该时间已经无可预约名额<p>';
                                break;

                            case '1109':
                                html +='<p>城市选择有误，请选择您报名城市 </p>';
                                break;

                            case '1110':
                                html += '<p>该时间不可预约或已经取消，请刷新页面后重试。</p>';
                                break;
                        }
                        if (d.msg == 1107) {
                            var update_modal = jQuery('#myUpdate');
                            update_modal.find('.modal-body').html(html);
                            update_modal.unbind('click');
                            update_modal.find('[func="interview_update"]').bind('click',function(){
                                jQuery.get(
                                    ajax_url,
                                    {
                                        p_id : info['p_id'],
                                        code : info['code'],
                                        interview_date : date,
                                        interview_time : time,
                                        act : 'change'
                                    },
                                    function(d){
                                        update_modal.modal('hide');
                                        if (d.status) {
                                            alert('修改成功');
                                            window.location.reload();
                                        } else {
                                            alert(d.msg);
                                        }
                                    },
                                    'json'
                                )
                            });
                            update_modal.modal('show');
                        } else {
                            var modal = jQuery('#myWaring');
                            modal.find('.modal-body').html(html);
                            modal.modal('show');
                        }
                    }

                },
                'json'
            );
        });
        o.modal('show');
    }

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

</script>


