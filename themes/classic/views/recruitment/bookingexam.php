<div id = "data" style="display: none;"><?php  print_r($month_data) ?></div>
<div id = "city_id" style="display: none;"><?php  print_r($city_id) ?></div>
<input type="hidden" value="<?php echo $used_num['h1']?>" id="total-apm-driver1"/>
<input type="hidden" value="<?php echo $used_num['h2']?>" id="total-apm-driver2"/>
<input type="hidden" value="<?php echo $used_num['h3']?>" id="total-apm-driver3"/>
<div class="calendar-box">
    <h1>预约管理</h1>
<?php
if(0 == Yii::app()->user->city){
$form = $this->beginWidget('CActiveForm'
);

$city = Dict::items('city');
    $city[-1] ='--选择城市--';
    unset($city[0]);
echo CHtml::label('城市选择','city'); 
echo CHtml::dropDownList('city_list',
    $city_id,
    $city,
    array()
    );
?>
<input class="btn btn-success" type="submit" value="查询" />
<?php $this->endWidget();
} ?>

    <div id='calendar'></div>
</div>
<div class="cms-box">
    <div id="cms">
        <div class="cms-item">
            <div class="cms-title">【路考预约短信】<br/>
师傅您好，您预约了e代驾XX分公司XX月XX日XX:XX的路考。</div>
            <div><textarea id="apms-txt" rows="20" placeholder="请您携带以下证件及物品准时参加路考：身份证、驾驶证、开通网银的银行卡、安卓智能手机。
路考地点：
联系电话：
公司地址："><?php echo $sms ?></textarea></div>
            <div><a class="button" id="save-amp">保存</a></div>
        </div>
    </div>
    <div class='ws-slot-form' <?php if($hide_change_hours) {?>style="display:none;"<?php }?>>
    <div class='ws-slot-title'>预约时间段：</div>
    <div class='ws-slot-item'>
        <div class='ws-slot-start'><label for='ws-slots1'>开始时间：</label><input id='ws-slots1' placeholder='开始时间'
        maxlength="5" value='<?php echo $hours_setting['hour_1_start']?>'/></div>
        <div class='ws-slot-end'><label for='ws-slote1'>结束时间：</label><input id='ws-slote1' placeholder='结束时间'
                                                                            maxlength="5" value='<?php echo $hours_setting['hour_1_end']?>'/></div>
    </div>
    <div class='ws-slot-item'>
        <div class='ws-slot-start'><label for='ws-slots2'>开始时间：</label><input id='ws-slots2' placeholder='开始时间'
        maxlength="5" value='<?php echo $hours_setting['hour_2_start']?>'/></div>
        <div class='ws-slot-end'><label for='ws-slote2'>结束时间：</label><input id='ws-slote2' placeholder='结束时间'
        maxlength="5" value='<?php echo $hours_setting['hour_2_end']?>'/></div>
    </div>
    <div class='ws-slot-item'>
        <div class='ws-slot-start'><label for='ws-slots3'>开始时间：</label><input id='ws-slots3' placeholder='开始时间'
        maxlength="5" value='<?php echo $hours_setting['hour_3_start']?>'/></div>
        <div class='ws-slot-end'><label for='ws-slote3'>结束时间：</label><input id='ws-slote3' placeholder='结束时间'
        maxlength="5" value='<?php echo $hours_setting['hour_3_end']?>'/></div>
    </div>
    <div class='ws-slot-item'><a class='button' id='save-slot'>保存</a></div>
</div>
</div>



<script id="editTableTmpl" type="text/x-easy-template">
    <table id="edit-table" cellpadding="0" cellspacing="0" border="0" width="100%">
        <thead>
        <tr>
            <th colspan="3" id="apm-title">日程管理【${data.date}】</th>
        </tr>
        <tr>
            <th> 时间段</th>
            <th> 人数</th>
            <th> 当前已约人数</th>
        </tr>
        </thead>
        <tbody>
        <#if (data.events.length > 0)>
            <#list data.events as list>
                <tr>
                    <td>${list.startTime}-${list.endTime}</td>
                    <td><input type="text" value="${list.sum}" maxlength="3"/></td>
                    <td>${list.ren}</td>
                </tr>
            </#list>
        </#if>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" data-date="${data.date}"><a id="add" class="button">添加</a></td>
        </tr>
        </tfoot>
    </table>
</script>
<script id="evtTmpl" type="text/x-easy-template">
    <table id="evt-table" cellpadding="0" cellspacing="0" border="0" width="100%">
        <thead>
        <tr>
            <th colspan="3" id="evt-title">日程管理【${data.date}】</th>
        </tr>
        <tr>
            <th> 时间段</th>
            <th> 人数</th>
            <th> 当前已约人数</th>
        </tr>
        </thead>
        <tbody>
        <#if (data.events.length > 0)>
            <#list data.events as list>
                <tr>
                    <td>${list.startTime}-${list.endTime}</td>
                    <td><input type="text" value="${list.sum}" maxlength="3"/></td>
                    <td>${list.ren}</td>
                </tr>
            </#list>
        </#if>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" data-date="${data.date}"><a id="update" class="button">修改</a></td>
        </tr>
        </tfoot>
    </table>
</script>


