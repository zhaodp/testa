<?php

//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'complain-finance-grid',
    'dataProvider'=>$data,
    'ajaxUpdate' => false,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'columns'=>array (
        array (
            'name'=>'客户',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["customer"]'
        ),
        array (
            'name'=>'司机',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["driver"]'
        ),

        array (
            'name'=>'信息费余额',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["costs"]'
        ),
        array (
            'name'=>'处理状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["status"]'
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