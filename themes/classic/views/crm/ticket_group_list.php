<?php
/* @var $this VipController */
/* @var $model Vip */


$this->breadcrumbs=array(
    '工单用户'=>array('groupUserList'),
    'Manage',
);


$this->pageTitle = '工单用户管理';
$count = 0;
?>
<h4><a href="<?php echo Yii::app()->createUrl("/crm/groupUserAdd");?>">添加用户</a></h4>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'Ticket-group-map-grid',
    'dataProvider'=>$userModels->search(),
    'itemsCssClass'=>'table table-striped',
    'columns'=>array(
        array (
            'name'=>'id',
            'headerHtmlOptions'=>array (
                'width'=>'20px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->id',
        ),
        array(
            'name' => 'city_id',
            'value'=>'Dict::item("city",$data->city_id)',
        ),
        array(
            'name' => 'user',
            'value' => '$data->user',
        ),
        array(
            'name'=> 'is_admin',
            'headerHtmlOptions'=>array (
                'nowrap'=>'nowrap'
            ),
            'value'=> '$data->is_admin?"是":"否"',
        ),
        array(
            'header' => '部门',
            'value' => array($this,'getGroup'),
        ),
        array(
            'header' => '负责分类',
            'type'=>'raw',
            'value' => array($this,'getCategory'),
        ),
        'create_time',
        array(
            'header'=>'操作',
            'type'=>'raw',
            'value' => array($this,'getOperations')
        ),
    ),
)); ?>
<script type="text/javascript">
    //设为管理员
    function set_admin(username){
        if(!confirm("您确认此操作？")){
            return false;
        }
        var url = '<?php echo Yii::app()->createUrl("/crm/setAdmin");?>';
        $.post(url,{
            username:username
        },function(data){
            //alert(data.msg);
            window.location.reload();
        },'json');
    }
    //删除用户
    function del_ticket_user(userid){
        if(!confirm("您确认删除此用户？")){
            return false;
        }
        var url = '<?php echo Yii::app()->createUrl("/crm/delTicketUser");?>';
        $.post(url,{
            userid:userid
        },function(data){
            //alert(data.msg);
            window.location.reload();
        },'json');
    }
</script>
