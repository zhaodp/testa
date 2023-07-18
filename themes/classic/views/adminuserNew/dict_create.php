<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-9-2
 * Time: 下午5:49
 */
$this->pageTitle = '新建字典';
?>

    <h1><?php echo $this->pageTitle; ?></h1>

<?php echo $this->renderPartial('_form_dict', array('model'=>$model)); ?>