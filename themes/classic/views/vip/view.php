<?php
/* @var $this VipController */
/* @var $model Vip */
$this->breadcrumbs = array(
    'VipPhone' => array('index'),
    $model->name,
);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('admin-user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1>查看  <?php echo $model->id; ?> 信息</h1>

<hr/>
<h3>主卡信息</h3>

<table class="table table-striped">
    <tr>
        <th>VIP卡号</th>
        <td><?php echo $model->id; ?></td>
        <th>姓名</th>
        <td><?php echo $model->name; ?></td>
        <th>手机号</th>
        <td><?php echo $model->phone; ?></td>
    </tr>
    <tr>
        <th>VIP卡类型</th>
        <td><?php echo Dict::item('vip_type', $model->type); ?></td>
        <th>办理城市</th>
        <td><?php echo Dict::item('city', $model->city_id); ?></td>
        <th>短信接受电话</th>
        <td><?php echo $model->send_phone; ?></td>
    </tr>
    <tr>
        <th>总消费金额</th>
        <td><?php echo $model->totelamount; ?></td>
        <th>余额</th>
        <td><?php echo $model->balance; ?></td>
        <th>信誉度</th>
        <td><?php echo $model->credit; ?></td>
    </tr>
    <tr>
        <th>状态</th>
        <td><?php echo Dict::item('vip_status', $model->status); ?></td>
        <th>经手人</th>
        <td><?php echo empty($model->operator) ? '系统默认' : $model->operator; ?></td>
        <th>开卡时间</th>
        <td><?php echo date('Y-m-d', $model->created); ?></td>
    </tr>
    <tr>
        <th>邮件地址</th>
        <td><?php echo $model->email; ?></td>
        <th>账单接受方式</th>
        <td><?php echo $model->send_phone == 0 ? 'wap页面' : '短信'; ?></td>
        <th>公司名称</th>
        <td><?php echo $model->company; ?></td>
    </tr>
    <tr>
        <th>发票抬头</th>
        <td colspan='5'><?php echo $model->commercial_invoice; ?></td>
    </tr>
    <tr>
        <th>备注</th>
        <td colspan='5'><?php echo $model->remarks; ?></td>
    </tr>
</table>
<hr/>
<h3>副卡信息</h3>
<?php
$status = Dict::items('vipphone_status');
$type = array('0' => '副卡', '1' => '主卡');
?>
<table class="table">
    <tr>
        <th>序号</th>
        <th>副卡人姓名</th>
        <th>手机号码</th>
        <th>手机号类型</th>
        <th>状态</th>
        <th>发短信</th>
        <th>操作</th>
    </tr>
    <?php
    $type_value = 0;
    $orgiVipId = 0;
    if (!empty($vipPhone)) {
        $i = 0;
        foreach ($vipPhone as $value) {
            $i++;
            if($value['type']==1){
                $type_value = 1;
                $orgiVipId = $value['id'];
            }
            ?>
            <tr <?php if ($value['status'] == 2) {
                echo "style= 'background: none repeat scroll 0 0 #FF8351;'";
            } ?>>
                <td>
                    <?php echo $i; ?>
                    <?php echo CHtml::hiddenField("vips_id_".$value['id'], $value['id'], array('style' => 'width:150px;')); ?>
                </td>
                <td><?php echo CHtml::textField("vips_name_" . $value['id'], $value['name'], array('style' => 'width:150px;')); ?></td>
                <td><?php echo CHtml::textField("vips_phone_" . $value['id'], $value['phone'], array('style' => 'width:150px;')); ?></td>
                <td>
                    <?php
                    echo CHtml::dropDownList("vips_type_" . $value['id'], $value['type'], $type, array('style' => 'width:150px;'));
                    ?>
                </td>
                <td><?php
                    echo CHtml::dropDownList("vips_status_" . $value['id'], $value['status'], $status, array('style' => 'width:150px;'));
                    ?>
                </td>
                <td><?php echo CHtml::checkBox('vips_check_' . $value['id'], true); ?></td>
                <td><a href="#" id="vips_update_<?php echo $value ['id']; ?>"
                       name="vips_udpate_<?php echo $value['id']; ?>">修改</a> &nbsp;&nbsp;
                    <a href="javascript:void(0);" onclick="vip_delete('<?php echo $value['id']; ?>')" id="vips_delete"
                       name="vips_delete">删除</a>
                    <script type="text/javascript">
                        jQuery('#vips_update_<?php echo $value['id'];?>').live('click', function () {
                            var vips_typeValue = $("#vips_typeValue").val();
                            var _orgiVipId = $("#orgiVipId").val();//原来存在的vip主卡的id
                            var _vips_id = $("#vips_id_<?php echo $value['id'];?>").val();//当前选中要修改的vip主卡的id
                            var _selType = $("#vips_type_<?php echo $value['id'];?> option:selected").val();
                            if(_orgiVipId != 0 && (_orgiVipId != _vips_id) && _selType == 1){
                                //当前判断逻辑:首先记住原始列表中主卡的id，判断当前修改记录如果是主卡则判断主卡的id是否和原始列表的id是否相同
                                //_orgiVipId=0说明里面全部是副卡
                                alert("已经存在一张主卡,不能同时设置两张主卡");
                                return false;
                            }
                            var checked_<?php echo $value['id'];?> = 0;

                            if($("#vips_check_<?php echo $value['id'];?>").attr("checked")=='checked'){

                                checked_<?php echo $value['id'];?> = 1;
                            }
                            $.ajax({
                                'url': '<?php echo Yii::app()->createUrl('/vip/UpdateVipPhone');?>',
                                'data': 'id=<?php echo $value['id'];?>&vipid=<?php echo $value['vipid'];?>&name=' + $('#vips_name_<?php echo $value['id'];?>').attr('value') + '&check=' + checked_<?php echo $value['id'];?> + '&phone=' + $('#vips_phone_<?php echo $value['id'];?>').attr('value') + '&status=' + $("#vips_status_<?php echo $value['id'];?> option:selected").val() + '&type=' + $("#vips_type_<?php echo $value['id'];?> option:selected").val(),
                                'type': 'get',
                                'success': function (data) {
                                    if (data.length > 0) {
                                        if (data.length > 100) {
                                            alert("修改成功");
                                        } else {
                                            alert(data);
                                            $('#vips_phone_<?php echo $value['id'];?>').focus();
                                        }
                                    }
                                    window.location.href = "<?php echo Yii::app()->createUrl('vip/view',array('id' => $model->id)); ?>";
                                },
                                'cache': false
                            });
                            return false;
                        });

                    </script>
                </td>
            </tr>
        <?php
        }
    }

    ?>
    <input type="hidden" id="vips_typeValue" value="<?php echo $type_value?>">
    <input type="hidden" id="orgiVipId" value="<?php echo $orgiVipId?>">
    <tr>
        <td>&nbsp;</td>
        <td><?php echo CHtml::textField("vips_name_0", '', array('style' => 'width:150px;')); ?></td>
        <td><?php echo CHtml::textField("vips_phone_0", '', array('style' => 'width:150px;')); ?></td>
        <td>
            <?php
            echo CHtml::dropDownList("vips_type_0", 0, $type, array('style' => 'width:150px;'));
            ?>
        </td>
        <td><?php echo CHtml::dropDownList("vips_status_0", 1, $status, array('style' => 'width:150px;')); ?></td>

        <td><?php echo CHtml::checkBox('vips_check_0', true); ?></td>
        <td>
            <a href="#" id="vips_create" name="vips_create">添加</a>
        </td>
    </tr>
</table>
</div>
<script type="text/javascript">
    jQuery('#vips_create').live('click', function () {
        if (!confirm('确定要添加该副卡吗?')) return false;
        var vips_typeValue = $("#vips_typeValue").val();
        var _selType = $("#vips_type_0 option:selected").val();
        if(vips_typeValue > 0 && _selType == 1 ){
            //判断逻辑:如果当前列表已经存在主卡则不能再添加主卡
            alert("已经存在主卡不能再次添加主卡");
            return false;
        }
        var checked = 0;
        if($("#vips_check_0").attr("checked") == 'checked'){
            checked = 1;
        }

        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/vip/CreateVipPhone');?>',

            'data': 'id=<?php echo $model->id;?>&name=' + $('#vips_name_0').attr('value') + '&check=' + checked + '&phone=' + $('#vips_phone_0').attr('value') + '&status=' + $("#vips_status_0 option:selected").val() + '&type=' + $("#vips_type_0 option:selected").val(),
            'type': 'get',
            'success': function (data) {
                if (data != '') {
                    alert(data);
                }
                window.location.href = "<?php echo Yii::app()->createUrl('vip/view',array('id' => $model->id)); ?>";
            },
            'cache': false
        });
        return false;
    });
    function vip_delete(id) {
        if (!confirm('确定要删除该副卡吗?')) return false;
        $.ajax({
            'url': '<?php echo Yii::app()->createUrl('/vip/DeleteVipPhone');?>',
            'data': 'id=' + id,
            'type': 'get',
            'success': function (data) {
                if (data != '') {
                    alert(data);
                }
                window.location.href = "<?php echo Yii::app()->createUrl('vip/view',array('id' => $model->id)); ?>";
            }
        })
    }

</script>




