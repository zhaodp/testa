<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 13-6-13
 * Time: 上午11:16
 * To change this template use File | Settings | File Templates.
 */

?>
<h1>投诉分类</h1>
<?php
$action=Yii::app()->createUrl($this->route);
$form=$this->beginWidget('CActiveForm', array(
    'action'=>$action,
    'method'=>'POST',
));
?>
<div class="input-append">


</div>
<div class="control-group">
    <div class="controls">
        <div class="input-prepend">
            <input class="span8" id="name" name="name" type="text">
            <button class="btn" type="submit" name="create">创建一级分类</button>
        </div>
    </div>
</div>


<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'complain-type-grid',
    'dataProvider'=>$model,
    'columns'=>array(
        array(
            'name'=>'分类名称',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'100px',
                'nowrap'=>'nowrap'
            ),
            'value' => '($data["parent_id"] >0)? "&nbsp;&nbsp;&nbsp;&nbsp;".$data["name"] :$data["name"]'
        ),
        array(
            'name'=>'大类',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'40px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'($data["parent_id"] > 0) ? $data["category"]:""'
        ),
        array(
            'name'=>'状态',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'40px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'($data["status"]==1)?"正常":( ($data["status"]==2) ? "屏蔽" : "默认" )'
        ),
        array(
            'name'=>'权重系数',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'40px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'($data["parent_id"] >0)? $data["weight"]:""'
        ),
        array(
            'name'=>'司机代驾扣分',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'40px',
                'nowrap'=>'nowrap'
            ),
            'value' =>'($data["parent_id"] > 0) ? $data["score"]:""'
        ),
        array(
            'name'=>'投诉响应时间',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'value' => '($data["parent_id"] > 0) ? $data["should_response_hour"]:""'
        ),
        array(
            'name'=>'投诉跟进时间',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'value' => '($data["parent_id"] > 0) ? $data["should_follow_hour"]:""'
        ),
        array(
            'name'=>'投诉结案时间',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'value' => '($data["parent_id"] > 0) ? $data["should_closing_hour"]:""'
        ),
        array(
            'name'=>'创建时间',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["create_time"]'
        ),
        array(
            'name'=>'更新时间',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'value' => '$data["update_time"]'
        ),
        array(
            'name'=>'投诉任务组',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
            'value' => array($this,'getGroup')
        ),

        array(
            'class'=>'CButtonColumn',
            'header'=>'操作',
            'headerHtmlOptions'=>array (
                'width'=>'120px',
                'nowrap'=>'nowrap'
            ),
            'template'=>'{add} {up} {del}',
            'buttons'=>array(
                'add'=>array(
                    'label'=>'添加子分类',
                    'url'=>'Yii::app()->controller->createUrl("'.$this->route.'",array("id"=>$data["id"],"type"=>"add"))',
                    'options'=>array('style'=>'cursor:pointer;'),
                    'visible'=>'($data["parent_id"] ==0) ? true:false',
                ),
                'up' => array(
                    'label'=>'编辑',
                    'url'=>'Yii::app()->controller->createUrl("'.$this->route.'",array("id"=>$data["id"],"type"=>"up"))',
                    'options'=>array('style'=>'cursor:pointer;'),
                    'visible'=>'true',
                ),
                'del' => array(
                    'label'=>'屏蔽',
                    'url'=>'Yii::app()->controller->createUrl("'.$this->route.'",array("id"=>$data["id"],"type"=>"del"))',
                    'options'=>array('style'=>'cursor:pointer;'),
                    'visible'=>'true',
                ),

            ),
        ),

    ),
));
?>

<?php $this->endWidget(); ?>



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

</script>



