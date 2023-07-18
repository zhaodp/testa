<?php

?>
<h3>工单列表</h3>
<div class="btn-group">
        <?php echo CHtml::link('工单处理', array('crm/ticketList'),array('class'=>'btn'));?>
        <?php echo CHtml::link('工单补扣款处理', array('crm/feeList'),array('class'=>'btn-primary btn'));?>
</div>
<div class="search-form" style="display:block">
    <?php $this->renderPartial('fee_search',array(
        'param'=>$param,
    )); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'ticket-fee-grid',
    'dataProvider'=>$dataProvider,
      	'ajaxUpdate' => false,
    'itemsCssClass'=>'table table-striped',
	'pagerCssClass'=>'pagination text-center',
    	'pager'=>Yii::app()->params['formatGridPage'],
    'columns'=>array(
        array (
            'name'=>'工单ID',
            'headerHtmlOptions'=>array (
                'width'=>'20px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["id"]'
        ),
        array(
            'name' => '城市',
            'value'=>'Dict::item("city",$data["city_id"])',
        ),
       array(
           'name'=> '类型',
           'value'=>'Dict::item("ticket_category", $data["type"])',
       ),
	array(
	   'name'=>'分类',
	   'value'=>'$data["class"]==0?"":SupportTicketClass::model()->findByPk($data["class"])->name',
	
	),
        array (
            'name'=>'内容',
            'headerHtmlOptions'=>array (
                'width'=>'300px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["content"]'
        ),
	array(
            'header'=>'司机工号',
            'type'=>'raw',
            'value'=>'$data["driver_id"]',
        ),
	array(
            'header'=>'补偿金额',
            'type'=>'raw',
            'value'=>'$data["total"]',
        ),
	array(
            'header'=>'状态',
            'type'=>'raw',
            'value'=>'$data["status"]==1?"未处理":"已处理"',
        ),
	 array(
            'header'=>'创建人',
            'type'=>'raw',
            'value'=>'$data["create_user"]',
        ),
	 array(
            'header'=>'创建时间',
            'type'=>'raw',
            'value'=>'$data["create_time"]',
        ),
	 array(
            'header'=>'处理人',
            'type'=>'raw',
            'value'=>'$data["deal_user"]',
        ),
	 array(
            'header'=>'处理时间',
            'type'=>'raw',
            'value'=>'$data["deal_time"]',
        ),
	 array(
            'header'=>'操作',
            'type'=>'raw',
	    'value' => array($this, 'getOperateButton'),
        ),
    ),
)); ?>

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
	    alert(123);
        });
    });

</script>

