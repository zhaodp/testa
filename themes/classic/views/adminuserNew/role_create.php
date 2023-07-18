<?php
$this->pageTitle = '新建角色';
?>

<h1>新建角色</h1>
<hr class="divider"/>
<?php echo $this->renderPartial('_roleform',
    array(
        'model'=>$model,
        'action_info'=> $action_info,
        'dep_name'=>$dep_name,
        'currentUserInfo'=>$currentUserInfo,
        'can_edit'=>$can_edit,
    )); ?>
