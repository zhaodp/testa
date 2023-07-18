<?php
$this->pageTitle = '价格表 - e代驾';
$cs = Yii::app()->clientScript;
$cs->coreScriptPosition = CClientScript::POS_HEAD;
$cs->registerScriptFile(SP_URL_JS . 'common.js', CClientScript::POS_END);

?>
<style type="text/css">
#edaijia_price {  display: none; }
</style>
<div id="edaijia_price">
    <?php
    if($city_fee['citys']){
        foreach($city_fee['citys'] as $k => $v){

            echo '<h4>价格表(';
            $city_str = implode('、',$v);
            echo $city_str;
            echo ')</h4>';


            $str = CityConfig::model()->getfeelist($k);
            echo $str;
            echo '<br>';
        }

    } ?>

</div>

<script type='text/temple' id='priceList-tpl'>
    <#macro data>
        <h4>价格表(${data.city})</h4>
        <table class="table">
            <thead>
            <tr>
                <td>时间段</td>
                <td>代驾费(${data.price_list.distince}公里)</td>
            </tr>
            </thead>
            <tbody>
            <#list data.price_list.part_price as list >
                <tr>
                    <td width="50%">${list.part}</td>
                    <td width="50%">　${list.price}元</td>
                </tr>
            </#list>
            </tbody>
        </table>
        <br>

        <div>
            <p>1：${data.memo[1]}</p>
            <p>2：${data.memo[2]}</p>
            <p>3：${data.memo[3]}</p>
        </div>
    </#macro>
</script>
