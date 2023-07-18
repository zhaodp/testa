<?php
/* @var $this CityConfigController */
/* @var $model CityConfig */

?>

<h1><?php echo $model->city_name;?> 城市配置修改</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>