<?php
/**
 * created by PhpStorm.
 * User: mtx
 * Date: 13-11-6
 * Time: 下午1:58
 * auther mengtianxue
 */
?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'form-submit',
        'enableAjaxValidation' => false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row-fluid">
        <table width="98%">
            <tr>
                <td width="30%">
                    已选择：<span id="bonus_select_num"><?php echo $searchModel->selectNum-$searchModel->problemNum ?></span>张有效实体卷
                </td>
                <td>
                    问题卡：<span id="error_bonus_cord"><?php echo $searchModel->problemNum>0?$searchModel->problemNum:0 ?>

                    </span>张:<a href="#" id="error_cord_list" onclick="errorBonesList()">问题卡</a>
                </td>
            </tr>
        </table>

    </div>


    <div class="row-fluid">
        按号段选择
    </div>
    <div class="row-fluid">
        <div style="width:30%; float: left;">
            <?php echo $form->labelEx($searchModel, 'bonus_sn_start'); ?>
            <?php echo $form->textField($searchModel, 'bonus_sn_start'); ?>
            <?php echo $form->error($searchModel, 'bonus_sn_start'); ?>
        </div>
        <div style="width:30%; float: left;">
            <?php echo $form->labelEx($searchModel, 'bonus_sn_end'); ?>
            <?php echo $form->textField($searchModel, 'bonus_sn_end'); ?>
        </div>
        <div style="width:30%; float: left;">
            <input type="hidden" id="selectBonusType" name="selectBonusType" value="0">
            <input type="hidden" id="selectBonusValue" name="selectBonusValue"
                   value="<?php echo $searchModel->bonus_sn ?>">
            <?php echo $form->labelEx($searchModel, '&nbsp;'); ?>
            <?php echo CHtml::button('选择', array('class' => 'btn', 'onclick' => 'select_bonus()', 'style' => 'width:120px')); ?>
        </div>
    </div>
    <input type="hidden" id="isHas" name="isHas" value="1">
    <?php if ($dis_city == 0) { ?>

        <div class="row-fluid">
            <input id="rl_1" name="rl$tt" type="radio" value="1"  onclick="addChannelRedio()"/>添加新渠道
        </div>

        <div class="row-fluid">
            <div style="width:30%; float: left;">
                <?php echo $form->labelEx($searchModel, 'channel'); ?>
                <?php echo $form->textField($searchModel, 'channel'); ?>
            </div>

            <div style="width:30%; float:left">
                <?php echo $form->labelEx($searchModel, 'contact'); ?>
                <?php echo $form->textField($searchModel, 'contact'); ?>
            </div>
        </div>
        <div class="row-fluid">
            <div style="width:30%; float: left;">
                <?php echo $form->labelEx($searchModel, 'tel'); ?>
                <?php echo $form->textField($searchModel, 'tel'); ?>
            </div>

            <div style=" float:left ">
                <?php echo $form->labelEx($searchModel, '&nbsp;'); ?>
                <?php echo CHtml::button('添加', array('class' => 'btn', 'onclick' => 'add_bonus_channel(0)', 'style' => 'width:120px')); ?>
            </div>

            <div style=" float:left ">
                <?php echo $form->labelEx($searchModel, '&nbsp;'); ?>
                <?php echo CHtml::button('添加并选择', array('class' => 'btn', 'onclick' => 'add_bonus_channel(1)', 'style' => 'width:120px')); ?>
            </div>
        </div>
        <div class="row-fluid">
            <input id="rl_2" name="rl$tt" type="radio" checked="checked" value="2" onclick="SelectChannelRedio()"/>选择已有渠道
        </div>

        <div class="row-fluid">
            <div class="input-prepend input-append">
                <input type="hidden" id="selectChannelId" name="selectChannelId"
                       value="0">

                <?php echo $form->textField($model, 'channel', array('style' => 'width:150px')); ?>
                <?php echo $form->error($model, 'channel'); ?>
            </div>
            <div id="lib_poilist_channel" style="height:100px;width:170px;border:solid 1px gray;overflow-x:scroll;display: none">
                <div style="background: none repeat scroll 0% 0% rgb(255, 255, 255);">
                    <ol style="list-style: none outside none; padding: 0pt; margin: 0pt;"></ol>
                </div>
            </div>

        </div>

<!--        <div class="row-fluid">-->
<!--            <div style="width:30%; float: left;">-->
<!--                --><?php //echo $form->dropDownList($model, 'owner', array('请选择') + $area, array('style' => 'width:120px', 'onchange' => 'select_channel()')); ?>
<!--            </div>-->
<!--        </div>-->

        <div class="row-fluid">
            <table width="400px">
                <tr>
                    <td>
                        渠道名称：
                        <input type="hidden" id="channel_id" name="channel_id" value="">
                    </td>
                    <td>
                        <span id="channel_name"></span>
                    </td>
                    <td>
                        联系人：
                    </td>
                    <td>
                        <span id="channel_contact"></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        联系电话：
                    </td>
                    <td>
                        <span id="channel_tel"></span>
                    </td>
                    <td>
                        渠道创建人：
                    </td>
                    <td>
                        <span id="creat_by"></span>
                    </td>
                </tr>
            </table>

        </div>
    <?php } ?>

    <?php if ($dis_city == 1) { ?>
<!--        <div class="row-fluid">-->
<!--            --><?php //echo $form->labelEx($model, 'city_id'); ?>
<!--            --><?php //echo $form->dropDownList($model, 'city_id', Dict::items('city')); ?>
<!--        </div>-->


        <div class="row-fluid">
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
    <?php } ?>
    <div class="row-fluid">
        <div style="width:27%; float: left;">
            <input type="hidden" id="distri_type" name="distri_type" value="1">
            <input type="hidden" id="dis_city" name="is_manager" value="<?php echo $dis_city ?>">
            <?php if ($is_manager == 0 && $dis_city == 0) { ?>
                <?php echo CHtml::button('销售', array('class' => 'btn', 'style' => 'width:120px', 'onclick' => 'disTypeSubmit()')); ?>
            <?php } ?>

        </div>
        <div style="width:27%; float: left;">

            <?php echo CHtml::button('分配', array('class' => 'btn', 'style' => 'width:120px', 'onclick' => 'disSubmit()')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->


<script type="text/javascript">
    function select_bonus() {
        var bonus_sn_start = $("#BonusLibrarySearch_bonus_sn_start").val();
        var bonus_sn_end = $("#BonusLibrarySearch_bonus_sn_end").val();

        if (bonus_sn_start != '' && bonus_sn_end != '' && (bonus_sn_start <= bonus_sn_end)) {
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('/bonusLibrary/select_bonus');?>',
                'data': {
                    'bonus_sn_start': bonus_sn_start,
                    'bonus_sn_end': bonus_sn_end
                },
                'type': 'get',
                'dataType': 'json',
                'success': function (data) {
                    $('#bonus_select_num').text(data.bonus_sn-data.bonus_id);
                    $('#selectBonusValue').val('');

                    if (data.bonus_sn != data.bonus_id) {
                        $('#selectBonusValue').val('yes');
                    }
                    $('#error_bonus_cord').text(data.bonus_id);
                    $('#selectBonusType').val('1');
                },
                'cache': false
            });
        } else {
            alert("请填写正确的起始、结束号码!");
        }
        return false;
    }


    function add_bonus_channel(flag) {
        var contact = $("#BonusLibrarySearch_contact").val();
        var channel = $("#BonusLibrarySearch_channel").val();
        var tel = $("#BonusLibrarySearch_tel").val();
        if (channel != '' && contact != '' && tel != '') {
            $.ajax({
                'url': '<?php echo Yii::app()->createUrl('/bonusCode/add_bonus_channel');?>',
                'data': {
                    'channel': channel,
                    'contact': contact,
                    'tel': tel,
                    'need_channel': true
                },
                'type': 'get',
                'dataType': 'json',
                'success': function (data) {
                    if (data.code == "1") {
                        alert("添加成功");
                        if (data.list && data.list != '') {
                            $("#BonusLibrary_owner").empty().append(data.list);
                        }

                        if (flag == 1) {
                            $('#isHas').val('1');
                            $('#channel_id').val(data.id);
                            $("#channel_name").text(data.channel);
                            $("#channel_contact").text(data.contact);
                            $("#channel_tel").text(data.tel);
                            $("#creat_by").text(data.creat_by);
                        }
                    } else {
                        alert("添加失败");
                    }
                },
                'cache': false
            });
        } else {
            alert("信息完善够才能添加");
        }
        return false;

    }

    function select_channel() {
        var channel_id = $("#BonusLibrary_owner").val();
        if (channel_id == 0) {
            $('#channel_id').val('');
            $("#channel_name").text('');
            $("#channel_contact").text('');
            $("#channel_tel").text('');
            $("#creat_by").text('');
            return false;
        }

        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/bonusCode/getChannelInfo');?>',
            'data': 'id=' + channel_id,
            'type': 'get',
            'dataType': 'json',
            'success': function (data) {
                $('#channel_id').val(data.id);
                $("#channel_name").text(data.channel);
                $("#channel_contact").text(data.contact == null ? "" : data.contact);
                $("#channel_tel").text(data.tel == null ? "" : data.tel);
                $("#creat_by").text(data.creat_by == null ? "" : data.creat_by);
            },
            'cache': false
        });
    }

    function disSubmit() {

        if (($('#selectBonusType').val() == 0 && $('#selectBonusValue').val() == '') ||
            ($('#selectBonusType').val() == 1 && ($('#selectBonusValue').val() != 'yes' || ($('#BonusLibrarySearch_bonus_sn_start').val() == '' ||
            $('#BonusLibrarySearch_bonus_sn_end').val() == '')))) {
            alert('请选择实体卷!');
            return false;
        }
        if ($('#selectBonusType').val() == 1 && ($('#BonusLibrarySearch_bonus_sn_start').val() >
            $('#BonusLibrarySearch_bonus_sn_end').val())) {
            alert('其实号码不能大于结束号码!');
            return false;
        }


        if ($('#isHas').val()==0||($('#channel_id').val() == '' && $('#dis_city').val() == 0)) {
            alert('请选择渠道!');
            return false;
        }

        if ($('#selectCityId').val() == 0) {
            alert('请选择城市!');
            return false;
        }
        $('#form-submit').submit();
    }


    function disTypeSubmit() {
        if (($('#selectBonusType').val() == 0 && $('#selectBonusValue').val() == '') ||
            ($('#selectBonusType').val() == 1 && ($('#selectBonusValue').val() != 'yes' || ($('#BonusLibrarySearch_bonus_sn_start').val() == '' ||
            $('#BonusLibrarySearch_bonus_sn_end').val() == '')))) {
            alert('请选择实体卷!');
            return false;
        }
        if ($('#selectBonusType').val() == 1 && ($('#BonusLibrarySearch_bonus_sn_start').val() >
            $('#BonusLibrarySearch_bonus_sn_end').val())) {
            alert('其实号码不能大于结束号码!');
            return false;
        }


        if ($('#channel_id').val() == '' && $('#dis_city').val() == 0) {
            alert('请选择渠道!');
            return false;
        }

        if ($('#selectCityId').val() == 0) {
            alert('请选择城市!');
            return false;
        }
        $('#distri_type').val('0');

        $('#form-submit').submit();
    }

    function errorBonesList() {
        var type = $('#selectBonusType').val();
        var value = $('#selectBonusValue').val();

        var dis_city=$('#dis_city').val();

        var start = $('#BonusLibrarySearch_bonus_sn_start').val();
        var end = $('#BonusLibrarySearch_bonus_sn_end').val();


        var src = "<?php echo Yii::app()->createUrl('/bonusLibrary/error_bonus_list');?>" + "&type=" + type
            + "&value=" + value + "&start=" + start + '&end=' + end+'&dis_city='+dis_city;
        window.open(src);
        // alert('errorBonesList');
    }


    function addChannelRedio(){
        $('#isHas').val('0');
        $('#channel_id').val('');
        $('#BonusLibrary_channel').val('');
        $('#BonusLibrary_channel').attr('readonly','readonly');
        $("#channel_name").text('');
        $("#channel_contact").text('');
        $("#channel_tel").text('');
        $("#creat_by").text('');

        $('#BonusLibrarySearch_channel').removeAttr('readonly');
        $('#BonusLibrarySearch_contact').removeAttr('readonly');
        $('#BonusLibrarySearch_tel').removeAttr('readonly');
    }
    function SelectChannelRedio(){
        $('#isHas').val('1');
        $('#BonusLibrary_channel').removeAttr('readonly');
        $('#BonusLibrarySearch_channel').val('');
        $('#BonusLibrarySearch_contact').val('');
        $('#BonusLibrarySearch_tel').val('');

        $('#BonusLibrarySearch_channel').attr('readonly','readonly');
        $('#BonusLibrarySearch_contact').attr('readonly','readonly');
        $('#BonusLibrarySearch_tel').attr('readonly','readonly');
    }

    ;(function($){
        $('#BonusLibrarySearch_channel').attr('readonly','readonly');
        $('#BonusLibrarySearch_contact').attr('readonly','readonly');
        $('#BonusLibrarySearch_tel').attr('readonly','readonly');
        var $city = $("#BonusLibrary_city_id");
        var $channel = $("#BonusLibrary_channel");
        $city.keyup(function(e){
            refreshCity();
        });

        $city.keydown(function(e){
            if(e.keyCode==13){
                refreshCity();
            }
        });

        $channel.keyup(function(e){
            refreshChannel();
        });

        $channel.keydown(function(e){
            if(e.keyCode==13){
                refreshChannel();
            }
        });

        function refreshCity() {

            var searchStr=$('#BonusLibrary_city_id').val();

            if(searchStr==''){
                $('#selectCityId').val('0');
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
                        $('#selectCityId').val('0');
                    }
                });
        }


        function refreshChannel() {

            var searchStr=$('#BonusLibrary_channel').val();

            if(searchStr==''){
                $('#selectChannelId').val('0');
                $('#lib_poilist_channel').hide();
                return false;
            }

            $.get('index.php?r=bonusCode/getChannelByArea&channel='+searchStr
                ,function(res){

                    var s = '<ol>';
                    res=$.parseJSON(res);

                    if(res.code==1){
                        var city=res.arr;
                        for (var i=0;i<city.length && i<20;i++){
                            var oneres=city[i];
                            s +='<li style="margin: 2px 0pt; padding: 0pt 5px 0pt 3px; cursor: pointer; overflow: hidden; line-height: 17px;" data-name="'
                            +oneres.channel+'" data-id="'+oneres.id+'" data-tel="'+oneres.tel+'"  data-contact="'+oneres.contact+'" data-creat_by="'+oneres.creat_by+'">';
                            s +='<span class="placeTitle" style="color:#00c;">'+oneres.channel+'</span>';
                            s +='</li>';
                        }
                        s +='</ol>';

                        s=$(s).find("li").click(function(){
                            $('#BonusLibrary_channel').val($(this).find(".placeTitle").text());
                            $('#lib_poilist_channel').hide();

                            $('#channel_id').val($(this).attr('data-id'));
                            $("#channel_name").text($(this).attr('data-name'));
                            $("#channel_contact").text($(this).attr('data-contact')=='null'?'':$(this).attr('data-contact'));
                            $("#channel_tel").text($(this).attr('data-tel')=='null'?'':$(this).attr('data-tel'));
                            $("#creat_by").text($(this).attr('data-creat_by')=='null'?'':$(this).attr('data-creat_by'));
                        });

                        $('#lib_poilist_channel').show();
                        $("#lib_poilist_channel ol").html("").append(s);
                    }else{
                        $('#lib_poilist_channel').hide();
                        $('#channel_id').val($(this).attr('0'));
                    }
                });
        }
    })(jQuery);
</script>

