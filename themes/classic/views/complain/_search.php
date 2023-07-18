
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'id'=>'complain-list-search',
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>

    <div class="row-fluid">
        <div class="span3">
            <?php echo $form->label($model,'order_id',array('class'=>'control-label')); ?>
            <?php echo CHtml::textField('order_id',$model->order_id,array('class'=>'input-large','placeholder'=>'订单编号'));?>
        </div>
        <div class="span3">
            <?php echo $form->label($model,'driver_id',array('class'=>'control-label')); ?>
            <?php echo CHtml::textField('driver_id',$model->driver_id,array('class'=>'input-large','placeholder'=>'司机工号'));?>
        </div>
        <div class="span3">
            <?php echo $form->label($model,'customer_phone',array('class'=>'control-label')); ?>
            <?php echo CHtml::textField('customer_phone',$model->customer_phone,array('class'=>'input-large','placeholder'=>'客户电话'));?>
        </div>
		<!--   By曾志海    start -->
        <div class="span3">
            <?php echo $form->label($model,'operator',array('class'=>'control-label')); ?>
            <?php echo CHtml::textField('operator',$model->operator,array('class'=>'input-large','placeholder'=>'处理人'));?>
        </div>
        <!--   By曾志海   end -->
    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('投诉时间','create_time');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'start_time',
                'value'=>$s_time,
                'mode'=>'datetime',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"开始",
                ),


            ));?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'end_time',
                'value'=>$e_time,
                'mode'=>'datetime',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),  // jquery plugin options
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"结束",
                ),
            ));
            ?>
            <?php echo CHtml::label('城市','city_id');?>
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
                    $city_id = $model->city_id;
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
            <?php echo CHtml::label('处理时间','create_time');?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'handle_start_time',
                'value'=>$h_s_time,
                'mode'=>'datetime',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"开始",
                ),


            ));?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array (
                'name'=>'handle_end_time',
                'value'=>$h_e_time,
                'mode'=>'datetime',  //use "time","date" or "datetime" (default)
                'options'=>array (
                    'dateFormat'=>'yy-mm-dd'
                ),  // jquery plugin options
                'language'=>'zh',
                'htmlOptions'=>array(
                    'placeholder'=>"结束",
                ),
            ));
            ?>
           <?php echo CHtml::label('关注状态','attention');?>
            <?php echo CHtml::dropDownList('attention',$model->attention, array(''=>'全部','0'=>'0','1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9')); ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('投诉类型','reason');?>
            <?php  echo CHtml::dropDownList('complain_maintype',
                $parent_id,
                $typelist,
                array(
                    'ajax' => array(
                        'type'=>'POST', //request type
                        'url'=>Yii::app()->createUrl('complain/getsubtypeall'),
                        'update'=>'#sub_type', //selector to update
                        'data'=>array('complain_maintype'=>'js:$("#complain_maintype").val()')
                    ))
            );?>
            <?php echo CHtml::dropDownList('sub_type',$child_id,$child); ?>
            <?php echo CHtml::label('来源','source');?>
            <?php echo CHtml::dropDownList('source',$model->source,CustomerComplain::$source,array('empty'=>'全部')) ?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('状态','status');?>
            <?php echo CHtml::dropDownList('status', $model->status?$model->status:'0',CustomerComplain::$newStatus); ?>
            <?php echo CHtml::label('处理节点','status');?>
            <?php echo CHtml::dropDownList('pnode', $model->pnode?$model->pnode:'0',CustomerComplain::$pnode); ?>
            <?php echo CHtml::label('是否回复','reply_status');?>
            <?php echo CHtml::dropDownList('reply_status', $model->reply_status,array('empty'=>'全部','0'=>'否','1'=>'是')); ?>
        </div>

    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('投诉任务人','user');?>
            <?php  echo CHtml::dropDownList('group_id',
                $task_gid,
                $grouplist,
                array(
                    'ajax' => array(
                        'type'=>'POST', //request type
                        'url'=>Yii::app()->createUrl('complain/getgroupuser'),
                        'update'=>'#user_id', //selector to update
                        'data'=>array('group_id'=>'js:$("#group_id").val()')
                    ))
            );?>
            <?php echo CHtml::dropDownList('user_id',$task_uid,$userlist); ?>

        </div>
        <div class="span3">
            <?php echo CHtml::label('个人任务','task_person');?>
            <span id="task_my"></span>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span3">
            <?php echo CHtml::label('投诉ID','id');
            echo CHtml::textField('id',$model->id,array('class'=>'input-large','placeholder'=>'投诉ID'));?>
        </div>
        <div class="span3">
            <?php echo CHtml::label('投诉ID尾数','id_tail');
            $search_id_tail_arr = array(0,1,2,3,4,5,6,7,8,9);
            echo CHtml::dropDownList('id_tail',(isset($_GET['id_tail']) ? $_GET['id_tail'] : ''), $search_id_tail_arr , array('empty'=>'全部')) ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span10">
            <button class="btn btn-primary span2" type="submit" name="search">搜索</button>&nbsp;&nbsp;

            <?php echo  CHtml::link('新建投诉',Yii::app()->createUrl('complain/add'),  array('target'=>'_blank','class'=>'btn')); ?>
            <!-- Button to trigger modal -->
            <?php echo  CHtml::link('投诉分类',Yii::app()->createUrl('complain/typelist'),  array('target'=>'_blank','class'=>'btn')); ?>
            <?php echo  CHtml::link('投诉派工设置',Yii::app()->createUrl('complain/dispatch'),  array('target'=>'_blank','class'=>'btn')); ?>
            <?php echo CHtml::Button('下载当前数据到excel',array('class'=>'btn btn-success','id'=>'down_excel_btn')); ?>
        </div>

    </div>
    <?php $this->endWidget(); ?>


<script>
    $.get('index.php?r=complain/getusertaskstat',function(data){
        $('#task_my').html(data);
    });
</script>




