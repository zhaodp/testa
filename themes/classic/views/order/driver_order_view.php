<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-5-21
 * Time: 下午7:16
 * To change this template use File | Settings | File Templates.
 */
if ( empty($drivers)  ){
    echo "当前还没有司机接单";
}else{
?>
    <table width="700">
        <?php foreach($drivers as $driver){?>
        <tr>
            <td>司机工号:<?php echo $driver['driver_id']; ?></td>
            <td>手机:<?php echo $driver['phone']; ?></td>
            <td><?php echo CHtml::button("再次推送",array("onclick"=>"{PushAgain('".$queue_id."' , '".$driver['driver_id']."');}"));?></td>
        </tr>
       <?php }?>
    </table>

<?php
}

?>
<script type="text/javascript">
function PushAgain(queue_id , driver_id) {
	if(confirm("您确定再次给司机推送订单详情么？")){
        $.ajax({
            'url':'<?php echo Yii::app()->createUrl('/order/pushagain');?>',
            'data':'queue_id='+queue_id+'&driver_id='+driver_id,
            'type':'get',
            'success':function(data){
                if(data == 1){
					alert("推送成功");
				}else{
					alert("推送失败");
				}
            },
            'cache':false
        });
    }
}
</script>