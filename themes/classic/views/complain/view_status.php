<ul class="nav nav-pills">
    <li>
        <a href="#" id="newLog" style="text-decoration:underline" >处理日志(新)</a>
    </li>
    <li>
        <a href="#" id="oldLog" style="text-decoration:underline"  >处理日志(旧)</a>
    </li>
</ul>
<div id="newLogGrid">
    <div class="row-fluid">
    <?php
    $this->widget('zii.widgets.grid.CGridView', array (
        'id'=>'driver-punish-grid',
        'dataProvider'=>$data,
        'itemsCssClass'=>'table table-striped',
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'columns'=>array (
            array (
                'name'=>'序号',
                'headerHtmlOptions'=>array (
                    'style'=>'width:5px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["seq"]'
            ),
            array (
                'name'=>'处理点',
                'headerHtmlOptions'=>array (
                    'style'=>'width:20px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["node"]'
            ),
            array (
                'name'=>'处理详情',
                'headerHtmlOptions'=>array (
                    'style'=>'width:60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["pdetail"]'
            ),
            array (
                'name'=>'处理人',
                'headerHtmlOptions'=>array (
                    'style'=>'width:30px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["operator"]'
            ),
            array (
                'name'=>'处理时间',
                'headerHtmlOptions'=>array (
                    'style'=>'width:40px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["ptime"]'
            ),
        )
    ));
    ?>
    </div>
</div>
<div id="oldLogGrid">
    <div class="well span12">
    <?php
    $this->widget('zii.widgets.grid.CGridView', array (
        'id'=>'driver-punish-grid2',
        'dataProvider'=>$data2,
        'itemsCssClass'=>'table table-striped',
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'columns'=>array (
            array (
                'name'=>'处理意见',
                'headerHtmlOptions'=>array (
                    'style'=>'width:200px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["mark"]'
            ),
            array (
                'name'=>'处理结果',
                'headerHtmlOptions'=>array (
                    'style'=>'width:40px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["result"]'
            ),
            array (
                'name'=>'处理部门',
                'headerHtmlOptions'=>array (
                    'style'=>'width:40px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["process_type"]'
            ),
            array (
                'name'=>'处理人',
                'headerHtmlOptions'=>array (
                    'style'=>'width:40px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["operator"]'
            ),
            array (
                'name'=>'处理日期',
                'headerHtmlOptions'=>array (
                    'style'=>'width:40px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data["create_time"]'
            ),
        )
    ));
    ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("#newLogGrid").show();
        $("#oldLogGrid").hide();
        $("#newLog").css('background-color','#08c');
        $("#newLog").css('color','#fff');
        $("#newLog").click(function(){
            $("#newLog").css('background-color','#08c');
            $("#oldLog").css('background-color','inherit');
            $("#newLog").css('color','#fff');
            $("#oldLog").css('color','inherit');
            $("#newLogGrid").show();
            $("#oldLogGrid").hide();
        });
        $("#oldLog").click(function(){
            $("#oldLog").css('background-color','#08c');
            $("#newLog").css('background-color','inherit');
            $("#oldLog").css('color','#fff');
            $("#newLog").css('color','inherit');
            $("#newLogGrid").hide();
            $("#oldLogGrid").show();
        });
    });
</script>