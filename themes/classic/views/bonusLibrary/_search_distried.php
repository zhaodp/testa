<div class="well row-fluid">
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'form-submit',
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <div class="span12">
        <div class="span3">
            <?php echo $form->label($model, '开始时间'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'dateStart',
                'model' => '', //Model object
                'value' => $dateStart,
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh'
            ));

            ?>
        </div>


        <div class="span3">
            <?php echo $form->label($model, '结束时间'); ?>
            <?php

            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'dateEnd',
                'model' => '', //Model object
                'value' => $dateEnd,
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh'
            ));

            ?>
        </div>
        <?php if ($channel == 0 && $type!=3) { ?>
            <div class="span3">
                <?php echo $form->labelEx($model, '渠道'); ?>
                <?php echo $form->textField($model, 'channel', array('size' => 30, 'maxlength' => 30)); ?>
            </div>
        <?php } ?>

        <?php if ($show_type == 0 ||$show_type == 1) { ?>
            <div class="span3">
                <div class="input-prepend input-append">
                    <input type="hidden" id="selectCityId" name="selectCityId"
                           value="0">
                    <?php echo $form->label($model, '城市'); ?>
                    <?php echo $form->textField($model, 'city_id', array('style' => 'width:150px')); ?>
                    <?php echo $form->error($model, 'city_id'); ?>
                </div>
                <div id="lib_poilist" style="height:100px;width:170px;border:solid 1px gray;overflow-x:scroll;display: none">
                    <div style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                        <ol style="list-style: none outside none; padding: 0pt; margin: 0pt;"></ol>
                    </div>
                </div>

            </div>
        <?php
        }
        ?>

        <?php if ($type == 0 || $show_type==3) { ?>
            <div class="span3">
                <?php echo $form->labelEx($model, '使用状态'); ?>
                <?php
                // 过滤掉区域固定码
                $snStatus = array('0' => '全部', '1' => '未使用', 2 => '已使用');
                echo $form->radioButtonList($model, 'sn_type', $snStatus,
                    array(
                        'tabIndex' => 4,
                        'template' => '{input}{label}',
                        'separator' => '&nbsp;&nbsp;',
                        'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:2px;')));?>
            </div>
        <?php } ?>

        <div class="span3">
            <?php echo $form->labelEx($model, 'bonus_sn'); ?>
            <?php echo $form->textField($model, 'bonus_sn', array('size' => 30, 'maxlength' => 30)); ?>
            <?php echo $form->error($model, 'bonus_sn'); ?>
        </div>

        <div class="span3">
            <?php echo $form->labelEx($model, 'password'); ?>
            <?php echo $form->textField($model, 'password', array('size' => 30, 'maxlength' => 30)); ?>
            <?php echo $form->error($model, 'password'); ?>
        </div>



        <?php if ($show_type == 2|| $show_type==3) { ?>
            <div class="span3">
                <?php echo $form->labelEx($model, '分配人'); ?>
                <?php echo $form->dropDownList($model, 'distri_by',array('' => '请选择')+ $arr_dis); ?>
            </div>

            <div class="span3">
                <?php echo $form->labelEx($model, '实体卷名称'); ?>
                <?php echo $form->textField($model, 'create_by', array('size' => 30, 'maxlength' => 30)); ?>
            </div>
        <?php
        }
        ?>


        <?php if (($is_manager == 0 && $type == 0) || $channel > 0) { ?>

            <div class="span3">
                <?php echo $form->labelEx($model, '类型'); ?>
                <?php
                // 过滤掉区域固定码
                $snDisType = array('0' => '全部', '1' => '销售', 2 => '分配');
                ?>
                <?php echo $form->radioButtonList($model, 'distri_type', $snDisType,
                    array(
                        'tabIndex' => 4,
                        'template' => '{input}{label}',
                        'separator' => '&nbsp;&nbsp;',
                        'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:2px;')));?>
            </div>
        <?php } ?>

        <?php if ($is_manager == 0 && $type == 3) { ?>

            <div class="span3">
                <?php echo $form->labelEx($model, '类型'); ?>
                <?php
                // 过滤掉区域固定码
                $operat_by = array('0' => '全部', '1' => '坏卡', 2 => '未分配');
                ?>
                <?php echo $form->radioButtonList($model, 'operat_by', $operat_by,
                    array(
                        'tabIndex' => 4,
                        'template' => '{input}{label}',
                        'separator' => '&nbsp;&nbsp;',
                        'labelOptions' => array('class' => 'radio inline', 'style' => 'padding-left:2px;')));?>
            </div>
        <?php } ?>

        <div class="span3">
            <?php echo $form->label($model, '&nbsp'); ?>
            <?php echo CHtml::button('搜索', array('class' => 'btn', 'onclick' => 'searchSubmit()')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
    function searchSubmit() {
        if ($('#dateStart').val() > $('#dateEnd').val()) {
            alert('开始时间不能大于结束时间!');
            return false;
        }
        if($('#selectCityId').val()==-1){
            alert('请选择城市!');
            return false;
        }
        $('#form-submit').submit();
    }

    /**
     *  城市列表
     **/
    ;(function($){
        var $address = $("#BonusLibrary_city_id");

        $address.keyup(function(e){
            refreshAddressPool();
        });

        $address.keydown(function(e){
            if(e.keyCode==13){
                refreshAddressPool();
            }
        });

        function refreshAddressPool() {

            var searchStr=$('#BonusLibrary_city_id').val();

            if(searchStr==''){
                $('#selectCityId').val($(this).attr('0'));
                $('#lib_poilist').hide();
                return false;
            }

            $.get('index.php?r=bonusLibrary/getCityList&city='+searchStr
                ,function(res){

                    var s = '<ol>';
                    res=$.parseJSON(res);

                    if(res.code==1){
                        var city=res.arr;
                        for (var i=0;i<city.length && i<20;i++){
                            var oneres=city[i];
                            s +='<li style="margin: 2px 0pt; padding: 0pt 5px 0pt 3px; cursor: pointer; overflow: hidden; line-height: 17px;" data-name="'+oneres.name+'" data-city="'+oneres.city_id+'">';
                            s +='<span class="placeTitle" style="color:#00c;">'+oneres.name+'</span>';
                            s +='</li>';
                        }
                        s +='</ol>';

                        s=$(s).find("li").click(function(){
                            $('#BonusLibrary_city_id').val($(this).find(".placeTitle").text());
                            $('#lib_poilist').hide();
                            $('#selectCityId').val($(this).attr('data-city'));
                        });
                        $('#lib_poilist').show();
                        $("#lib_poilist ol").html("").append(s);
                    }else{
                        $('#lib_poilist').hide();
                        $('#selectCityId').val($(this).attr('-1'));
                    }
                });
        }
    })(jQuery);
</script>

