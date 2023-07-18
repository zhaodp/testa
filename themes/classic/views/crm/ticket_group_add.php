<?php
/**
 * 创建 工单组成员
 */
$this->breadcrumbs=array(
    '工单组成员列表'=>array('groupUserList'),
    'Create',
);
if(isset($_GET['error_msg']))
{
    echo '<span style="color: red;"><h4>'.$_GET["error_msg"].'</h4></span>';
}
?>
<h1>添加工单操作人员</h1>

<?php echo $this->renderPartial('_group_user_form', array('model'=>$model)); ?>