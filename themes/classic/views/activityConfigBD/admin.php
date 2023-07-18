<?php
$this->pageTitle = 'BD活动配置后台 - ' . $this->pageTitle;
?>
    <h1>BD活动配置后台</h1>
<?php
    Yii::app()->clientScript->registerScript('search', "
    $('.search-button').click(function(){
        $('.search-form').toggle();
        return false;
    });
    $('.search-form form').submit(function(){
        $.fn.yiiGridView.update('activity-grid', {
            data: $(this).serialize()
        });
        return false;
    });
    ");
?>
    <div class="search-form">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        )); ?>
        <div class="row-fluid">
            <div class="span3">
                <?php echo $form->label($model, 'name'); ?>
                <?php echo $form->textField($model, 'name'); ?>
            </div>
            <div class="span3">
                <?php echo $form->label($model, 'english_name'); ?>
                <?php echo $form->textField($model, 'english_name'); ?>
            </div>
        </div>
        <div class='row-fluid'>
            <div class="span12">
                <?php
                echo CHtml::submitButton('搜索', array('class' => 'btn btn-success'));
               // echo CHtml::link('创建活动', array('activitySeller/create'), array('target' => '_blank', 'class' => 'btn'));
                echo CHtml::link('创建活动', array('activitySeller/create'), array('class' => 'btn'));
                ?>

            </div>
        </div>
    </div>
<?php $this->endWidget(); ?>

<?php
$dataProvider = $model->search();
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'activity-grid',
    'itemsCssClass' => 'table',
    'enableSorting' => false,
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'dataProvider' => $dataProvider,
    'columns' => array(
        array(
            'name' => 'id',
            'header' => '编号',
            'type' => 'raw',
        ),
        array(
            'name' => 'name',
            'header' => '活动名字',
            'type' => 'raw',
        ),
        array(
            'name' => '英文名字',
            'type'=>'raw',
            'value'=>array($this,'getUrl')
        ),
        array(
            'name' => 'create_user',
            'header' => '创建者',
            'type' => 'raw',
        ),
        array(
            'name' => 'bonus_num',
            'header' => '城市',
            'type' => 'raw',
        ),
        array(
            'name' => 'bonus_num',
            'header' => '商家名',
            'type' => 'raw',
        ),
        array(
            'name' => 'begin_time',
            'header' => '活动时间',
            'type' => 'raw',
        ),
        array(
            'name' => 'end_time',
            'header' => '当前状态',
            'type' => 'raw',
        ),
        array(
            'header' => '操作',
            'type' => 'raw',
            'htmlOptions' => array('style' => 'width:30px;'),
            'value' => 'CHtml::link("修改", array("activityConfigBD/edit", "id"=>$data->id))',
        ),
    ),
));
?>