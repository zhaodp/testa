<?php
/* @var $this CityConfigController */
/* @var $dataProvider CActiveDataProvider */

?>
<h1>城市配置缓存管理</h1>
<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'city-config-form',
    'enableAjaxValidation' => false,
)); ?>
<div class="row-fluid">

    <div class="span3">
            <label class="required" for="">城市配置查看项(*是带参数)</label>
            <select id="city_key" name="city_key">
                <option value="getDriverCityLt100" <?php echo (isset($city_arr['city_key']) && $city_arr['city_key'] == 'getDriverCityLt100') ? 'selected': '';?>>小于100划款城市名单</option>
                <option value="getDriverCityLt200" <?php echo (isset($city_arr['city_key']) && $city_arr['city_key'] == 'getDriverCityLt200') ? 'selected': '';?>>小于200划款城市名单</option>
                <option value="getCityFeeEq19" <?php echo (isset($city_arr['city_key']) && $city_arr['city_key'] == 'getCityFeeEq19') ? 'selected': '';?>>实体劵返现19的城市</option>
                <option value="getFee" <?php echo (isset($city_arr['city_key']) && $city_arr['city_key'] == 'getFee') ? 'selected': '';?>>获取城市收费标准 *</option>
                <option value="getOpenCityList" <?php echo (isset($city_arr['city_key']) && $city_arr['city_key'] == 'getOpenCityList') ? 'selected': '';?>>开通城市列表</option>
                <option value="getCityByPrifix" <?php echo (isset($city_arr['city_key']) && $city_arr['city_key'] == 'getCityByPrifix') ? 'selected': '';?>>根据城市前缀获取城市信息 * </option>
                <option value="getCityByID" <?php echo (isset($city_arr['city_key']) && $city_arr['city_key'] == 'getCityByID') ? 'selected': '';?>>根据城市ID获取城市信息 * </option>
        </select>
    </div>

    <div class="span3">
        <label class="required" for="">
            参数
        </label>
        <input id="city_params" type="text" name="city_params" maxlength="100" size="100" value="<?php echo isset($city_arr['city_params']) ? $city_arr['city_params'] : '';?>">
    </div>

    <div class="span3">
        <?php echo $form->labelEx($model, '&nbsp;'); ?>
        <?php echo CHtml::submitButton('查询信息', array('name' => 'btn1', 'class' => 'btn')); ?>
        <?php echo CHtml::submitButton('重新加载', array('name' => 'btn2', 'class' => 'btn')); ?>
    </div>

</div>


<?php $this->endWidget(); ?>


<pre>
    <?php
    print_r($fun_value);
    ?>
</pre>
