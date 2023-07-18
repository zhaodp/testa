<?php
$s_time=isset($_GET['start_time'])?trim($_GET['start_time']):'';
$e_time=isset($_GET['end_time'])?trim($_GET['end_time']):'';
?>

<!--<h1>回评短信队列</h1>-->
<?php $this->renderPartial('/commentSms/com_nav'); ?>
<div class="search-form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'sms-mo-search',
        'action'=>Yii::app()->createUrl('sms/mo'),
        'method'=>'get',
    )); ?>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('用户手机号码','sender');?>
            <?php echo $form->textField($model,'sender',array('placeholder'=>'用户手机号码')); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('上行内容','content');?>
            <?php echo $form->textField($model,'content',array('placeholder'=>'上行内容')); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('开始时间','create_time');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'start_time',
                'value'=>$s_time,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"开始",
                ),
            ));?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('结束时间','create_time');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'end_time',
                'value'=>$e_time,
                'mode'=>'date',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"结束",
                ),
            ));?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('状态','status');?>
            <?php
            $statusArr=array(''=>'请选择状态',0=>'未处理',1=>'已处理');
            echo CHtml::activedropDownList($model,'status',$statusArr,array('class'=>' info','placeholder'=>'状态类型'));
            ?>
        </div>
        <div class="span3">
            <label for="status">　</label>
            <button class="btn btn-primary span8" type="submit" name="search">搜索</button>
        </div>
    </div>

    <?php $this->endWidget(); ?>
</div>
<!-- 搜索结束 -->

<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'send-grid',
    'ajaxUpdate' => false,
    'dataProvider'=>$model->search(),
    'itemsCssClass'=>'table table-striped',
    'columns'=>array (
        array (
            'name'=>'通道',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->recvtel'
        ),
        array (
            'name'=>'用户手机号码',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'Common::parseCustomerPhone($data->sender)',
        ),
        array (
            'name'=>'短信内容',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->content'
        ),
        array (
            'name'=>'接收时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->recdate'
        ),
        array (
            'name'=>'渠道',
            'headerHtmlOptions'=>array (
                'style'=>'width:30px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->channel '
        ),
        array (
            'name'=>'subcode',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->subcode'
        ),
        array (
            'name'=>'状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:50px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->status==0?"未处理":"已处理"'
        ),
        array (
            'name'=>'创建时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:120px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->created'
        ),
        array (
            'name'=>'更新时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:120px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->update_time'
        ),
    )
));

?>