
<div class="row-fluid">

    <input class="btn btn-success" id="send_msg_btn" onclick="toShield()" type="button" value="批量屏蔽">
    <?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'ranking-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table table-striped',
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'ajaxUpdate' => false,
        'columns' => array(
            array(
                'class' => 'CCheckBoxColumn',
                'selectableRows' => 2,
                'value' => '$data["driver_user"]',
            ),
            array(
                'name' => '司机姓名',
                'headerHtmlOptions' => array(
                    'width' => '40px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '$data["driver"]."<br/>".$data["driver_user"]'),

            array(
                'name' => '总订单',
                'headerHtmlOptions' => array(
                    'width' => '60px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '"订单：".$data["order_count"]."<br/>".
			                "销单：".$data["cancel_count"]."<br/>".
			                "销单率：".sprintf("%.2f%%" , $data["cancel_rate"]*100)'),

            array(
                'name' => 'APP订单',
                'headerHtmlOptions' => array(
                    'width' => '60px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '"订单：".$data["app_count"]."<br/>".
			                "销单：".$data["cancel_app_count"]."<br/>".
			                "销单率：".sprintf("%.2f%%" , $data["cancel_app_rate"]*100)'),

            array(
                'name' => '400订单',
                'headerHtmlOptions' => array(
                    'width' => '60px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '"订单：".$data["callcenter_count"]."<br/>".
			                "销单：".$data["cancel_callcenter_count"]."<br/>".
			                "销单率：".sprintf("%.2f%%" , $data["cancel_callcenter_rate"]*100)'),
            array(
                'name' => '司机端生效销单',
                'headerHtmlOptions' => array(
                    'width' => '60px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '"销单率：".number_format(DriverInspireData::model()->getInspireDataByDriverId($data["driver_user"])["cancel_rate"],3,".","")."<br/>
                            销单排名：".number_format(DriverInspireData::model()->getCancelRateByDriverId($data["driver_user"])["ranking"],3,".","")'),
            array(
                'name' => '当前状态',
                'headerHtmlOptions' => array(
                    'width' => '20px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '$data["driver_user"]!="BJ00000"?Driver::model()->getDriverStatus($data["driver_user"]):""'),
			array(
                'name' => '可疑恶意消单',
                'headerHtmlOptions' => array(
                    'width' => '30px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '"可疑计数：".$data["alert_num"]."<br/>".
			                "可疑比率：".sprintf("%.2f%%" , $data["alert_rate"]*100)'),

            array(
                'name' => '处理次数',
                'headerHtmlOptions' => array(
                    'width' => '20px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => 'DriverProcessLog::model()->getProcessNum($data["driver_user"])'),
            array(
                'name' => '操作',
                'headerHtmlOptions' => array(
                    'width' => '60px',
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => 'CHtml::link("明细", array("order/orderAccount", "driver_id"=>$data["driver_user"],"start_time"=>date("Y-m-d",strtotime($data["start_time"])),"end_time"=>date("Y-m-d",strtotime($data["end_time"])),"is_pro"=>'.$condition['processed'].'),array("class"=>"btn","target" => "_blank"))."&nbsp;".
                            CHtml::link("可疑销单明细", array("ReportOrder/cancel", "driver_id"=>$data["driver_user"],"start_time"=>date("Y-m-d",strtotime($data["start_time"])),"end_time"=>date("Y-m-d",strtotime($data["end_time"])),"is_pro"=>'.$condition['processed'].'),array("class"=>"btn","target" => "_blank"))."&nbsp;".
			                CHtml::link("处理", "",array("class"=>"btn btn-success","style" =>"display:inline-block;cursor:pointer;","mewidth"=>"400","data-target" => "","data-toggle"=>"modal","url"=>Yii::app()->createUrl("driver/process",array("driver_id"=>$data["driver_user"],"stime"=>date("Y-m-d",strtotime($data["start_time"])),"etime"=>date("Y-m-d",strtotime($data["end_time"]))))))."&nbsp;".
			                CHtml::link("跟进记录", "",array("class"=>"btn","style" =>"display:inline-block;cursor:pointer;","mewidth"=>"800","data-target" => "","data-toggle"=>"modal","url"=>Yii::app()->createUrl("driver/prolist",array("driver_id"=>$data["driver_user"]))))'),
        ),
    ));

    Yii::app()->clientScript->registerScript('search', "
        $('.search-button').click(function(){
            $('.search-form').toggle();
            return false;
        });
        $('.search-form form').submit(function(){
            $.fn.yiiGridView.update('ranking-grid', {
                data: $(this).serialize()
            });

            var start_time = $('#report_start_time').val();
            var end_time = $('#report_end_time').val();
            var city_id = $('#report_city_id').val();
            if(start_time!='' && end_time!=''){
                var data = 'start_time='+start_time+'&end_time='+end_time+'&city_id='+ city_id;
                $.ajax({
                    type: 'get',
                    url: '" . Yii::app()->createUrl('/report/cancelstatajax') . "',
                    data: data,
                    dataType : 'html',
                    success: function(html){
                        $('#cancel').html(html);
                }});
            }
            return false;
        });


");
    ?>
</div>
