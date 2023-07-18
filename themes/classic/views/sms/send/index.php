<?php
  $s_time=isset($_GET['start_time'])?trim($_GET['start_time']):'';
  $e_time=isset($_GET['end_time'])?trim($_GET['end_time']):''
?>
<!--<h1>短信发送队列</h1>-->
<?php $this->renderPartial('/commentSms/com_nav'); ?>
<div class="search-form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'sms-send-search',
        'action'=>Yii::app()->createUrl('sms/send'),
        'method'=>'get',
    )); ?>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('接收者','receiver');?>
            <?php echo $form->textField($model,'receiver',array('placeholder'=>'接收者')); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('司机工号','driver_id');?>
            <?php echo $form->textField($model,'driver_id',array('placeholder'=>'司机工号')); ?>
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
            <?php echo CHtml::label('发送状态','status');?>
            <?php
            $statusArr=array(''=>'请选择发送状态',0=>'新建',1=>'已发送',2=>'已收取',3=>'待发送');
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
            'name'=>'接收者',
            'headerHtmlOptions'=>array (
                'style'=>'width:70px',
                'nowrap'=>'nowrap'
            ),
            'value'=>array($this,'showPhoneNumber')
        ),
        array (
            'name'=>'信息',
            'headerHtmlOptions'=>array (
                'style'=>'width:200px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->message'
        ),
        array (
            'name'=>'司机工号',
            'headerHtmlOptions'=>array (
                'style'=>'width:70px',
                'nowrap'=>'nowrap'
            ),
            'value'=>array($this,'showDriverId')
        ),
        array (
            'name'=>'订单id',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'showOrderId')
        ),
        array (
            'name'=>'订单状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->order_status==0?"未报单":
                      ($data->order_status==1?"已报单":
                      ($data->order_status==2?"待审核":
                      ($data->order_status==3?"已消单":
                      ($data->order_status==4?"拒绝消单":"未报单"))))'
        ),
        array (
            'name'=>'subcode',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->subcode'
        ),
        array (
            'name'=>'短信类型',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->sms_type==0?"评价":"价格核实"'
        ),
        array (
            'name'=>'延迟时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->sche_time'
        ),
        array (
            'name'=>'状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->status==0?"新建":
                      ($data->status==1?"已发送":
                      ($data->status==2?"已收取":
                      ($data->status==3?"待发送":"新建")))'
        ),
        array (
            'name'=>'创建时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:160px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->created'
        ),
    )
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