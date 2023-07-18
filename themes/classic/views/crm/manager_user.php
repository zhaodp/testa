<?php
$this->breadcrumbs=array(
    '列表'=>array('ticketList'),

);

?>
<div class="span5">
<h3>工单用户信息</h3>
<h4><a href="<?php echo Yii::app()->createUrl("/crm/groupUserList");?>">返回列表</a></h4>
<?php if(!empty($model)){?>

<table class="table .table-bordered">
    <tr>
        <td>ID:</td>
        <td><?php echo $model->id;?></td>
    </tr>
    <tr>
        <td>用户名:</td>
        <td><?php echo $model->user;?></td>
    </tr>
    <tr>
        <td>管理员:</td>
        <td><?php echo $model->is_admin?"是":"否";?></td>
    </tr>
    <tr>
        <td>部门:</td>
        <td><?php echo $this->getGroup($model);?></td>
    </tr>
    <tr>
        <td>负责分类:</td>
        <td><?php echo $this->getCategory($model);?></td>
    </tr>
    <tr>
        <td> 城市:</td>
        <td><?php echo Dict::item("city",$model->city_id);?></td>
    </tr>
    <tr>
        <td> 排序:</td>
        <td><?php echo $model->cursor_sort;?></td>
    </tr>
    <tr>
        <td> 状态:</td>
        <td><?php echo $model->status == 1?"有效":"无效";?></td>
    </tr>


</table>
</div>
<?php ?>

<?php }else{
    echo "<span style='color: red;'>用户不存在</span>";
}?>
