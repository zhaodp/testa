<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-11-6
 * Time: 下午1:58
 * auther mengtianxue
 */
?>
    <h1>实体劵管理</h1>

<?php echo CHtml::link('实物卡使用统计', array('bonusLibrary/bonus_channel'), array('class' => 'btn')); ?>&nbsp;
<?php echo CHtml::link('实物卡渠道分配', array('bonusLibrary/assign'), array('class' => 'btn')); ?>

    <div class="search-form">
        <div class="well span12">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'action' => Yii::app()->createUrl($this->route),
                'method' => 'get',
            )); ?>
            <div class="row span12">
                <div class="span3">
                    <?php echo $form->labelEx($model, 'area_id'); ?>
                    <?php
                    $city = array('请选择') + $area;
                    echo $form->dropDownList($model, 'area_id', $city, array('style' => 'width:120px')); ?>
                </div>

                <div class="span3">
                    <?php echo $form->labelEx($model, '渠道'); ?>
                    <?php echo $form->textField($model, 'channel', array('style' => 'width:120px')); ?>
                </div>

                <div class="row span3">
                    <?php echo $form->labelEx($model, '&nbsp;'); ?>
                    <?php echo CHtml::submitButton('Search'); ?>
                </div>
            </div>

            <?php $this->endWidget(); ?>

        </div>
        <!-- search-form -->
    </div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bonus-type-grid',
    'dataProvider' => $model->search(20),
    'itemsCssClass' => 'table table-striped',
    //'filter'=>$model,
    'columns' => array(
        array(
            'name' => 'id',
            'headerHtmlOptions' => array(
                'width' => '10px',
                'nowrap' => 'nowrap'
            ),
        ),
        array(
            'name' => 'area_id',
            'headerHtmlOptions' => array(
                'width' => '30px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Dict::item("city", $data->area_id)'
        ),

        array(
            'name' => 'channel',
            'headerHtmlOptions' => array(
                'width' => '30px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->channel'
        ),

        array(
            'name' => '发放张数',
            'headerHtmlOptions' => array(
                'width' => '30px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'BonusLibrary::model()->getBonusByOwner($data->id, 0, "count")'
        ),

        array(
            'name' => '使用张数',
            'headerHtmlOptions' => array(
                'width' => '30px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'BonusLibrary::model()->getBonusByOwner($data->id, 147, "usedCount")'
        ),
        array(
            'name' => '操作',
            'headerHtmlOptions' => array(
                'width' => '30px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'CHtml::link("查看明细",Yii::app()->createUrl("bonusLibrary/assign",array("BonusLibrary[city_id]"=>$data->area_id, "BonusLibrary[owner]"=>$data->id)))'
        ),


    ),
));

?>