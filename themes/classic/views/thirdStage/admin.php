<div class="search-form">
    <?php
    $this->renderPartial('_search', array(
        'model' => $model,
    ));
    ?>
</div>

<?php echo CHtml::link('创建合作商家', Yii::app()->createUrl('thirdStage/create'), array('class' => 'btn btn-success','target'=>'_blank')); ?>
<?php echo CHtml::link('合作商家订单数据', Yii::app()->createUrl('thirdStage/ViewOrderSummary'), array('class' => 'btn btn-success','target'=>'_blank')); ?>


<?php

$gridId = 'third-user-grid';
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => $gridId,
    'dataProvider' => $model->search('id DESC'),
    'itemsCssClass' => 'table table-striped',
    'enableSorting' => FALSE,
    'columns' => array(
       array(
           'name' => 'channel',
           'type' => 'raw',
           'value'=> array($this, 'infoView'),
       ),
        array(
            'name' => 'name',
            'type' => 'raw',
        ),
        array(
            'name' => 'created',
            'type' => 'raw',
        ),
        array(
            'name' => 'channel',
            'type' => 'raw',
        ),
        array(
            'header' => '操作',
            'htmlOptions' => array(
                'style' => 'width:85px;'
            ),
            'class' => 'CButtonColumn',
            'template' => '{update}',
            'buttons' => array(
                'update' => array(
                    'label' => '修改',
                    'imageUrl' => false,
                    'options'=>array('target' => '_blank'),
                ),
            )
        ),
        array(
            'name' => '账单',
            'type' => 'raw',
            'value' => array($this, 'billLink'),
        ),
    ),
));

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'商家登录详情',
        'autoOpen'=>false,
        'width'=>'900',
        'height'=>'500',
        'modal'=>true,
        'buttons'=>array(
            '关闭'=>'js:function(){$("#mydialog").dialog("close");} '
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="cru-frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
