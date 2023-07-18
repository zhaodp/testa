<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-9-2
 * Time: 下午5:23
 */
?>
    <div class="wide form">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        )); ?>
        <div class="row-fluid">

            <div class="span3">
                <?php echo $form->label($model, 'dictname'); ?>
                <?php echo $form->textField($model, 'dictname', array('size' => 20, 'maxlength' => 20)); ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, 'name'); ?>
                <?php echo $form->textField($model, 'name', array('size' => 20, 'maxlength' => 20)); ?>
            </div>


            <div class="span3">
                <?php echo $form->label($model, '&nbsp'); ?>
                <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>&nbsp;&nbsp;&nbsp;
            </div>
        </div>

        <?php $this->endWidget(); ?>

    </div><!-- search-form -->
    <?php
    $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
        'id'=>'mydialog',
        // additional javascript options for the dialog plugin
        'options'=>array(
            'title'=>'添加字典',
            'autoOpen'=>false,
            'width'=>'600',
            'height'=>'500',
            'modal'=>true,
            'buttons'=>array(
                '关闭'=>'js:function(){$("#mydialog").dialog("close");  $(".search-form form").submit();} '
            ),
        ),
    ));
    echo '<div id="dialogdiv"></div>';
    echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
    $this->endWidget('zii.widgets.jui.CJuiDialog');
    ?>
<?php echo CHtml::link('添加字典', 'javascript:;' ,array('class' => 'btn btn-primary', 'id'=>'dict-pop-id','onclick'=>'addDict(\''.Yii::app()->createUrl('adminuserNew/dictCreate').'\')')); ?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'admin-user-grid',
    'itemsCssClass' => 'table table-striped',
    'dataProvider' => $dataProvider,
    'columns' => array(
        'dictname',
        'name',
        'code',
        'postion',
    ),
)); ?>
<script type="text/javascript">
    function addDict(url){
        $("#cru-frame").attr("src", url);
        $("#mydialog").dialog("open");
        return false;
    }
</script>