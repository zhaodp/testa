<?php
$this->pageTitle = '修改用户信息';
?>

    <h1>修改用户信息</h1>
    <hr class="divider"/>
<?php echo $this->renderPartial('_form', array('model'=>$model,'admin_info'=>$admin_info,'role_info'=> $role_info,'my_role_info'=>$my_role_info,'city_list_dict'=>$city_list_dict,'organization'=>$organization)); ?>
