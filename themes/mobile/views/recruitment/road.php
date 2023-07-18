<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-7-12
 * Time: 下午4:05
 * To change this template use File | Settings | File Templates.
 */
Yii::app()->clientScript->registerMetaTag('viewport','width=device-width, initial-scale=1.0');
$this->pageTitle = '司机路考';
$rank_array = array('A','B','C');
?>
<legend xmlns:b="http://www.w3.org/1999/html"><?php echo $is_examiner=='automatic' ? '自动档司机路考' : '手动档司机路考';?></legend>
<p></p>
<?php if ($action == 'admin') {?>
<form class="form-inline" action="" method="post" id="main_form">
    <div class="input-prepend">
        <span class="add-on"><?php echo $city[$driver_city_id];?></span>
        <input type="hidden" name="action" value="search" />
        <input id="serial_number" name="serial_number" type="text" placeholder="司机报名流水号">
        <input class="btn" type="submit" value="查询"/>
    </div>
</form>
<?php
} else {

    echo '<div class="alert alert-success">';
    echo '司机报名流水号：<strong>'.$driver_info['queue_number'].'</strong>  <strong>'.$driver_info['name'].'</strong>';
    echo '</div>';
    if (isset($driver_info) && $driver_info['recycle']) {
        echo '<div class="alert alert-error">该司机已经被回收,回收原因为:'.$driver_info['recycle_reason'].'</div>';
    } elseif (isset($driver_info) && $driver_info['status'] != 2) {
        echo '<div class="alert alert-error">该司机面试未通过或状态不正确</div>';
    } elseif (isset($road_info) && ($road_info['manuax'] ==1 || $road_info['manuax'] ==1)) {

        if ($road_info['automatic']==1) {
            echo '<div class="alert alert-error">该自动档考试不合格，不予参加考试！</div>';
        }
        if ($road_info['manuax']==1) {
            echo '<div class="alert alert-error">该手动档考试不合格，不予参加考试！</div>';
        }
    } elseif(in_array($road_info['manuax'], $rank_array) && $is_examiner=='manuax') {
        echo '<div class="alert alert-success">该司机已经参加手动档路考成绩为：'.$road_info['manuax'].'</div>';
    }elseif(in_array($road_info['automatic'], $rank_array) && $is_examiner=='automatic') {
        echo '<div class="alert alert-success">该司机已经参加自动档路考成绩为：'.$road_info['automatic'].'</div>';
    } else {
?>

<form action="" method="post" id="rank_form" class="form-horizontal">
    <div class="control-group">
        <ul class="nav nav-pills">
            <li><button class="btn btn-success btn-large" type="button" func="rank" rank="A" style="margin-right:20px;">A</button></li>
            <li><button class="btn btn-success btn-large" type="button" func="rank" rank="B" style="margin-right:20px;">B</button></li>
            <li><button class="btn btn-success btn-large" type="button" func="rank" rank="C" style="margin-right:20px;">C</button></li>
            <li><button class="btn btn-success btn-large" type="button" func="rank" rank="1">不合格</button></li>
        </ul>
    </div>
    <div class="control-group">
        <input type="hidden" name="<?php echo $is_examiner;?>" id="rank"/>
        <input type="hidden" name="serial_number" value="<?php echo $driver_info['serial_number'];?>" />
        <input type="hidden" name="city_id" value="<?php echo $driver_city_id; ?>" />
        <input type="hidden" name="<?php echo $is_examiner=='automatic' ? 'a_examiner' : 'm_examiner';?>" value="<?php echo $driver_id;?>" />
        <input type="hidden" name="exam_date" value="<?php echo $current_date;?>" />
        <input type="hidden" name="action" value="submit" />
        <div class="span2"><button class="btn btn-success btn-large" type="button" id="saveRecord">确认</button></div>
    </div>
    <hr>

</form>


    <?php } ?>

<?php } ?>
<?php if ($action != 'admin') {?>
    <div class="row-fluid">
        <div class="span2"><a href="<?php echo Yii::app()->createUrl('recruitment/road');?>" class="btn btn-success btn-large" >重新查询</a></div>
    </div>
<?php } ?>
<script>
    jQuery(document).ready(function(){

        jQuery('.well').hide();

        jQuery('#main_form').submit(function(){
            if (jQuery('#serial_number').val() == '') {
                alert('请输入报名流水号');
                return false;
            }
            return true;
        });

        jQuery('[func="rank"]').click(function(){
            jQuery('#rank').val(jQuery(this).attr('rank'));
            jQuery('[func="rank"]').attr('class', 'btn btn-success btn-large');
            jQuery(this).attr('class','btn btn-danger btn-large');
        });

        jQuery('#saveRecord').click(function(){
            jQuery.post(
                '<?php echo Yii::app()->createUrl('recruitment/road');?>',
                jQuery('#rank_form').serialize(),
                function(d) {
                    if(d.status) {
                        window.location = '<?php echo Yii::app()->createUrl('recruitment/road');?>';
                    }
                },
                'json'
            );

        });
    })
</script>