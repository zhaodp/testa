<?php $this->pageTitle = Yii::app()->name . ' -待办事项管理';?>
<h1>待办事项管理</h1>
<div class="search-form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'schedule-search',
        'action'=>Yii::app()->createUrl('adminuser/schedule'),
        'method'=>'get',
    )); ?>
    <div class="row-fluid">
        <div class="span3">
            <?php
            echo CHtml::label('创建者','create_user');
            echo CHtml::TextField('create_user',$create_user,array('placeholder'=>'创建者'));
            ?>
        </div>
        <div class="span3">
            <?php
                echo CHtml::label('发送人','sender');
                echo CHtml::TextField('sender',$sender,array('placeholder'=>'发送人'));
            ?>
        </div>
        <div class="span3">
            <?php
            echo CHtml::label('接收人','to_user');
            echo CHtml::TextField('to_user',$to_user,array('placeholder'=>'接收人'));
            ?>
        </div>
        <div class="span3">
            <?php
            echo CHtml::label('主题','name');
            echo CHtml::TextField('name',$name,array('placeholder'=>'主题'));
            ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php
            echo CHtml::label('是否全天展示','is_all_day');
            echo CHtml::DropDownList('is_all_day',$is_all_day,AdminSchedule::$is_all_days);
            ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span10">
            <button class="btn btn-primary span2" type="submit" name="search">搜索</button>
        </div>
    </div>


    <?php $this->endWidget(); ?>
</div>
<!-- 搜索结束 -->

<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'template-grid',
    'ajaxUpdate' => false,
    'dataProvider'=>$model->search(),
    'itemsCssClass'=>'table table-striped',
    'columns'=>array (
        array (
            'name'=>'创建者',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["create_user"]'
        ),
        array (
            'name'=>'发送人',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["sender"]'
        ),
        array (
            'name'=>'接收人',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["to_user"]'
        ),
        array (
            'name'=>'主题',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["title"]'
        ),
        array (
            'name'=>'内容',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["msg"]'
        ),
        array (
            'name'=>'是否全天展示',
            'headerHtmlOptions'=>array (
                'style'=>'width:20px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["is_all_day"]'
        ),
    )
));

?>