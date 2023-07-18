<?php $this->pageTitle = Yii::app()->name . ' - 返程车司机管理';?>
<h1>返程车司机管理</h1>
<div class="search-form">
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'driver-black-search',
        'action'=>Yii::app()->createUrl('driver/blackcar'),
        'method'=>'get',
    )); ?>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('司机姓名','name');?>
            <?php echo CHtml::textField('name',$name,array('class'=>'input-large','placeholder'=>'司机姓名'));?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('司机工号','user');?>
            <?php echo CHtml::textField('user',$user,array('class'=>'input-large','placeholder'=>'司机工号'));?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('城市','city_id');?>
            <?php echo CHtml::dropDownList('city_id',$city_id, Dict::items('city'),array('class'=>'info')); ?>
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
    'id'=>'black-grid',
    'ajaxUpdate' => false,
    'dataProvider'=>$data,
    'itemsCssClass'=>'table table-striped',
    'columns'=>array (
        array (
            'name'=>'司机工号',
            'type'=>'raw',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'CHtml::link($data["user"], array("driver/archives", "id"=>$data["user"]),array("target"=>"_blank","title"=>"查看司机信息"))'
        ),
        array (
            'name'=>'司机姓名',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["name"]'
        ),
        array (
            'name'=>'城市',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'Dict::item("city",$data["city_id"])'
        ),
        array (
            'name'=>'价格信息',
            'headerHtmlOptions'=>array (
                'style'=>'width:120px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["url"]'
        ),
        array (
            'name'=>'总被叫(不去重)',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["allCount_Num"]'
        ),
        array (
            'name'=>'总被叫(去重)',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["AllorderNum"]'
        ),
        array (
            'name'=>'昨日被叫(去重)',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["YesterdayNum"]'
        ),
        array (
            'name'=>'前日被叫(去重)',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["before_yesterday_Num"]'
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