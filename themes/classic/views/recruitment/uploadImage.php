<style type="text/css">
    div#wrap {
        width:auto;
        margin:0 auto;
    }
    td {
        line-height: 40px;
    }
</style>


<div id="wrap">
    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'driver-form',
        'enableAjaxValidation'=>false,
        'htmlOptions'=>array('enctype'=>'multipart/form-data'),
//	'action'=> Yii::app()->createUrl('driver/create')
    ));
    ?>
    <table>
        <tr>
            <td style="margin:0 auto;">
                姓名：
                <?php echo CHtml::encode($model->name); ?>
            </td>
            <td>
                工号：
                <?php echo CHtml::encode($model->user); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                身份证号：
                <?php echo CHtml::encode($model->id_card); ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                头像：
                <?php echo CHtml::activeFileField($model,'picture'); ?>
                <?php //echo CHtml::image($model->attributes['picture'], $model->attributes['name'], array("width"=>120, "height"=>144)); ?>
                <?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '保存',array('class'=>'btn btn-success')); ?>
            </td>
        </tr>
    </table>

    <?php $this->endWidget(); ?>
    <table class="table-striped">
        <tr>
            <td width="150">头像</td>
            <td width="150">资料扫描件</td>
            <td width="150">担保信息</td>
        </tr>
        <tr>
            <td><?php echo $model->attributes['picture'] ? CHtml::image($model->attributes['picture'], $model->attributes['name'], array("width"=>120, "height"=>144)) : '';?></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>