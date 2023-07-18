<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-10-24
 * Time: 下午1:30
 */
?>

<h1>查看优惠劵规则列表</h1>
<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'view_bonus_dialog',
    'options' => array(
        'title' => '优惠劵规则',
        'autoOpen' => false,
        'width' => '580',
        'height' => '540',
        'modal' => true,
        'buttons' => array(
            '关闭' => 'js:function(){$("#view_bonus_dialog").dialog("close");}'))));
echo '<div id="view_bonus_dialog"></div>';
echo '<iframe id="view_bonus_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<?php echo CHtml::link('批量绑定优惠劵规则', "javascript:void(0);", array('class' => 'btn btn-success', "onClick" => "bonusCreatedInit()")); ?>
<?php
/* @var $this BonusRulesController */
/* @var $model BonusRules */
/* @var $form CActiveForm */
?>
<div class="row-fluid">
    <div class="wide well">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        )); ?>

        <div class="row-fluid">
            <div class="span3">
                <?php echo $form->label($model, 'bonus_sn'); ?>
                <?php echo $form->textField($model, 'bonus_sn', array('size' => 20, 'maxlength' => 20)); ?>
            </div>
            <div class="span3">
                <?php echo $form->label($model, 'balance'); ?>
                <?php echo $form->textField($model, 'balance'); ?>
            </div>
            <div class="span3">
                <?php echo $form->label($model, 'merchants'); ?>
                <?php echo $form->textField($model, 'merchants', array('size' => 30, 'maxlength' => 30)); ?>
            </div>
            <div class="span3">
                <?php echo $form->label($model, 'type'); ?>
                <?php echo $form->dropDownList($model, 'type', array('' => '全部', '0' => '未审核', '1' => '已审核', '2' => '已绑定')) ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span3 buttons">
                <?php echo CHtml::submitButton('搜 索', array('class' => 'btn')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>

    </div>
    <!-- search-form -->
</div>
<?php
$gridId = 'bonus-code-grid';
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => $gridId,
    'dataProvider' => $model->search('id DESC'),
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
        'id',
        'bonus_sn',
        'balance',
        'number',
        'phone_num',
        'merchants',
        'create_by',
        'created',
        array(
            'name' => '优惠劵状态',
            'type' => 'raw',
            'value' => '($data->type == 0) ? "待审核" : ( ($data->type == 1) ? "已审核" : "已绑定")',
        ),
        array(
            'name' => '查看详情',
            'type' => 'raw',
            'value' => 'CHtml::link("查看详情", "javascript:void(0);", array("id" => "bonus_rules_$data->id","onClick" => "bonusDialogdivInit($data->id)"))',
        ),
        array(
            'name' => '生成优惠劵',
            'type' => 'raw',
            'value' => '($data->type == 0) ? (CHtml::link("审核", "javascript:void(0);",array("onClick" => "audit(this,\'$data->id\',\'1\')"))) : ( ($data->type == 1) ? (CHtml::link("绑定优惠劵", "javascript:void(0);",array("onClick" => "audit(this,\'$data->id\',\'2\')"))) : "已经绑定")',
        ),
    ),
));

?>

<script>
    function bonusDialogdivInit(id) {
        var src = "<?php echo Yii::app()->createUrl('/bonusCode/bonus_rules_view');?>"+"&id=" + id;
        $("#view_bonus_frame").attr("src", src);
        $("#view_bonus_dialog").dialog("open");
        return false;
    }

    function bonusCreatedInit() {
        var src = "<?php echo Yii::app()->createUrl('/bonusCode/bonus_rules_create');?>";
        $("#view_bonus_frame").attr("src", src);
        $("#view_bonus_dialog").dialog("open");
        return false;
    }

    var TimeFn = null;
    function audit(arg,id, type){
        clearTimeout(TimeFn);
        TimeFn = setTimeout(function () {
            var s = '';
            if(type == 1){
                s = "确定审核通过";
            }else{
                s = "确定为用户绑定优惠劵";
            }
            if(confirm(s)){
                if(type == 2)
                    $(arg).remove();
                var src = "<?php echo Yii::app()->createUrl('/bonusCode/bonus_generate');?>" + "&id=" + id + "&type=" + type;
                window.location.href = src;
            }else{
                return false;
            }
        },300);
    }

</script>