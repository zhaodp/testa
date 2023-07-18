<?php
$this->pageTitle = '队列名称管理';
?>
<h1><?php echo $this->pageTitle;?></h1>

<div class="search-form">
<?php
    $base_qname = isset($_REQUEST['base_qname']) ? $_REQUEST['base_qname'] : '';
    $hash_qname = isset($_REQUEST['hash_qname']) ? strip_tags($_REQUEST['hash_qname']) : '';
    $owner = isset($_REQUEST['owner']) ? strip_tags($_REQUEST['owner']) : '';
    $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    ));
?>
<div class="row-fluid">
    <div class="span3">
        <label style="display:inline" for="base_qname">队列名</label>
        <?php echo CHtml::textField('base_qname',$base_qname) ?>
    </div>
    <div class="span3">
        <label style="display:inline" for="hash_qname">开发标识</label>
        <?php echo CHtml::textField('hash_qname',$hash_qname) ?>
    </div>
    <div class="span3">
        <label style="display:inline" for="owner">负责人</label>
        <?php echo CHtml::textField('owner',$owner) ?>
    </div>
    <div class="span2">
        <?php echo CHtml::submitButton('查询');?>
    </div>
</div>
    <?php $this->endWidget(); ?>
</div>

</div><!-- search-form -->

<div class="btn-group">
    <?php echo CHtml::link('添加队列', array("create"),
        array('class'=>"search-button btn-primary btn",'style'=>'margin-right:5px'));?>
</div>
<br>

<?php
$this->widget ('zii.widgets.grid.CGridView', array (
    'id' => 'scron-grid',
    'dataProvider' => $dataProvider,
    'cssFile'=>SP_URL_CSS . 'table.css',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-condensed',
    'htmlOptions'=>array('class'=>'row span11'),
    'columns' => array (
        'id' => array(
            'header'=>'序号',
            'name'=>'id',
            'htmlOptions'=>array(
                'width'=>'150'
            )
        ),
       'base_qname'=>array(
            'header'=>'队列名',
            'name'=>'base_qname',
            'htmlOptions'=>array(
               'width'=>'300'
            )
        ),
       'hash_qname'=>array(
            'header'=>'开发标识(用于代码和配置JOB)',
            'name'=>'hash_qname',
            'htmlOptions'=>array(
               'width'=>'350'
            )
        ),
       'level'=>array(
            'header'=>'重要等级',
            'name'=>'level',
            'htmlOptions'=>array(
               'width'=>'100'
            )
        ),
       'max'=>array(
            'header'=>'报警阈值',
            'name'=>'max',
            'htmlOptions'=>array(
               'width'=>'200'
            )
        ),
        'owner'=>array(
            'header'=>'责任人',
            'name'=>'owner',
            'htmlOptions'=>array(
                'width'=>'150'
            )
        ),
        array(
            'header'=>'操作',
            'class'=>'CButtonColumn',
            'template'=>'{update}',
            'updateButtonOptions'=>array('title'=>'修改'),
        ),
    )
));
?>
