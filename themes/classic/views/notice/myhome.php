<?php
$this->pageTitle = Yii::app()->name . ' - 司管操作控制台';
?>

<div class="page-header">
	<h1>司管操作控制台</h1>
</div>
<div class="container">
    <div class="row">
        <div class="span1">&nbsp;</div>
        <div class="span5 thumbnail">
            <h3 class="text-left" style="padding-left: 10px">本月招聘签约</h3>
            <div class="row-fluid">
                <div class="span5" style="padding-left: 10px">还有<strong class="text-info"><?php echo intval($interview_info);?></strong>个可用面试时间</div>
                <div class="span6"><a class="btn btn-primary" href="<?php echo $this->CreateUrl('recruitment/interviewSetting');?> " target="_blank" >面试设置</a></div>
            </div>
            <div class="row-fluid">
                <div class="span5" style="padding-left: 10px" >已通知本月面试 <strong class="text-info"><?php echo $inform_row?></strong>人</div>
                <div class="span7"><a class="btn btn-primary" href="<?php echo $this->CreateUrl('recruitment/interview')?>" target="_blank">面&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;试</a>&nbsp;<a class="btn btn-primary" href="<?php echo $this->CreateUrl('recruitment/roadSetting');?> " target="_blank" >路考考官设置</a></div>
            </div>
            <div class="row-fluid">
                <div class="span5" style="padding-left: 10px">本月可签约<strong class="text-info"> <?php echo $entrant_row?></strong>人</div>
                <div class="span6"><a class="btn btn-primary" href="<?php echo $this->CreateUrl('recruitment/driverfastentry')?>" target="_blank">办理签约</a></div>
            </div>
            <div class="row-fluid">
                <div class="span5" style="padding-left: 10px">可用工号还有 <strong class="text-info"><?php echo $driver_id_count?></strong>人</div>
                <div class="span6"><a class="btn btn-primary" href="<?php echo $this->CreateUrl('driver/address')?>" target="_blank">生成工号</a></div>
            </div>
            <div class="row-fluid">
                <div class="span11" style="padding-left: 10px">
                本月已路考通过但未签约 <strong class="text-error" ><?php echo $road_row?></strong>人,已签约 <strong class="text-info"><?php echo $entry_row?></strong>人
                </div>
            </div>
        </div>
        <div class="span5 thumbnail">
            <h3 class="text-left" style="padding-left: 10px" >本月处理司机</h3>
            <div class="row-fluid">
                <div class="span5" style="padding-left: 10px" >待处理司机<strong class="text-info"> <?php echo $untreated_row?></strong>人</div>
                <div class="span5"><a class="btn btn-primary" href="<?php echo $this->CreateUrl('complain/driver', array('city_id'=>$city_id, 'search'=>1))?>" target="_blank">处理</a></div>
            </div>
            <div class="row-fluid">
                <div class="span5" style="padding-left: 10px">本日需办理激活司机<strong class="text-info"><?php echo $activation_row?></strong>人</div>
                <div class="span5"><a class="btn btn-primary" href="<?php echo $this->CreateUrl('complain/active', array('city_id'=>$city_id))?>" target="_blank">激活</a></div>
            </div>
            <div class="row-fluid">&nbsp;</div>
            <div class="row-fluid">&nbsp;</div>
            <div class="row-fluid">&nbsp;</div>
            <div class="row-fluid">&nbsp;</div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery('.row-fluid').css('margin-top', '10px');
    });
</script>

