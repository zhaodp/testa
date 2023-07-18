<?php $this->renderPartial('tab',array('tab'=> 4));
    Yii::app()->clientScript->registerScript('search', "

$('.search-form form').submit(function(){
    if($('#searchType').val() == 0){
        if($('#Material2Driver_city_id').val() == '' || $('#Material2Driver_type_id').val() == '' || $('#Material2Driver_m_id').val() == ''){
            alert('当使用无此物料的搜索条件时必须选择前三项搜索条件');
            return false;
        }
    }
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'报名信息',
        'autoOpen'=>false,
        'width'=>'800',
        'height'=>'600',
        'modal'=>true,
        'buttons'=>array(
            'Close'=>'js:function(){$("#mydialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="msg_frame" width="100%" height="100%" style="border:0px"></iframe>';

$this->endWidget('zii.widgets.jui.CJuiDialog');

?>
<div class="row search-form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>
    <div class="row span2">
        <?php echo $form->label($model,'city_id');
        $user_city_id = Yii::app()->user->city;

        if ($user_city_id != 0) {
            $city_list = array(
                '城市' => array(
                    $user_city_id => Dict::item('city', $user_city_id)
                )
            );
            $city_id = $user_city_id;
        } else {
            $city_id = isset($_GET['Material2Driver']['city_id']) ? $_GET['Material2Driver']['city_id'] : $model->city_id;
            $city_list = CityTools::cityPinYinSort();
        }
        $this->widget("application.widgets.common.DropDownCity", array(
            'cityList' => $city_list,
            'name' => 'Material2Driver[city_id]',
            'value' => $city_id,
            'type' => 'modal',
            'htmlOptions' => array(
                'style' => 'width: 134px; cursor: pointer;',
                'readonly' => 'readonly',
            )
        ));
        ?>
    </div>

    <div class="row span1">
        <?php
        echo $form->label($model,'type_id');
        if($searchType === '0') {
            echo CHtml::dropDownList('Material2Driver[type_id]',(isset($_GET['Material2Driver']['type_id']) ? $_GET['Material2Driver']['type_id'] : '') ,$type_list, array('empty'=>'全部','style'=>'width:100px'));
        }
        else{
            echo $form->dropDownList($model, 'type_id', $type_list, array('empty'=>'全部','style'=>'width:100px'));
        }
        //echo $_GET['Material2Driver']['type_id'];echo 'aaaaaaaaaaa';
        ?>
    </div>

    <div class="row span2">
        <?php echo $form->label($model,'m_id');
        echo '<span id="mater_box">';
        if($searchType === '0') {
            echo CHtml::dropDownList('Material2Driver[m_id]',(isset($_GET['Material2Driver']['m_id']) ? $_GET['Material2Driver']['m_id'] : '') ,$material_list, array('empty'=>'全部'));

        }else{
            echo $form->dropDownList($model,'m_id',$material_list,array('empty'=>'全部'));
        }
        echo '</span>';?>
    </div>

    <div class="row span3">
        <label for="searchType">有无此物料：</label>
        <?php echo CHtml::dropDownList('searchType',(isset($_GET['searchType']) ? $_GET['searchType'] : ''),array('1'=>'有','0'=>'无'),array('style'=>'width:100px')); ?>

    </div>

    <div class="row span2">
        <label>&nbsp;</label>
        <?php echo CHtml::submitButton('Search',array('class'=>'btn search-button')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>
<hr class="divider" />

<div class="row span2"><?php echo CHtml::Button('发送短信',array('class'=>'btn btn-success','id'=>'send_msg_btn', 'act'=>'send_msg_btn', 'func'=>'send_msg')); ?>
</div>
<?php

if($searchType === '0'){

    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'material_list',
        'dataProvider'=>$dataProvider,
        'itemsCssClass'=>'table table-striped',
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'columns'=>array(
            array(
                'selectableRows'=>2,
                'class' => 'CCheckBoxColumn',
                'checkBoxHtmlOptions' => array(
                    'name' => 'driver_id[]',
                    'value'=> '$data["driver_id"]',
                ),

            ),
            array(
                'name'=>'工号',
                'value'=>'$data["driver_id"]',
            ),
            array(
                'name'=>'数量',
                'value'=>'$data["quantity"]',
            ),
            array(
                'name' => '物料类型',
                'value' => '$data["type_name"]',
            ),
            array(
                'name' => '物料ID',
                'value' => '$data["m_name"]',
            ),
            array (
                'class'=>'CButtonColumn',
                'template'=>'{detail}',
                'buttons'=>array (
                    'detail'=>array (
                        'label'=>'详情',
                        'url'=>'$this->grid->controller->createUrl("detail",array("id"=>$data["driver_id"],"dialog"=>1,"grid_id"=>$this->grid->id));',
                    )
                )
            )

        )
    ));
    echo $page;

}
else

    $this->widget('zii.widgets.grid.CGridView', array (
        'id'=>'material_list-grid',
        'itemsCssClass'=>'table table-striped',
        'dataProvider'=>$model->search(),
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        'columns'=>array (
            array(
                'selectableRows'=>2,
                'class' => 'CCheckBoxColumn',
                'checkBoxHtmlOptions' => array(
                    'name' => 'driver_id[]',
                ),

            ),
            'driver_id',
            'quantity',
            array(
                'name'=>'type_id',
                'value'=>' Material::getTypeInfoName((int)$data->type_id)',
            ),
            array (
                'name'=>'m_id',
//            'headerHtmlOptions'=>array (
//                'width'=>'100px'),
                'type'=>'raw',
                //'value'=>array($this,'showGroupName')),
                'value'=>'$this->grid->controller->getminfo($data->m_id)'
            ),

            array (
                'class'=>'CButtonColumn',
                'template'=>'{detail}',
                'buttons'=>array (
                    'detail'=>array (
                        'label'=>'详情',
                        'url'=>'$this->grid->controller->createUrl("detail",array("id"=>$data->driver_id,"dialog"=>1,"grid_id"=>$this->grid->id));',
                    )
                )
            )
        )
    ));


?>

<script>
    $(document).ready(function(){
        $('#Material2Driver_type_id').change(function(){
            var type_id = $('#Material2Driver_type_id').val();
            if(type_id != '' ){
                var url = '<?php echo Yii::app()->createUrl('material/ajaxGetMaterialHtml');?>&type_id='+type_id;
            }else{
                var url = '<?php echo Yii::app()->createUrl('material/ajaxGetMaterialHtml');?>';
            }
            $.get(url,
                function(result){
                    $('#mater_box').html(result);
                });
        });

        //通知
        $("#send_msg_btn").click(function(){

            $(".ui-dialog-title").html("发送短信");

            id_length = $("input[name='driver_id[]']:checked").length;
            if(id_length<=0){
                    alert("请选择需要发送短信的司机！");
                return false;
            }
            id_str = '';
            for(i=0;i<id_length;i++)
            {
                id_str += $("input[name='driver_id[]']:checked").eq(i).parent().next().html()+',';
            }

            url = '<?php echo Yii::app()->createUrl('/material/sendMsg');?>&ids_str='+id_str;
            $("#msg_frame").attr("src",url);
            $("#mydialog").dialog("open");
        });
    });
</script>