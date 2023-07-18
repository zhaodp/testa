<?php
/* @var $this QuestionController */
/* @var $model Question */

$this->breadcrumbs=array(
    'Questions'=>array('index'),
    'Create',
);

?>

    <h1>新建题目</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>