<h1>短信模板管理</h1>
<div class="search-form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'sms-template-search',
        'action'=>Yii::app()->createUrl('sms/template'),
        'method'=>'get',
    )); ?>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('短信模板名称','name');?>
            <?php echo CHtml::textField('name',$name,array('class'=>'info')); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('短信模板类型','type');?>
            <?php
            $typesList=array();
            $typesList[0]='请选择短信模板类型';
            $typesList=array_merge($typesList,SmsTemplate::$types);
            echo CHtml::dropDownList('type',$type, $typesList,array('class'=>'info'));
            ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('短信接受者','receive');?>
            <?php
            $receiveList=array();
            $receiveList[0]='请选择短信接受者类型';
            $receiveList=array_merge($receiveList,SmsTemplate::$recerves);
            echo CHtml::dropDownList('receive',$receive, $receiveList,array('class'=>'info'));
            ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span10">
            <button class="btn btn-primary span2" type="submit" name="search">搜索</button>
            　
            <?php echo  CHtml::link('新建短信模板',Yii::app()->createUrl('sms/templateadd'),  array('target'=>'_blank','class'=>'btn')); ?>
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
    'dataProvider'=>$dataProvider,
    'itemsCssClass'=>'table table-striped',
    'columns'=>array (
        array (
            'name'=>'模板名称',
            'headerHtmlOptions'=>array (
                'style'=>'width:120px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["name"]'
        ),
        array (
            'name'=>'Subject',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["subject"]'
        ),
        array (
            'name'=>'接收者',
            'headerHtmlOptions'=>array (
                'style'=>'width:50px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'SmsTemplate::$recerves[$data["receive"]]'
        ),
        array (
            'name'=>'通道',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'SmsTemplate::$channels[$data["channel"]]'
        ),
        array (
            'name'=>'短信类型',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'SmsTemplate::$types[$data["type"]]'
        ),
        array (
            'name'=>'短信内容',
            'headerHtmlOptions'=>array (
                'style'=>'width:280px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["content"]'
        ),
        array (
            'name'=>'创建人',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["created"]'
        ),
        array (
            'name'=>'创建和更新',
            'headerHtmlOptions'=>array (
                'style'=>'width:140px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["time"]'
        ),
        array (
            'name'=>'操作',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["url"]'
        ),
    )
));

?>
<script type="text/javascript">
    $(function(){
        $('.url_del_template').click(function(){
            return confirm('你真的要删除此数据吗？')?true:false;
        });
    });
</script>