<?php

//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'complain-grid-driver',
    'ajaxUpdate' => false,
    'dataProvider'=>$data,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'columns'=>array (
        array (
            'name'=>'投诉ID',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["id"]'
        ),
        array (
            'name'=>'司机',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'driverInfo')
        ),
        array (
            'name'=>'已被投诉',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'getComplainNumber')
        ),
        array (
            'name'=>'投诉订单',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'orderIdAndNumber')
        ),
        array (
            'name'=>'投诉类型',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["name"]'
        ),
        array (
            'name'=>'联系电话',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'Common::parseDriverPhone($data["driver_phone"])'
        ),
        array (
            'name'=>'处理状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'processStatus')
        ),
        array (
            'name'=>'操作',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["opt"]'
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