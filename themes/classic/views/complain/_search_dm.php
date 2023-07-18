
    <?php
    $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
    )); ?>

    <div class="row-fluid">
        <div class="span3">
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
            <?php echo CHtml::label('来源','source');?>
            <?php echo CHtml::dropDownList('source',$source,array_merge(array('-1'=>'全部'),CustomerComplain::$source)) ?>

        </div>
        <div class="span3">
            <?php echo CHtml::label('处理状态', 'dm_process');?>
            <?php
                $statusArray = CustomerComplain::$newStatus;
                echo CHtml::dropDownList('dm_process', $status, $statusArray);
            ?>
        </div>

    </div>
    <div class="row-fluid">

        <div class="span3">
            <?php echo CHtml::label('投诉类型','reason');?>
            <?php  echo CHtml::dropDownList('complain_maintype',
                $complain_maintype,
                $typelist,
                array(
                    'ajax' => array(
                        'type'=>'POST', //request type
                        'url'=>Yii::app()->createUrl('complain/getsubtype'),
                        'update'=>'#sub_type', //selector to update
                        'data'=>array('complain_maintype'=>'js:$("#complain_maintype").val()')
                    ),'style' => 'width:110px')
            );?>
            <?php echo CHtml::dropDownList('sub_type',$sub_type, $childTypeList, array('style' => 'width:110px;')); ?>

        </div>
        <div class="span3">
            <?php echo CHtml::label('司机工号','driver_id');?>
            <?php echo CHtml::textField('driver_id',$driver_id,array('class'=>'input-large','placeholder'=>'司机工号'));?>
        </div>
        <div class="span3">
            <label for="search">&nbsp;</label>
            <button class="btn btn-primary" type="submit" name="search" value="search">搜索</button>
        </div>

    </div>


    <?php $this->endWidget(); ?>

