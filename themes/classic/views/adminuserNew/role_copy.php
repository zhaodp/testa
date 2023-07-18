<?php
$this->pageTitle = '复制角色';
?>

<h1>复制角色</h1>
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

<script>
    $(function(){
        var a = $('#AdminRole_name').val();
        $('#AdminRole_name').val(a+'(复制)');
    });
</script>
