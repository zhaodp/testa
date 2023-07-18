<h1 style="float: left; margin-right: 50px;">司机签约</h1> <h1><?php echo CHtml::link('快捷签约', Yii::app()->createUrl('/recruitment/driverfastentry'))?></h1>
<form action="" method="post" id="frm" class="form-inline">
    面试时间：
    <?php
    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
        'attribute'=>'batch_start',
        'language'=>'zh_cn',
        'name'=>'batch_start',
        'value'=>$batch_start,
        'options'=>array(
            'showAnim'=>'fold',
            'showOn'=>'both',
            //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
            'buttonImageOnly'=>true,
            'maxDate'=>'new Date()',
            'dateFormat'=>'yymmdd',
            'changeYear'=>true,
            'changeMonth'=> true,
        ),
        'htmlOptions'=>array(
            'style'=>'width:120px',
        ),
    ));
    ?>
    ——
    <?php
    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
        'attribute'=>'visit_time',
        'language'=>'zh_cn',
        'name'=>'batch_end',
        'value'=>$batch_end,
        'options'=>array(
            'showAnim'=>'fold',
            'showOn'=>'both',
            //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
            'buttonImageOnly'=>true,
            'maxDate'=>'new Date()',
            'dateFormat'=>'yymmdd',
            'changeYear'=>true,
            'changeMonth'=> true,
        ),
        'htmlOptions'=>array(
            'style'=>'width:120px',
        ),
    ));
    ?>
    &nbsp;&nbsp;&nbsp;
    <input type="hidden" id="queue_number" name="queue_number" value=""/>
    <input type="submit" id="search" value="确定" class='btn btn-success',/>
</form>
<?php if ($recruitmentList) {?>
    <?php
    $style_a = array(
        'style'=>'width:60px',
        'readonly'=> true,
    );
    $style_b = array(
        'style'=>'width:150px',
        'readonly'=> true,
    );
    $drop_style = array(
        'style'=>'width:60px',
        'disabled'=>'disabled',
    );
    ?>
<table id="data_record" class="table table-striped">
    <tr>
        <th>报名流水</th>
        <th>姓名</th>
        <th>身份证号</th>
        <th>驾照类型</th>
        <th>领证时间</th>
        <th>V号</th>
        <th>手机号</th>
        <th>等级</th>
        <!--
        <th>担保状态</th>
        -->
        <th>操作</th>
    </tr>
    <?php
    $exist = array();
    foreach($recruitmentList as $v) {
        $style_a['class'] = 'class'.$v['id'];
        $style_b['class'] = 'class'.$v['id'];
        $drop_style['class'] = 'class'.$v['id'];
        $exist[] = $v['id'];
    ?>
        <?php echo Yii::app()->controller->getRecordHtml($v, $style_a, $style_b, $drop_style);?>
    <?php } ?>
    <?php
    if (is_array($queue_data) && count($queue_data)) {
        if (!in_array($queue_data, $exist)) {
        echo Yii::app()->controller->getRecordHtml($queue_data, $style_a, $style_b, $drop_style);
        }
    }
    ?>
</table>
<?php } ?>
<?php if ($recruitmentList) {?>
<form class="form-inline">
<table>
    <tr>
        <td>请输入报名流水号：<input type="text" id="queue" style="width:120px;"/></td>
        <td><input type="button" id="search_queue" class="btn btn-success" value='增加'/></td>
    </tr>
</table>
</form>
<?php } ?>

<?php
$show = false;
if (is_array($entry_driver) && count($entry_driver) && $recruitmentList) {
    $show  = true;
    echo '今天已经签约司机共'.count($entry_driver).'人';
}
?>
    <table id="entry_list" <?php if(!$show) echo 'style="display:none;"'; ?> class="table table-striped">
        <tr>
            <th style="width:80px">姓名</th>
            <th>工号</th>
        </tr>
        <?php
        foreach ($entry_driver as $v) {
            ?>
            <tr>
                <td><?php echo $v['name'];?></td>
                <td><?php echo $v['user'];?></td>
            </tr>
        <?php
        }
        ?>
    </table>

<script type="text/javascript">
    window.onload = function() {
        jQuery('.ui-datepicker-trigger').remove();
    }

    jQuery(document).ready(function(){

        jQuery('#search').click(function(){
            var batch_start = jQuery('#batch_start').val();
            var batch_end = jQuery('#batch_end').val();
            if (!batch_start || !batch_start) {
                alert('请输入时间');
                return false;
            }
            jQuery.get(
                '<?php echo Yii::app()->createUrl('/recruitment/ajax');?>',
                {
                    action : 'setQueueNumber',
                    queue_number : queue_name
                },
                function (d) {
                    if (d.status) {
                        window.location.reload();
                    }
                },
                'json'
            )
        });

        jQuery('[func="update"]').click(function(){
            var id =jQuery(this).attr('id').replace('update','');
            var obj = jQuery('.class'+id);
            var button = jQuery(this);
            obj.attr('readonly',false);
            obj.attr('disabled',false);
            jQuery(this).hide();
            jQuery('#submit'+id).show();
        });

        jQuery('[func="submit"]').click(function(){
            var id = jQuery(this).attr('id').replace('submit','');
            jQuery.post(
                '<?php echo Yii::app()->createUrl('/recruitment/interview'); ?>',
                {
                    'id' : id,
                    'name' : jQuery('#name_'+id).val(),
                    'id_card' : jQuery('#id_card_'+id).val(),
                    'driver_type' : jQuery('#driver_type_'+id).val(),
                    'driver_year' : jQuery('#driver_year_'+id).val(),
                    'driver_phone' : jQuery('#driver_phone_'+id).val(),
                    'rank' : jQuery('#rank_'+id).val(),
                    'submit' : true
                },
                function(d) {
                    if (d.status) {
                        var obj = jQuery('.class'+id);
                        obj.attr('readonly',true);
                        obj.attr('disabled',true);
                        jQuery('#submit'+id).hide();
                        jQuery('#update'+id).show();
                    }
                },
                'json'
            );
        });

        jQuery('#search_queue').click(function(){
            var queue = jQuery('#queue').val();
            if (!queue) {
                alert('请输入流水号');
                return false;
            }
            var exist = false;
            jQuery('.q_n').each(function(){
                if (jQuery(this).html() == queue) {
                    exist = true;
                }
            });
            if (exist) {
                alert('该司机已经存在列表中');
            } else {
                jQuery('#queue_number').val(queue);
                jQuery('#frm').submit();
            }
        });

        jQuery('[func="entry"]').click(function() {
            var id = jQuery(this).attr('id').replace('entry','');
            var driver_phone = jQuery('#driver_phone_'+id).val();
            var v_code = jQuery('#v_'+id).val();
            var assure = jQuery('#assure_'+id).val();
            if (!id) {
                alert('出错');
                return false;
            }
            if (!driver_phone) {
                alert('请输入手机号');
                return false;
            }
            if (!v_code) {
                alert('请输入V号');
                return false;
            }
            /*
            if (!assure) {
                alert('请选择担保状态');
                return false;
            }
            */
            jQuery('this').val('处理中..');
            jQuery.get(
                '<?php echo Yii::app()->createUrl('/recruitment/ajax'); ?>',
                {
                    action : 'driver_entry',
                    id : id,
                    driver_phone : driver_phone,
                    v_code : v_code,
                    assure : assure
                },
                function(d) {
                    if (d.status) {
                        var driver = d.data;
                        alert('签约成功，工号为：'+ driver.driver_id);
                        getEntryList(driver);
                        jQuery('#tr_'+id).hide();
                        return false;
                    } else {
                        alert(d.data);
                        jQuery('entry'+id).val('签约');
                        return false;
                    }
                },
                'json'
            );
        });

        jQuery('[name="v"]').blur(function() {
            var id = jQuery(this).attr('id').replace('v_','');
            var v_code = jQuery.trim(jQuery(this).val());
            var reg = /v[0-9]/i;
            var p1 = /^(13\d{9})|(15\d{9})|(18\d{9})|(0\d{10,11})$/;
            if (v_code.length != 0) {
                if (!reg.test(v_code)) {
                    alert('请输入正确的V号');
                    jQuery(this).val('');
                    jQuery(this).focus();
                    return false;
                }
            } else {
                return false;
            }
            jQuery.get(
                '<?php echo Yii::app()->createUrl('/recruitment/ajax'); ?>',
                {
                    action : 'get_phone_by_v',
                    v_code : v_code
                },
                function (d) {
                    if (d.status) {
                        var phone_info = d.msg;
                        if (p1.test(phone_info.phone)) {
                            jQuery('#driver_phone_'+id).val(phone_info.phone);
                            jQuery('#driver_phone_'+id).attr('readonly', true);
                            jQuery('#driver_phone_'+id).focus();
                        } else {
                            jQuery('#driver_phone_'+id).val('').focus();
                        }
                    } else {
                        alert('V号有误');
                        jQuery('#v_'+id).val('').focus();
                    }
                },
                'json'
            );
        });

        function getEntryList(d) {
            var html = "<tr>";
                html += "<td>"+ d.name+"</td>";
                html += "<td>"+ d.driver_id + "</td>";
                html += "</tr>";
            var o = jQuery(html);
            jQuery('#entry_list').append(o);
        }

    });

</script>