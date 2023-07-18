<h1>司机面试</h1>
<form class="form-inline">
    <table>
        <tr>
            <td>请输入报名流水号：<input type="text" name="" id="queue_number"/></td>
            <td><input type="button" id="search" value="搜索&添加" class='btn btn-success',/></td>
        </tr>
    </table>
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
        <th>面试</th>
        <!--
        <th>路考</th>
        <th>总评分</th>
        -->
        <th>操作</th>
    </tr>
<?php
    foreach ($data as $v) {
        $style_a['class'] = 'class'.$v['id'];
        $style_b['class'] = 'class'.$v['id'];
        $drop_style['class'] = 'class'.$v['id'];
?>
    <tr id="tr_<?php echo $v['id'];?>">
        <td><?php echo Yii::app()->controller->getRecruitmentQueueNumber($v['id'], $v['city_id']);?></td>
        <td><?php echo CHtml::textField('name', $v['name'],$style_a+array('id'=>'name_'.$v['id']));?></td>
        <td><?php echo CHtml::textField('id_card', $v['id_card'], $style_b+array('id'=>'id_card_'.$v['id']));?></td>
        <td><?php echo CHtml::textField('id_driver_card', $v['id_driver_card'], $style_b+array('id'=>'id_driver_card_'.$v['id']));?></td>
        <td><?php echo CHtml::dropDownList('driver_type', $v['driver_type'], Dict::items('driver_type'),$drop_style+array('id'=>'driver_type_'.$v['id']));?></td>
        <td><?php echo CHtml::textField('driver_year', date('Y-m-d',$v['driver_year']),$style_b+array('id'=>'driver_year_'.$v['id']));?></td>
        <td><?php echo CHtml::dropDownList('interview', $v['interview'], $this->interview_dict, array('id'=>'interview_'.$v['id']));?></td>
        <!--
        <td><?php echo CHtml::dropDownList('road', $v['road'], $this->road_dict, $drop_style+array('id'=>'road_'.$v['id']));?></td>
        <td><?php echo CHtml::textField('rank', $v['rank'], $style_a+array('id'=>'rank_'.$v['id']));?></td>
        -->
        <td>
            <?php echo CHtml::button('submit',array('value'=>'修改','class'=>"btn btn-success", 'func'=>'update','id'=>'update'.$v['id']));?>
            <?php echo CHtml::button('submit',array('value'=>'保存','class'=>"btn btn-success", 'func'=>'submit','id'=>'submit'.$v['id'], 'style'=>'display:none'));?>
        </td>
    </tr>
<?php } ?>
</table>
<?php } ?>
<script type="text/javascript">

    jQuery(document).ready(function(){
        jQuery('#search').click(function(){
            var queue_name = jQuery('#queue_number').val();
            if (queue_name == '') {
                alert('请输入报名流水号');
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
                    } else {
                        alert('该司机状态不符要求');
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

        jQuery('[name="interview"]').change(function(){
            var id = jQuery(this).attr('id').replace('interview_','');
            var value = jQuery(this).val();
            if (value != '') {
                jQuery('#update'+id).hide();
                jQuery('#submit'+id).show();
            }
            /*
            if (value =='' || value == '-1' || value == '-2') {
                jQuery('#road_'+id).val('');
                jQuery('#road_'+id).attr('disabled','disabled');
                jQuery('#rank_'+id).val('');
            } else {
                jQuery('#road_'+id).attr('disabled',false);
            }
            var road = jQuery('#road_'+id).val();
            if (road == 'A' || road == 'B' || road == 'C') {
                var rank = value > road ? value : road;
                jQuery('#rank_'+id).val(rank);
            }
            */

        });

        jQuery('[name="road"]').change(function(){
            var value = jQuery(this).val();
            var id = jQuery(this).attr('class').replace('class','');
            if (value != '-2') {
                var interview = jQuery('#interview_'+id).val();
                var road = jQuery('#road_'+id).val();
                var rank = interview > road ? interview : road;
                jQuery('#rank_'+id).val(rank);
            } else {
                jQuery('#rank_'+id).val('');
            }
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
                    'interview' : jQuery('#interview_'+id).val(),
                    'submit' : true
                },
                function(d) {
                    if (d.status) {
                        var obj = jQuery('.class'+id);
                        obj.attr('readonly',true);
                        obj.attr('disabled',true);
                        jQuery('#submit'+id).hide();
                        jQuery('#update'+id).show();
                        if (
                            jQuery('#interview_'+id).val() == '-1' ||
                            jQuery('#interview_'+id).val() == '-2' ||
                            jQuery('#interview_'+id).val() == 'A' ||
                            jQuery('#interview_'+id).val() == 'B' ||
                            jQuery('#interview_'+id).val() == 'C'
                            ) {
                            jQuery('#tr_'+id).remove();
                        }
                    } else {
                        alert(d.error_msg);
                    }
                },
                'json'
            );
        });

    });

</script>
