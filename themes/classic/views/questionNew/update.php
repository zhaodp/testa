<?php
/* @var $this QuestionController */
/* @var $model Question */

$this->breadcrumbs=array(
    'Questions'=>array('index'),
    $model->title=>array('view','id'=>$model->id),
    'Update',
);

?>

    <h1>修改题目</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'city_ids'=>$city_ids)); ?>