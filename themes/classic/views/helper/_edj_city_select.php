<?php
/**
 * Desc：
 * Created by Huangjiangang
 * Created on 2021/6/3 14:18
 */
/* @var $edj_city_select EdjCitySelect*/

if (strpos($_SERVER['HTTP_HOST'], '.d.edaijia')) {
    $host = 'https://h5.d.edaijia.cn';
} else {
    $host = 'https://h5.edaijia.cn';
}
Yii::app()->clientScript->registerCssFile($host . '/core/libs/city-grade/style.css');
Yii::app()->clientScript->registerScriptFile($host . '/core/libs/city-grade/componentCity.js');
Yii::app()->clientScript->registerScriptFile($host . '/core/libs/city-grade/artTemplate.js');
Yii::app()->clientScript->registerScriptFile($host . '/core/libs/city-grade/xlsx.core.min.js');
?>


<div class="
     <?php
$class = isset($style['class']) ? $style['class'] : "span3";
echo $class;
?>
">
    <?php
    $edj_city_select->initComponentHtml();
    ?>
</div>

<script>
    <?php
    $edj_city_select->initComponentScript();
    ?>
</script>