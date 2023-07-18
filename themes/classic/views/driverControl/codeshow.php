<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-9-13
 * Time: 上午11:16
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '城市管理';
?>

<div class="container">
    <h1>城市管理 </h1>
	<div class="row-fluid" style="margin-top: 20px; margin-bottom: 20px;">
        <div class="span2">
        </div>
        <div class="span8">
        </div>
        <div class="span2">
            <a href="javascript:void(0)" class="btn btn-success" onclick="delCached()">清除缓存</a>
        </div>
    </div>
    <?php if (is_array($data) && count($data)) { ?>
        <table class="table table-striped table-bordered">
            <tr>
                <th>名称</th>
                <th>前缀</th>
                <th>bonus_code</th>
            </tr>
            <?php foreach ($data as $v) {?>
                <?php if ($v['id']) { ?>
                <tr>
                    <td><?php echo CHtml::encode($v['name']);?></td>
                    <td><?php echo CHtml::encode($v['prefix']);?></td>
                    <td><?php echo $v['bonus_code'];?></td>
                </tr>
                <?php } ?>
            <?php } ?>
        </table>
    <?php } ?>
</div>
<SCRIPT type="text/javascript">
<!--
	function delCached() {
        jQuery.get(
            '<?php echo Yii::app()->createUrl('driverControl/cityAjax');?>',
            {
                'act' : 'del_cached'
            },
            function(d) {
                if (d) {
                    alert('成功');
                    window.location.reload();
                } else {
                    alert('失败');
                }
            } ,
            'json'
        );
    }
//-->
</SCRIPT>