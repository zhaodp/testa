<?php
/**
 * Created by JetBrains PhpStorm.
 * User: duke
 * Date: 2014-11-03
 * Time: 下午12:23
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '司机物料详情';
$this->renderPartial('_search',array('id'=>$id));
?>
<div class="container-fluid">
    <div class="tab-content" id="myTabContent">
        <div id="home" class="tab-pane active">
            <div class="row-fluid">
                <div class="span9" style="width:1280px;">
                    <!--Left content-->
                    <strong>司机信息</strong>

                    <div style="padding: 19px;" class="alert alert-success" use="form" id="form_0">
                        <button data-dismiss="alert" class="close" type="button" id="del_0" style="margin-right:20px; display: none">×</button>
                        <div class="input-prepend">
                            <span class="add-on">司机姓名：</span>
                            <span class="add-on"><?php if(isset($userinfo['name'])){ echo $userinfo['name'];}?></span>
                        </div>
                        <div class="input-prepend">
                            <span class="add-on"><?php if($id_type == 'signed'){ echo '司机工号';} else{echo '报名编号';} ?>：</span>
                            <span class="add-on"><?php if($id_type == 'signed'){ echo $userinfo['driver_id'];} else{ echo $id;}?></span>
                        </div><br>
                        <div class="input-prepend "><!--input-append-->
                            <span class="add-on">签约时间：</span>
                            <input type="text" style="width:150px;" readonly="readonly" name="material[signed_date]" id="signed_date"  value="<?php  echo $time['signed'];?>">

                        </div>

                        <div class="input-prepend "><!--input-append-->
                            <span class="add-on">解约时间：</span>
                            <input type="text" style="width:150px;" readonly="readonly" name="material[signed_date]" id="signed_date"  value="<?php  echo $time['unsigned'];?>">
                        </div>
                    </div>

                    <?php $this->renderPartial('driver_material',array('material2driver'=>$material2driver,'mater_info'=>$mater_info,));?>



                    历史操作记录
                    <?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'materialmoneylog-grid',
    'ajaxUpdate' => false,
    'cssFile'=>SP_URL_CSS.'table.css',
    'itemsCssClass' => 'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'dataProvider' => $model_mlog->search(),
    'columns' => array(
        'create_time',
        'operator',
        array(
            'name' => '操作原因',
            'type' => 'raw',
            'value' => 'MaterialMoneyLog::getstatus($data->status)'
        ),
        array(
            'name'=>'content',
            'type' => 'raw',
            'headerHtmlOptions'=>array (
                'width'=>'300px',
                'nowrap'=>'nowrap'
            ),
        ),


        'money',
        array(
            'name'=>'money',
            'type'=>'raw',
            'value'=>'$data->status == 4 ? "-".$data->money : "+".$data->money'
        ),
        array(
            'name'=>'remark',
            'type' => 'raw',
            'headerHtmlOptions'=>array (
                'width'=>'300px',
                'nowrap'=>'nowrap'
            ),
        ),
    ),
));
                    ?>








                </div>
            </div>
        </div>
    </div>
</div>

