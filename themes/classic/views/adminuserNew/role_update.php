<?php
$this->pageTitle = '修改角色';
?>

<h1>修改角色</h1>
<hr class="divider"/>
<?php echo $this->renderPartial('_roleform',
    array(
        'model'=>$model,
        'action_info'=> $action_info,
        'dep_name'=>$dep_name,
        'currentUserInfo'=>$currentUserInfo,
        'role_now_action'=>$role_now_action,
        'can_edit'=>$can_edit,
    )); ?>
