<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-11-6
 * Time: 下午1:58
 * auther mengtianxue
 */
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bonus-library-form',
        'enableAjaxValidation' => false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div style="width:30%; float: left;">
            <?php echo $form->hiddenField($model, 'bonus_sn'); ?>

            <?php echo $form->labelEx($model, '城市'); ?>
            <?php
            $city = array('请选择') + $area;
            echo $form->dropDownList($model, 'bonus_id', $city, array('onchange' => 'select_channel()', 'style' => 'width:120px')); ?>
        </div>

        <div style="width:30%; float: left;">
            <?php echo $form->labelEx($model, '渠道'); ?>
            <?php echo $form->dropDownList($model, 'owner', array(), array('style' => 'width:120px','empty' => '选择渠道')); ?>

        </div>

        <div style="width:13%; float: left;">
            <?php echo $form->labelEx($model, '&nbsp;'); ?>
            <a href="javascript:void(0);" onclick="is_shows()">添加渠道</a>
        </div>

        <div style="width:27%; float: left;">
            <?php echo $form->labelEx($model, '&nbsp;'); ?>
            <?php echo CHtml::submitButton($model->isNewRecord ? '分配' : 'Save', array('class' => 'btn', 'style' => 'width:120px')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

    <div id="add_channel" style="display: none;">
        <div class="row">
            <?php echo $form->labelEx($modelChannel, 'area_id'); ?>
            <?php echo $form->dropDownList($modelChannel, 'area_id', Dict::items('city')); ?>
            <?php echo $form->error($modelChannel, 'area_id'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($modelChannel, 'channel'); ?>
            <?php echo $form->textField($modelChannel, 'channel'); ?>
        </div>

        <div class="row">
            <?php echo CHtml::Button('添加', array('class' => 'btn', 'onclick' => 'add_bonus_channel()')); ?>
        </div>
    </div>

</div><!-- form -->
<script type="text/javascript">
    $("document").ready(function () {
        var str = "";
        $("[name = area]:checkbox", window.parent.document).each(function () {
            if ($(this).attr("checked")) {
                str += $(this).val() + ","
            }
        })
        str.split(",");
        $("#BonusLibrary_bonus_sn").val(str);

        select_channel();
    });

    function is_shows() {
        $("#add_channel").toggle();
    }

    function select_channel() {
        var area_id = $("#BonusLibrary_bonus_id").val();
        if (area_id != 0) {
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('/bonusCode/ajax_channel');?>',
                'data': 'area_id=' + area_id,
                'type': 'get',
                'success': function (data) {
                    $("#BonusLibrary_owner").empty().append(data);
                },
                'cache': false
            });
        }
        return false;
    }


    function add_bonus_channel() {
        var area_id = $("#BonusChannel_area_id").val();
        var channel = $("#BonusChannel_channel").val();
        if (area_id != 0 && channel != '') {
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('/bonusCode/add_bonus_channel');?>',
                'data': 'area_id=' + area_id + '&channel=' + channel,
                'type': 'get',
                'success': function (data) {
                    if (data == 1) {
                        alert("添加成功");
                        var location = "<?php echo Yii::app()->createUrl('/bonusCode/owner');?>" + '&area_id=' + area_id + '&channel=' + channel;
                        window.location.replace(location)
                    } else {
                        alert("添加失败");
                    }
                },
                'cache': false
            });
        } else {
            alert("信息完善够才能添加");
        }
        return false;

    }

</script>
