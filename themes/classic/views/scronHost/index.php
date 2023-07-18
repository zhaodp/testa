<?php
$this->pageTitle = '主机管理';
?>
    <h1><?php echo $this->pageTitle;?></h1>

    <div class="btn-group">
        <?php echo CHtml::link('添加主机', array("create"),array('class'=>"search-button btn-primary btn",'style'=>'margin-right:5px'));?>
        <?php echo CHtml::link('任务管理', array("scron/index"),array('class'=>"search-button btn-primary btn",'style'=>'margin-right:5px'));?>

    </div>
    <br >
<?php
$this->widget ('zii.widgets.grid.CGridView', array (
    'id' => 'scronhost-grid',
    'dataProvider' => $dataProvider,
    'cssFile'=>SP_URL_CSS . 'table.css',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-condensed',
    'htmlOptions'=>array('class'=>'row span11'),
    'columns' => array (
        'id',
        'host_name',
        'host',
        'is_enable'=>array(
            'name'=>'is_enable',
            'value'=>'$data->is_enable==0 ? "禁用" :  "启用"',
        ),

        array (
            'header' => '<span>操作</span>',
            'class' => 'CButtonColumn',
            'buttons'=>array(
                'stop'=>array(
                    'label'=>'禁用',
                    'url'=>'Yii::app()->controller->createUrl("deal", array("id"=>$data->id,"type"=>"stop"))',
                    'options'=>array(),
                ),
                'start'=>array(
                    'label'=>'启用',
                    'url'=>'Yii::app()->controller->createUrl("deal", array("id"=>$data->id,"type"=>"start"))',
                    'options'=>array(), // HTML options for the button tag
                ),

            ),
            'htmlOptions' => array (
                'width' => '200'
            ),
            'template' => '{update} {delete}  {stop} {start}'
        ),
    )
));

$dataProvider->model->restDbConnection();
?>