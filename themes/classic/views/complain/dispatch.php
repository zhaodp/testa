<?php
$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->registerScriptFile(SP_URL_STO.'jquery.confirm/jquery.confirm.js',CClientScript::POS_END);
$cs->registerCssFile(SP_URL_STO.'jquery.confirm/jquery.confirm.css');
?>
<?php
$this->pageTitle = Yii::app()->name . ' - 投诉派工设置';
?>
    <h1>投诉派工设置</h1>

<?php echo CHtml::textField('Text','',array('id'=>'group_name','width'=>100,'maxlength'=>100,'style'=>'margin-top:10px;')); ?>&nbsp;&nbsp;
<a url="<?php echo  Yii::app()->createUrl('complain/groupadd'); ?>" mewidth="600px" data-target="" data-toggle="modal" id="create_group" class="btn btn-primary">创建投诉任务组</a>
<div id="group-grid" class="grid-view">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>投诉任务组</th>
                <th>投诉任务人</th>
                <th>角色</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($groups as $k=>$group) {

            ?>
                <tr>
                    <td><?php echo $group['name']; ?><?php if($group['default']==1){echo '（默认）';} ?></td>
                    <td></td>
                    <td></td>
                    <td><a mewidth="600px" url="<?php echo Yii::app()->createUrl('complain/groupuseradd',array('gid'=>$group['id'])); ?>" data-toggle="modal" data-target="">添加投诉任务人</a> <?php if ($group['default']==2) {?><a mewidth="600px" url="<?php echo Yii::app()->createUrl('complain/groupadd',array('gid'=>$group['id'],'gname'=>$group['name'])); ?>" data-toggle="modal" data-target="">编辑</a> <a href="javascript:;" url="<?php echo Yii::app()->createUrl('complain/groupdel',array('gid'=>$group['id'])); ?>" data-toggle="group_del" id="group_del">删除</a><?php } ?></td>
                </tr>
                <?php foreach ($group['user'] as $k1=>$user) { ?>
                    <tr>
                        <td></td>
                        <td><?php echo $user['uname']; ?></td>
                        <td><?php echo $user['role']==1?'组长':'组员'; ?></td>
                        <td><a mewidth="600px" url="<?php echo Yii::app()->createUrl('complain/groupuseradd',array('gid'=>$group['id'],'uid'=>$user['uid'])); ?>" data-toggle="modal" data-target="">编辑</a> <a href="javascript:;" url="<?php echo Yii::app()->createUrl('complain/groupuserdel',array('gid'=>$group['id'],'uid'=>$user['uid'])); ?>" data-toggle="groupuser_del" id="groupuser_del">删除</a></td>
                    </tr>
                <?php } ?>
            <?php }?>
        </tbody>
    </table>
</div>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-body" id="modal-body">

    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
    </div>
</div>
<!-- Modal -->
<script type="text/javascript">
    $(function(){
        $("a[data-toggle=modal]").click(function(){
            var target = $(this).attr('data-target');
            var url = $(this).attr('url');
            if (url.indexOf('gname')<0) {
                url += '&gname='+$('#group_name').val();
            }

            var mewidth = $(this).attr('mewidth');
            if (mewidth == null) {
                mewidth='850px';
            }
            if (url != null) {
                $('#myModal').modal('toggle').css({'width':mewidth,'margin-left': function () {return -($(this).width() / 2);}});
                $('#myModal').modal('show');
                $('#modal-body').load(url);
            }
            return true;
        });
        //删除任务组
        $('a[data-toggle=group_del]').click(function(){
            var url = $(this).attr('url');
            $.confirm({
                'title'         : '删除任务组',
                'message'       : '确定删除此任务组吗?',
                'buttons'       : {
                    '是'        : {
                        'class' : 'blue',
                        'action': function(){
                            $.get(url,function(data){
                                if (data.succ==1) {
                                    window.location.reload();
                                } else {
                                    alert('删除任务组失败');
                                }
                            },'json');
                        }
                    },
                    '否'        : {
                        'class' : 'gray',
                        'action': function(){

                        }       // Nothing to do in this case. You can as well omit the action property.
                    }
                }
            });
        });
        //删除任务人
        $('a[data-toggle=groupuser_del]').click(function(){
            var url = $(this).attr('url');
            $.confirm({
                'title'         : '删除任务人',
                'message'       : '确定删除此任务人吗?',
                'buttons'       : {
                    '是'        : {
                        'class' : 'blue',
                        'action': function(){
                            $.get(url,function(data){
                                if (data.succ==1) {
                                    window.location.reload();
                                } else {
                                    alert('删除任务人失败');
                                }
                            },'json');
                        }
                    },
                    '否'        : {
                        'class' : 'gray',
                        'action': function(){

                        }       // Nothing to do in this case. You can as well omit the action property.
                    }
                }
            });
        });
    });
</script>