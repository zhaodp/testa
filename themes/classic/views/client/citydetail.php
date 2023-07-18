
<?php
    $city = Dict::items('city');
    $city[0] = '无法定位城市';
    echo CHtml::dropDownList('cityname',$cvalue,$city,array('id'=>'cityname'));
?>
<br><hr>
<div id="cityWether" style="width: 1000px;height: 90px; background-color: #AAAAFF;"><span id="cityWetherSpan" ></span></div>
<hr>
<div id="cityPriceDiv"><span id="cityPriceSpan" ><?php echo $feelist?></span></div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/sto/classic/www/js/fancybox/lib/jquery-1.10.2.min.js"></script>
<style type="text/css">
    table{
        width: 400px;
        height: 120px;
        float: left;
    }
    th{
        text-align: left;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        var cityWetherInfos = '<?php echo $cityWetherInfos ?>';
        getWether(cityWetherInfos);
    });
    $("#cityname").change(function(){
        var citySelectValue = $("#cityname option:selected").val();
        var citydetail_url = '<?php echo Yii::app()->createUrl('client/citydetail');?>';
        $.get(
            citydetail_url,
            {'channel' : 'ajax',
                'cvalue':citySelectValue
            },
            function(datas){
                var dataArr = datas.split("-%%-");
                var wetherData = dataArr[0];
                var priceData = dataArr[1];
                getWether(wetherData);//天气信息
                $("#cityPriceSpan").html(priceData);//城市价格信息
            }
        );
    });
    function getWether(cityWetherInfos){
        if(jQuery.parseJSON(cityWetherInfos).status == 'success'){
            var wtherStr ="<b>当前城市</b>:"+jQuery.parseJSON(cityWetherInfos).currentCity+"|"+"&nbsp;&nbsp;&nbsp;|"+ "|"+jQuery.parseJSON(cityWetherInfos).date+"|"+"&nbsp;&nbsp;&nbsp;|<b>天气</b>："+ jQuery.parseJSON(cityWetherInfos).weather+jQuery.parseJSON(cityWetherInfos).wind;
            wtherStr += "|&nbsp;&nbsp;&nbsp;<br><br>|<b>温度</b>："+ jQuery.parseJSON(cityWetherInfos).temperature +"|&nbsp;&nbsp;&nbsp;|";
            wtherStr += "<b>白天</b>：<img src="+jQuery.parseJSON(cityWetherInfos).dayPictureUrl+" border=0>|&nbsp;&nbsp;&nbsp;|";
            wtherStr += "<b>晚上</b>：<img src="+jQuery.parseJSON(cityWetherInfos).nightPictureUrl+" border=0>";
            $("#cityWetherSpan").html(wtherStr);
        }else{
            $("#cityWetherSpan").html("对不起暂时差查不到该城市的天气信息!");
        }
    }
</script>