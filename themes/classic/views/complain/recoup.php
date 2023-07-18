<?php
$this->pageTitle = Yii::app()->name . ' - 补偿扣款流水';
?>
<h1>补偿扣款流水</h1>
<?php $this->renderPartial('/complain/_com_nav'); ?>
<?php
$form=$this->beginWidget('CActiveForm', array(
    'id'=>'complain-list-search',
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>

<div class="row-fluid">
    <div class="span3">
        <?php echo $form->label($model,'driver_id',array('class'=>'control-label')); ?>
        <?php echo CHtml::textField('driver_id',$model->driver_id,array('class'=>'input-large','placeholder'=>'司机工号'));?>
    </div>
    <div class="span3">
        <?php echo CHtml::label('处理类型','process_type');?>
        <?php echo CHtml::dropDownList('process_type',$model->process_type, array(
            '0'=>'默认',
            '1'=>'用户补偿',
            '2'=>'用户扣款',
            '3'=>'司机补偿',
            '4'=>'司机扣款',
            '5'=>'补偿用户 司机补偿',
            '6'=>'补偿用户 司机扣款',
            '7'=>'用户扣款 司机补偿',
            '8'=>'用户扣款 司机扣款',
        )); ?>
    </div>
    <div class="span3">
        <?php echo $form->label($model,'created',array('class'=>'control-label')); ?>
        <?php echo CHtml::textField('created',$model->created,array('class'=>'input-large','placeholder'=>'创建人'));?>
    </div>
    <div class="span3">
        <?php echo $form->label($model,'operator',array('class'=>'control-label')); ?>
        <?php echo CHtml::textField('operator',$model->operator,array('class'=>'input-large','placeholder'=>'操作人'));?>
    </div>
</div>
<div class="row-fluid">
    <div class="span3">
        <?php echo CHtml::label('开始时间','create_time');?>
        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array (
            'name'=>'start_time',
            'value'=>$start_time,
            'mode'=>'date',  //use "time","date" or "datetime" (default)
            'options'=>array (
                'dateFormat'=>'yy-mm-dd'
            ),
            'language'=>'zh',
            'htmlOptions'=>array(
                'placeholder'=>"开始时间",
            ),
        ));?>
    </div>
    <div class="span3">
        <?php echo CHtml::label('结束时间','create_time');?>
        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array (
            'name'=>'end_time',
            'value'=>$end_time,
            'mode'=>'date',  //use "time","date" or "datetime" (default)
            'options'=>array (
                'dateFormat'=>'yy-mm-dd'
            ),  // jquery plugin options
            'language'=>'zh',
            'htmlOptions'=>array(
                'placeholder'=>"结束时间",
            ),
        ));
        ?>
    </div>

    <div class="span3">
        <?php echo CHtml::label('处理状态','status');?>
        <?php echo CHtml::dropDownList('status',$model->status, array(
            '0'=>'未处理',
            '1'=>'已处理',
            '2'=>'已驳回',
        )); ?>
    </div>
    <div class="span3">
        <?php echo CHtml::label('补偿方式','recoup_type');?>
        <?php echo CHtml::dropDownList('recoup_type',$model->recoup_type, array(
            '1'=>'现金',
            // '2'=>'优惠券',
        )); ?>
    </div>
</div>
<div class="row-fluid">
    <div class="span3">
        <?php echo CHtml::label('预约电话','customer');?>
        <?php echo CHtml::textField('customer',$model->customer,array('class'=>'input-large','placeholder'=>'预约电话'));?>
    </div>
    <div class="span3">
        <?php echo CHtml::label('补扣用户','recoup_customer');?>
        <?php echo CHtml::textField('recoup_customer',$model->recoup_customer,array('class'=>'input-large','placeholder'=>'补扣用户'));?>
    </div>
    <div class="span3"></div>
    <div class="span3"></div>
</div>
<div class="row-fluid">
    <div class="span10">
        <button class="btn btn-primary span2" type="submit" name="search">搜索</button>&nbsp;&nbsp;
        <?php
        //获取搜索的参数
        $params=(isset($_GET['driver_id'])?'&driver_id='.$_GET['driver_id']:'').
                (isset($_GET['process_type'])?'&process_type='.$_GET['process_type']:'').
                (isset($_GET['created'])?'&created='.$_GET['created']:'').
                (isset($_GET['operator'])?'&operator='.$_GET['operator']:'').
                (isset($_GET['start_time'])?'&start_time='.$_GET['start_time']:'').
                (isset($_GET['end_time'])?'&end_time='.$_GET['end_time']:'').
                (isset($_GET['status'])?'&status='.$_GET['status']:'').
                (isset($_GET['recoup_type'])?'&recoup_type='.$_GET['recoup_type']:'').
                (isset($_GET['customer'])?'&customer='.$_GET['customer']:'').
                (isset($_GET['recoup_customer'])?'&recoup_customer='.$_GET['recoup_customer']:'');
        echo CHtml::link('导出补偿扣款搜索结果',Yii::app()->createUrl('/complain/export'.$params),array('class' => 'btn search-botton')).'&nbsp;';
        ?>
    </div>

</div>
<?php $this->endWidget(); ?>
<!--上面搜索-->


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'recoup-grid',
	'dataProvider'=>$vmodel,
	'ajaxUpdate' => false,
    'itemsCssClass' =>'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'columns'=>array(
        array(
            'name' => '流水ID',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => '$data->id',
        ),

        array (
            'name'=>'预约电话/司机',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->customer',
        ),


        array (
            'name'=>'补扣用户',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->recoup_customer',
        ),

        array (
            'name'=>'金额(用户)',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->amount_customer',
        ),
        array (
            'name'=>'补扣司机',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->recoup_driver',
        ),
        array (
            'name'=>'金额(司机)',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->amount_driver',
        ),


        array(
            'name' => '补偿方式',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' =>  '($data->recoup_type==CustomerComplainRecoup::RECOUP_TYPE1)? "现金":"优惠券"',
        ),
        array(
            'name' => '处理类型',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => array($this,'recoupProcessType'),
        ),
        array(
            'name' => '投诉处理状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => array($this,'recoupProcessStatus'),
        ),
        array(
            'name' => '操作状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => array($this,'getStatus'),
        ),
        array(
            'name' => '操作人',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => '$data->operator',
        ),
        array(
            'name' => '操作时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => '$data->create_time',
        ),
        array(
            'name' => '操作',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value' => array($this, 'recoupOpt'),
        ),
	),
)); 
?>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-body" id="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
<!-- Modal -->



<script type="text/javascript">
    $(function(){
        $("a[data-toggle=modal]").click(function(){
            var target = $(this).attr('data-target');
            var url = $(this).attr('url');
            var mewidth = $(this).attr('mewidth');
            if(mewidth==null) mewidth='850px';
            if(url!=null){
                $('#myModal').modal('toggle').css({'width':mewidth,'margin-left': function () {return -($(this).width() / 2);}});
                $('#myModal').modal('show');
                $('#modal-body').load(url);
            }
            return true;
        });
    });

</script>