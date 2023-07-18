<div class="well span12">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'form-submit',
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>


    <div class="span3">
        <div class="input-prepend input-append">
            <input type="hidden" id="selectCityId" name="selectCityId"
                   value="0">
            <?php echo $form->label($model, '城市'); ?>
            <?php echo $form->textField($model, 'channel', array('style' => 'width:150px')); ?>
            <?php echo $form->error($model, 'channel'); ?>
        </div>
        <div id="lib_poilist" style="height:100px;width:170px;border:solid 1px gray;overflow-x:scroll;display: none">
            <div style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                <ol style="list-style: none outside none; padding: 0pt; margin: 0pt;"></ol>
            </div>
        </div>

    </div>

    <div class="row span3">
        <?php echo $form->label($model, 'created'); ?>
        <?php

        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array(
            'name' => 'dateStart',
            'model' => '', //Model object
            'value' => $dateStart,
            'value' => '',
            'mode' => 'datetime', //use "time","date" or "datetime" (default)
            'options' => array(
                'dateFormat' => 'yy-mm-dd'
            ), // jquery plugin options
            'language' => 'zh'
        ));

        ?>
    </div>

    <div class="row span3">
        <?php echo $form->label($model, 'update'); ?>
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


    <div class="row buttons">
        <br>
        <?php echo CHtml::button('搜索', array('class' => 'btn', 'id' => 'test', 'onclick' => 'searchSubmit()')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->


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

        var $address = $("#BonusLibrary_channel");

        $address.keyup(function(e){
            refreshAddressPool();
        });

        $address.keydown(function(e){
            if(e.keyCode==13){
                refreshAddressPool();
            }
        });

        function refreshAddressPool() {
            var searchStr=$('#BonusLibrary_channel').val();

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
                            $('#BonusLibrary_channel').val($(this).find(".placeTitle").text());
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
