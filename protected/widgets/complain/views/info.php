<style>
    .attention {background:rgb(255,204,0);}
</style>
<div style="margin-top:40px;padding-top: -40px;">
    <pre class="well nav-tabs navbar" style="padding:0 20px 0 10px;">
        <a class="brand" href="javascript:;" target="_top">投诉信息</a>
    </pre>
</div>
<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'complain-grid',
    'dataProvider' => $data,
    'showTableOnEmpty' => FALSE,
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'itemsCssClass' => 'table table-striped',
    'rowCssClassExpression' => '"item_".$data->id." ".($data->attention?"info":"")." ".($row%2>0?"odd":"even")',
    'enableSorting' => FALSE,
    'columns' => array(
        array(
            'name' => 'ID',
            'headerHtmlOptions' => array(
                'style' => 'width:10px',
                'nowrap' => 'nowrap'
            ), 'type' => 'raw',
            'value' => '$data->id',
        ),
        array(
            'name' => '投诉来源',
            'headerHtmlOptions' => array(
                'style' => 'width:60px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data->source?CustomerComplain::$source[$data->source]:""',
        ),
        array(
            'name' => '投诉详情',
            'headerHtmlOptions' => array(
                'style' => 'width:200px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->detail'
        ),
        array(
            'name' => '投诉时间',
            'headerHtmlOptions' => array(
                'style' => 'width:60px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->create_time'
        ),
        /* array (
          'name'=>'投诉类型',
          'headerHtmlOptions'=>array (
          'style'=>'width:60px',
          'nowrap'=>'nowrap'
          ),
          'type'=>'raw',
          'value'=>array($this,'getType')
          ),
          array (
          'name'=>'投诉人',
          'headerHtmlOptions'=>array (
          'style'=>'width:60px',
          'nowrap'=>'nowrap'
          ), 'type'=>'raw',
          'value'=>array($this,'complainUser'),
          ),
          array (
          'name'=>'预约电话',
          'headerHtmlOptions'=>array (
          'style'=>'width:60px',
          'nowrap'=>'nowrap'
          ), 'type'=>'raw',
          'value'=>array($this,'customer_phone'),
          ), */
        array(
            'name' => '创建人',
            'headerHtmlOptions' => array(
                'style' => 'width:40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->created'
        ),
        array(
            'name' => '操作人',
            'headerHtmlOptions' => array(
                'style' => 'width:40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->operator'
        ),
    /* array (
      'name'=>'司机',
      'headerHtmlOptions'=>array (
      'style'=>'width:40px',
      'nowrap'=>'nowrap'
      ), 'type'=>'raw',
      'value'=>array($this,'driverInfo'),
      ),
      array (
      'name'=>'订单编号',
      'headerHtmlOptions'=>array (
      'style'=>'width:60px',
      'nowrap'=>'nowrap'
      ),
      'type'=>'raw',
      'value'=>array($this,'orderIdAndNumber')
      ),
      array (
      'name'=>'处理状态',
      'headerHtmlOptions'=>array (
      'style'=>'width:60px',
      'nowrap'=>'nowrap'
      ),
      'type'=>'raw',
      'value'=>array($this,'processStatus')
      ),
      array (
      'header'=>'操作',
      'headerHtmlOptions'=>array (
      'style'=>'width:120px',
      'nowrap'=>'nowrap'
      ),
      'type'=>'raw',
      'value'=>array($this,'opt')
      ), */
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
    $(function() {
        $("a[data-toggle=modal]").click(function() {
            var target = $(this).attr('data-target');
            var url = $(this).attr('url');
            var mewidth = $(this).attr('mewidth');
            if (mewidth == null)
                mewidth = '850px';
            if (url != null) {
                $('#myModal').modal('toggle').css({'width': mewidth, 'margin-left': function() {
                        return -($(this).width() / 2);
                    }});
                $('#myModal').modal('show');
                $('#modal-body').load(url);
            }
            return true;
        });
    });
    function addAttention(ts) {
        $.ajax({
            url: $(ts).attr('href'),
            cache: false,
            success: function(data) {
                if (data.success) {
                    var iii = ".item_" + data.item_id;
                    if (data.attention == 1) {
                        $(iii).addClass("info");
                    } else {
                        $(iii).removeClass("info");
                    }
                    $("#ajaxLink_" + data.item_id).html(data.url);
                } else {
                    if (data.msg) {
                        alert(data.msg);
                    }
                }
            },
            dataType: 'json'
        });
    }

</script>