<?php
$this->pageTitle = Yii::app()->name . ' - 客服回访列表';
?>
<h1>客服回访列表</h1>
<!--<div class="search-form">-->
<!--    --><?php
//    $form=$this->beginWidget('CActiveForm', array(
//        'id'=>'wait-tube-search',
//        'method'=>'get',
//    )); ?>
<!--    <div class="row-fluid">-->
<!--        <div class="span3">-->
<!--            --><?php //echo CHtml::label('投诉类型','reason');?>
<!--            --><?php // echo CHtml::dropDownList('complain_maintype',
//                $parent_id,
//                $typelist,
//                array(
//                    'ajax' => array(
//                        'type'=>'POST', //request type
//                        'url'=>Yii::app()->createUrl('complain/getsubtype'),
//                        'update'=>'#sub_type', //selector to update
//                        'data'=>array('complain_maintype'=>'js:$("#complain_maintype").val()')
//                    ))
//            );?>
<!--        </div>-->
<!--        <div class="span3">-->
<!--            <label for="reason">　</label>-->
<!--            --><?php //echo CHtml::dropDownList('sub_type','',$child); ?>
<!--        </div>-->
<!--        <div class="span3">-->
<!--            <label for="status">　</label>-->
<!--            <button class="btn btn-primary span8" type="submit" name="search">搜索</button>-->
<!--        </div>-->
<!--    </div>-->
<!--    --><?php //$this->endWidget(); ?>
<!--</div>-->

<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'complain-grid-wait',
    'ajaxUpdate' => false,
    'dataProvider'=>$dataProvider,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'columns'=>array (
        array (
            'name'=>'ID',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["id"]'
        ),
        array (
            'name'=>'司机',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'CHtml::link($data["driver_id"], array("driver/archives", "id"=>$data["driver_id"]),array("target"=>"_blank","title"=>"查看司机信息"))'
        ),
        array (
            'name'=>'投诉订单',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'orderIdAndNumber')
        ),

        array (
            'name'=>'客户电话',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'Common::parseCustomerPhone($data["recoup_customer"])'
        ),
        array (
            'name'=>'处理结果',
            'headerHtmlOptions'=>array (
                'style'=>'width:20px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'getProcessStatus')
        ),
        array (
            'name'=>'处理意见',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["mark"]'
        ),
        array (
            'name'=>'补偿时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["update_time"]'
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