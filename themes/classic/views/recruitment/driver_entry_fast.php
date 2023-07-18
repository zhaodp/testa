<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZTY
 * Date: 13-6-17
 * Time: 下午5:45
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '司机签约';
?>
<h1 style="float: left; margin-right: 60px;">司机签约</h1>
<!--
<h1><?php echo CHtml::link('选择面试时间批量添加', Yii::app()->createUrl('/recruitment/createDriverEntry'))?></h1>
-->
<h1>
    <?php echo $city_list[$user_city_id];?>当前还有可用工号：<strong><?php echo $driver_id_count;?>个</strong>
    <?php echo CHtml::link('工号管理', Yii::app()->createUrl('/driver/address'), array("class"=>"btn btn-success", "style"=>"margin-left: 60px;")); ?>
</h1>
<form class="form-inline">
    <div class="input-prepend">
        <span class="add-on">请输入司机流水号：</span>
        <input type="text" name="" id="queue_number"/>
    </div>
    <input type="button" id="search" value="搜索&添加" class='btn btn-success',/>
</form>
<?php
if (is_array($data) && count($data)) {
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
<table class="table table-striped">
    <tr>
        <th>报名流水</th>
        <th>姓名</th>
        <th>身份证号</th>
        <th>驾照档案编号</th>
        <th>驾照类型</th>
        <th>领证时间</th>
        <th>司机工号</th>
        <th>V号</th>
        <th>手机号</th>
        <th>等级</th>
        <!--
        <th>担保状态</th>
        -->
        <th>操作</th>
    </tr>
<?php
    foreach ($data as $v) {
        $style_a['class'] = 'class'.$v['id'];
        $style_b['class'] = 'class'.$v['id'];
        $drop_style['class'] = 'class'.$v['id'];
        echo Yii::app()->controller->getRecordHtml($v, $style_a, $style_b, $drop_style, true);
    }
?>
</table>
<?php } ?>

<?php
$show = false;
if (is_array($entry_driver) && count($entry_driver) && $data) {
    $show  = true;
    echo '今天已经签约司机共'.count($entry_driver).'人';
}
?>
<table id="entry_list" <?php if(!$show) echo 'style="display:none;"'; ?> class="table table-striped">
    <tr>
        <th style="width: 80px;">姓名</th>
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
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">移除</h3>
    </div>
    <div class="modal-body">
        <table>
            <tr>
                <td colspan="2">
                移除理由：
                <select id="move_reason">
                    <option value="i">面试不合格</option>
                    <option value="r">路考不通过</option>
                </select>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="hidden" id="r_id" value='' />
                    <input type="hidden" id="q_id" value="" />
                    <a href="javascript:void(0)" id="interview_next" class="btn btn-success">下次通知</a>
                    <a href="javascript:void(0)" id="interview_recycle"class="btn btn-success">直接回收</a>
                    <a href="javascript:void(0)" id="road_recycle" style="display:none" class="btn btn-success">直接回收</a>
                </td>
            </tr>
        </table>

    </div>
    <div class="modal-footer">
        <!--
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
        <button class="btn btn-primary">Save changes</button>
        -->
    </div>
</div>
<!-- Modal -->
<script type="text/javascript">

    jQuery(document).ready(function(){
        jQuery('#search').click(function(){
            var queue_name = jQuery('#queue_number').val();
            if (queue_name == '') {
                alert('请输入报名流水号');
                return false;
            }
            var is_exist = false;
            jQuery('.q_n').each(function(){
                if (jQuery(this).html() == queue_name) {
                    is_exist = true;
                }
            });
            if (is_exist) {
                alert('该流水号已经存在列表中');
                return false;
            }
            jQuery.get(
                '<?php echo Yii::app()->createUrl('/recruitment/ajax');?>',
                {
                    action : 'setQueueNumberDriverEntry',
                    queue_number : queue_name
                },
                function (d) {
                    if (d.status) {
                        window.location.reload();
                    } else {
                        alert('流水号错误或该司机状态不符合要求');
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
            if (jQuery('#interview_'+id).val() == '') {
                jQuery('#road_'+id).attr('disabled','true');
                jQuery('#rank_'+id).attr('readonly','true');
            }
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
                    'id_driver_card' : jQuery('#id_driver_card_'+id).val(),
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
                    } else {
                        alert(d.error_msg);
                    }
                },
                'json'
            );
        });

        jQuery('[func="remove"]').click(function() {
            var id = jQuery(this).attr('id').replace('remove','');
            var queue = jQuery(this).attr('queue');
            jQuery('#r_id').val(id);
            jQuery('#q_id').val(queue);
            $('#myModal').modal('show');
            /*
            if (confirm('确认删除?')) {
                jQuery.post(
                    '<?php echo Yii::app()->createUrl('/recruitment/ajax'); ?>',
                    {
                        action : 'driver_entry_remove',
                        queue_number: queue
                    },
                    function(d) {
                        if (d.status) {
                            jQuery('#tr_'+id).remove();
                        } else {
                            alert('删除失败');
                        }
                    },
                    'json'
                );
            }
            */
        });

        jQuery('#interview_recycle').click(function() {
            var id = jQuery('#r_id').val();
            if (!id) {
                alert('操作失败，请重新操作');
                return false;
            }
            jQuery.post(
                '<?php echo Yii::app()->createUrl('/recruitment/interview'); ?>',
                {
                    'id' : id,
                    'name' : jQuery('#name_'+id).val(),
                    'id_card' : jQuery('#id_card_'+id).val(),
                    'id_driver_card' : jQuery('#id_driver_card_'+id).val(),
                    'driver_type' : jQuery('#driver_type_'+id).val(),
                    'driver_year' : jQuery('#driver_year_'+id).val(),
                    'interview' : '-2',
                    'road' : '',
                    'rank' : '',
                    'submit' : true
                },
                function(d) {
                    if (d.status) {
                        var obj = jQuery('.class'+id);
                        jQuery('#tr_'+id).remove();
                        $('#myModal').modal('hide');
                    }
                },
                'json'
            );

        });

        jQuery('#interview_next').click(function(){
            var id = jQuery('#r_id').val();
            if (!id) {
                alert('操作失败，请重新操作');
                return false;
            }
            jQuery.post(
                '<?php echo Yii::app()->createUrl('/recruitment/interview'); ?>',
                {
                    'id' : id,
                    'name' : jQuery('#name_'+id).val(),
                    'id_card' : jQuery('#id_card_'+id).val(),
                    'id_driver_card' : jQuery('#id_driver_card_'+id).val(),
                    'driver_type' : jQuery('#driver_type_'+id).val(),
                    'driver_year' : jQuery('#driver_year_'+id).val(),
                    'interview' : '-1',
                    'road' : '',
                    'rank' : '',
                    'submit' : true
                },
                function(d) {
                    if (d.status) {
                        var obj = jQuery('.class'+id);
                        jQuery('#tr_'+id).remove();
                        $('#myModal').modal('hide');
                    }
                },
                'json'
            );
        });

        jQuery('#road_recycle').click(function() {
            var id = jQuery('#r_id').val();
            if (!id) {
                alert('操作失败，请重新操作');
                return false;
            }
            jQuery.post(
                '<?php echo Yii::app()->createUrl('/recruitment/interview'); ?>',
                {
                    'id' : id,
                    'name' : jQuery('#name_'+id).val(),
                    'id_card' : jQuery('#id_card_'+id).val(),
                    'id_driver_card' : jQuery('#id_driver_card_'+id).val(),
                    'driver_type' : jQuery('#driver_type_'+id).val(),
                    'driver_year' : jQuery('#driver_year_'+id).val(),
                    'interview' : '',
                    'road' : '-2',
                    'rank' : '',
                    'submit' : true
                },
                function(d) {
                    if (d.status) {
                        var obj = jQuery('.class'+id);
                        jQuery('#tr_'+id).remove();
                        $('#myModal').modal('hide');
                    }
                },
                'json'
            );
        });

        jQuery('#move_reason').change(function(){
            if (jQuery(this).val() == 'i') {
                jQuery('#road_recycle').hide();
                jQuery('#interview_next').show();
                jQuery('#interview_recycle').show();
            } else {
                jQuery('#road_recycle').show();
                jQuery('#interview_next').hide();
                jQuery('#interview_recycle').hide();
            }
        });

        jQuery('[func="entry"]').click(function() {
            var id = jQuery(this).attr('id').replace('entry','');
            var driver_phone = jQuery('#driver_phone_'+id).val();
            driver_phone = driver_phone.replace(/(^\s*)|(\s*$)/g,"");
            var v_code = jQuery('#v_'+id).val();
            v_code = v_code.replace(/(^\s*)|(\s*$)/g,"");
            var assure = jQuery('#assure_'+id).val();
            var driver_id = jQuery('#driver_id_'+id).val();
            driver_id = driver_id.replace(/(^\s*)|(\s*$)/g,"");
            if (!id) {
                alert('出错');
                return false;
            }
            if (!driver_phone) {
                alert('请输入手机号');
                return false;
            }
            /*
            if (!isMobelNumber(driver_phone)) {
                alert('请输入正确的手机号码');
                return false;
            }
            */
//            if (!v_code) {
//                alert('请输入V号');
//                return false;
//            }
            /*
            if (!assure) {
                alert('请选择担保状态');
                return false;
            }
            */
            jQuery.post(
                '<?php echo Yii::app()->createUrl('/recruitment/ajax'); ?>',
                {
                    action : 'driver_entry',
                    id : id,
                    driver_phone : driver_phone,
                    v_code : v_code,
                    assure : assure,
                    driver_id : driver_id,
                    is_fast : true
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

        function isMobelNumber(value)
        {
            if(/^13\d{9}$/g.test(value)||(/^15[0-35-9]\d{8}$/g.test(value))||
                (/^18[05-9]\d{8}$/g.test(value))){
                return true;
            }else{
                return false;
            }
        }
    });

</script>