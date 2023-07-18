<h1>司机投诉管理</h1>

<?php $this->renderPartial('/complain/_com_nav'); ?>

<?php
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#driver-complaint-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="wide form thumbnail">
    <div class="caption">
        <?php
        $t_driver_user = isset($_GET['driver_user']) ? $_GET['driver_user'] : '';
        $t_customer_phone = isset($_GET['customer_phone']) ? $_GET['customer_phone'] : '';
        $t_order_type = isset($_GET['order_type']) ? $_GET['order_type'] : '';
        $t_complaint_type = isset($_GET['complaint_type']) ? $_GET['complaint_type'] : '';
        $t_city_id = isset($_GET['city_id']) ? $_GET['city_id'] : 0;
        $is_opreate = isset($_GET['is_opreate']) ? $_GET['is_opreate'] : 0;
        $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        )); ?>
        <div class="row-fluid">

            <div class="span3">
                <label for="mobile">司机工号</label>
                <input type="text" id="driver_user" class="span10" name="driver_user"
                       value="<?php echo $t_driver_user; ?>"/>
            </div>
            <div class="span3">
                <label for="mobile">客户电话</label>
                <input type="text" id="customer_phone" class="span10" name="customer_phone"
                       value="<?php echo $t_customer_phone; ?>"/>
            </div>
            <div class="span3">
                <label for="mobile">订单类型</label>
                <?php
                $order_type = array('全部', '报单', '销单');
                echo CHtml::dropDownList('order_type', $t_order_type, $order_type,
                    array(
                        'ajax' => array(
                            'type' => 'POST', //发送类型
                            'url' => CController::createUrl('driverComplaint/getcomplainttype'), //要调用返回的php程序.
                            'update' => '#complaint_type', //选择这个菜单后下个菜单要变动
                            'data' => array('order_type' => "js:this.value")
                        )
                    )
                );
                ?>
            </div>

            <div class="span3">
                <label for="mobile">投诉类型</label>
                <?php
                $complaint_type = array('请先选择报单类型，再选类型');
                echo CHtml::dropDownList('complaint_type', $t_complaint_type, $complaint_type);
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span3">
                <label for="mobile">所在城市</label>
                <?php
                    $user_city_id = Yii::app()->user->city;
                    if ($user_city_id != 0) {
                        $city_list = array(
                            '城市' => array(
                                $user_city_id => Dict::item('city', $user_city_id)
                            )
                        );
                        $city_id = $user_city_id;
                    } else {
                        $city_id = $city_id;
                        $city_list = CityTools::cityPinYinSort();
                    }
                    $this->widget("application.widgets.common.DropDownCity", array(
                        'cityList' => $city_list,
                        'name' => 'city_id',
                        'value' => $city_id,
                        'type' => 'modal',
                        'htmlOptions' => array(
                            'style' => 'width: 134px; cursor: pointer;',
                            'readonly' => 'readonly',
                        )
                    ));
                ?>  
            </div>
            <div class="span3">
                <label for="mobile">是否处理</label>
                <?php
                echo CHtml::dropDownList('is_opreate', $is_opreate, array(0 => '全部', '1' => '未处理', '2' => '已处理'));
                ?>
            </div>
        </div>
        <div class="row-fluid">

            <?php echo CHtml::submitButton('搜索', array('class' => 'btn btn-primary span2')); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div><!-- search-form -->





<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'driver-complaint-grid',
    'cssFile' => SP_URL_CSS . 'table.css',
    'dataProvider' => $dataProvider,
    'ajaxUpdate' => false,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'columns' => array(
        array(
            'name' => 'id',
            'value' => '$data->id'
        ),
        array(
            'name' => 'order_type',
            'value' => '($data->order_type==1) ? "报单" :"销单"'
        ),
        array(
            'name' => '司机信息',
            'type' => 'raw',
            'value' => 'Yii::app()->controller->getDriverNews($data->driver_user)'
        ),
        array(
            'name' => 'city',
            'type' => 'raw',
            'value' => 'Yii::app()->controller->getDriverCity($data->city)'
        ),
        array(
            'name' => 'customer_phone',
            'type' => 'raw',
            'value' => array($this, 'getCustomerPhone')
        ),
        array(
            'name' => '投诉内容',
            'headerHtmlOptions' => array(
                'style' => 'width:200px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->complaint_content'
        ),
        array(
            'name' => '投诉时间',
            'type' => 'raw',
            'value' => 'date("Y-m-d H:i",$data->create_time)'
        ),
        array(
            'name' => '代驾时间',
            'type' => 'raw',
            'value' => '($data->driver_time!=0) ? date("Y-m-d H:i",$data->driver_time) : "无"'
        ),
        array(
            'name' => '投诉类型',
            'type' => 'raw',
            'value' => array($this, 'getComplaintType')
        ),
        array(
            'name' => '操作',
            'type' => 'raw',
            'value' => array($this, 'getOperates')
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
    $(function () {
        $("a[data-toggle=modal]").click(function () {
            var target = $(this).attr('data-target');
            var url = $(this).attr('url');
            var mewidth = $(this).attr('mewidth');
            if (mewidth == null) mewidth = '850px';
            if (url != null) {
                $('#myModal').modal('toggle').css({'width': mewidth, 'margin-left': function () {
                    return -($(this).width() / 2);
                }});
                $('#myModal').modal('show');
                $('#modal-body').load(url);
            }
            return true;
        });
    });

</script>