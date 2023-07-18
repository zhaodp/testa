<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-15
 * Time: 下午5:32
 * To change this template use File | Settings | File Templates.
 */
$this->layout = '//layouts/main_no_nav';
?>

<style>
    .tit {
        background-color: #D9EDF7;
    }

    .navbar .navbar-inner{
        background-color: #FAFAFA!important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067)!important;
        background-image: -moz-linear-gradient(top, #FFF, #F2F2F2);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#FFF), to(#F2F2F2));
        background-image: -webkit-linear-gradient(top, #FFF, #F2F2F2);
        background-image: -o-linear-gradient(top, #FFF, #F2F2F2);
        background-image: linear-gradient(to bottom, #FFF, #F2F2F2);
    }
</style>

<div class="alert alert-info">
    <strong style="margin-right: 20px;"><?php echo $data['user'];?>--<?php echo $data['name'];?></strong>
    <span style="margin-right:10px">
        <?php $driver_status = $data['status']; ?>
        状态：<?php echo $driver_status['success'] ? $driver_status['msg']['redis']['status'] : $driver_status['msg'];?>
    </span>
    <?php
    $dep = AdminDepartment::model()->getInfoByName('技术');
    if ( Yii::app()->user->department == $dep['id'] && $driver_status['success']) {?>
    <span style="margin-right:10px" >mysql状态：<?php echo $driver_status['msg']['db']['status'];?></span>
    <span style="margin-right:10px" >mongo状态：<?php echo $driver_status['msg']['mongo']['status']; ?></span>
    <?php } ?>
    <span style="margin-right:20px">工作电话：<?php echo Yii::app()->controller->showPhoneNumber($data['phone']);?></span>
    <span style="margin-right:20px">备用电话：<?php echo Yii::app()->controller->showPhoneNumber($data['ext_phone']);?></span>
    <span style="margin-right:20px">信息费余额：<span id="balance"><?php echo $data['balance'];?></span></span>
    <?php $assures = Driver::$assure_dict; ?>
    <span style="margin-right:20px">担保状态：<span id="balance"><?php echo $assures[$data['assure']];?></span></span>
    <span style="margin-right:20px"><a href="<?php echo Yii::app()->createUrl('report/cancelstat', array('driver_user'=>$data['user']));?>" target="_blank"><strong>销单监控</strong></a></span>
</div>

<div class="navbar">
    <div class="navbar-inner">
        <a class="brand" href="#">信息摘要</a>
    </div>
</div>

<table class="table table-bordered">
    <tr>
        <td rowspan="7" style="width: 20%; text-align: center"><img src="<?php echo Driver::getPictureUrl($data['user'], $data['city_id']);?>" /></td>

        <td class="tit" style="width: 20%"><strong>星级</strong></td>
        <td style="width: 20%"><?php echo $data['level']; ?></td>

        <td class="tit" style="width: 20%"><strong>工作电话</strong></td>
        <td style="width: 20%"><?php echo Yii::app()->controller->showPhoneNumber($data['phone']);?></td>
    </tr>

    <tr>
        <td class="tit" ><strong>代驾次数</strong></td>
        <td id="service_times"><?php echo $data['service_times'];?></td>

        <td class="tit" ><strong>签约时间</strong></td>
        <td><?php echo strtotime($data['created']) > 0 ? date('Y年m月d日', strtotime($data['created'])): '未知';?></td>
    </tr>

    <tr>
        <td class="tit" ><strong>签约天数</strong>  <a href="javascript:void(0)" title="签约日期起到当天日期的天数 或 签约日期起到解约日期的天数"><i class="icon-question-sign"></i></a></td>
        <td><?php echo $data['entry_time'];?></td>

        <td class="tit"><strong>上线天数</strong> <a href="javascript:void(0)" title="全部上线天数(从13-03-01)"><i class="icon-question-sign"></i></a></td>
        <td id="online_days"><?php echo $data['online_days'];?></td>
    </tr>

    <tr>
        <td class="tit"><strong>给公司带来收入</strong></td>
        <td id="deductions"><?php echo $data['deductions'];?></td>

        <td class="tit"><strong>充值金额</strong></td>
        <td id="recharge"><?php echo $data['recharge'];?></td>
    </tr>

    <tr>
        <td class="tit"><strong>奖励次数</strong></td>
        <td id="recommend"><?php echo $data['recommend'];?></td>

        <td class="tit"><strong>客诉次数</strong></td>
        <td id="punish"><?php echo $data['punish'];?></td>
    </tr>

    <tr>
        <td class="tit"><strong>屏蔽</strong></td>
        <td id="punishtime" <?php if(!Common::checkOpenScore($data['city_id'],time())) echo 'colspan="3"';?>>屏蔽天数：<?php echo $data['limit_time'];?>--解屏时间：<?php echo $data['un_punish_time'];?></td>
        <?php if(Common::checkOpenScore($data['city_id'],time())){ ?>
            <td class="tit"><strong>代驾分</strong></td>
            <td id="score"><?php echo $data['score'];?></td>
        <?php }?>
    </tr>
    <tr>
        <td class="tit"><strong>当前皇冠信息</strong></td>
        <td><?php if (isset($data['recommand']) && is_array($data['recommand'])) {
                echo $data['recommand']['begin_time'].'-'.$data['recommand']['end_time'].'<br>'.
                    $data['recommand']['reason']; }else
                echo '无';?></td>
        <td class="tit"><strong>e币</strong></td>
            <td id="score"><?php echo $data['total_wealth'];?></td>
    </tr>

</table>

<div class="navbar">
    <div class="navbar-inner" style="background-color: #FAFAFA; background-image: linear-gradient(to bottom, #FFFFFF, #F2F2F2); box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067);">
        <a class="brand" href="#">司机信息</a>
    </div>
</div>

<h5>基本信息</h5>

<table class="table table-bordered">
    <tr>
        <td class="tit" style="width: 12.5%"><strong>姓名</strong></td>
        <td style="width: 12.5%"><?php echo $data['name'];?></td>

        <td class="tit" style="width: 12.5%"><strong>工号</strong></td>
        <td style="width: 12.5%"><?php echo $data['user'];?></td>

        <td class="tit" style="width: 12.5%"><strong>星级</strong></td>
        <td style="width: 12.5%"><?php echo $data['level']; ?></td>

        <td class="tit" style="width: 12.5%"><strong>性别</strong></td>
        <td style="width:12.5%"><?php echo Dict::item('gender', $data['gender']);?></td>
    </tr>

    <tr>
        <td class="tit" style="width: 12.5%"><strong>城市</strong></td>
        <td style="width: 12.5%"><?php echo Dict::item('city', $data['city_id']);?></td>

        <td class="tit" style="width: 12.5%"><strong>区域</strong></td>
        <td style="width: 12.5%">
            <?php echo District::getDistrictsName($data['city_id'], $data['district_id']);?>
        </td>

        <td class="tit" style="width: 12.5%"><strong>年龄</strong></td>
        <td style="width: 12.5%"><?php echo $data['age'];?></td>

        <td class="tit" style="width: 12.5%"><strong>籍贯</strong></td>
        <td style="width: 12.5%"><?php echo $data['domicile'].' '.$data['register_city'];?></td>
    </tr>

</table>
<div style="border-bottom:1px dashed #DDDDDD;"></div>

<h5>二维码</h5>
<div style="width:100px;height:100px;padding-left:10px">
    <img style="width:100%;height:100%" src="<?php echo $data['two_code_pic'];?>" />
</div>

<div style="border-bottom:1px dashed #DDDDDD;"></div>

<h5>工作信息</h5>

<table class="table table-bordered">
    <tr>
        <td class="tit" style="width: 12.5%"><strong>身份证号</strong></td>
        <td style="width: 12.5%"><?php echo $data['id_card'];?></td>

        <td class="tit" style="width: 12.5%"><strong>驾驶证号</strong></td>
        <td style="width: 12.5%"><?php echo $data['car_card'];?></td>

        <td class="tit" style="width: 12.5%"><strong>驾照档案编号</strong></td>
        <td style="width: 12.5%"><?php echo CHtml::encode($data['id_driver_card']);?></td>

        <td class="tit" style="width: 12.5%"><strong>驾龄</strong></td>
        <td style="width: 12.5%"><?php echo $data['year'];?></td>
    </tr>

    <tr>
        <td class="tit" ><strong>来源渠道</strong></td>
        <td><?php echo Dict::item('recruitment_src', $data['src']);?></td>

        <td class="tit" ><strong>工作方式</strong></td>
        <td><?php echo Dict::item('work_type',$data['work_type']);?></td>

        <td class="tit" style="width:16.6%"><strong>准驾车型</strong></td>
        <td style="width:16.6%"><?php echo Dict::item('driver_type', $data['driver_type']);?></td>

        <td class="tit" ><strong>推荐人</strong></td>
        <td><?php echo $data['recommender'];?></td>
    </tr>
</table>

<div style="border-bottom:1px dashed #DDDDDD;"></div>

<h5>联系信息</h5>

<table class="table table-bordered">
    <tr>
        <td class="tit" style="width: 16.5%"><strong>工作电话</strong></td>
        <td style="width: 16.5%"><?php echo Yii::app()->controller->showPhoneNumber($data['phone']);?></td>

        <td class="tit" style="width: 16.5%"><strong>备用电话</strong></td>
        <td style="width: 16.5%"><?php echo Yii::app()->controller->showPhoneNumber($data['ext_phone']);?></td>

        <td class="tit" style="width: 16.5%"><strong>IMEI</strong></td>
        <td style="width: 16.5%"><?php echo $data['imei'];?></td>
    </tr>

    <tr>
        <td class="tit"><strong>紧急联系人姓名</strong></td>
        <td><?php echo $data['contact'];?></td>

        <td class="tit"><strong>紧急联系人关系</strong></td>
        <td><?php echo $data['contact_relate'];?></td>

        <td class="tit"><strong>紧急联系人电话</strong></td>
        <td><?php echo Yii::app()->controller->showPhoneNumber($data['contact_phone']);?></td>
    </tr>
</table>

<div style="border-bottom:1px dashed #DDDDDD;"></div>

<script>
    /*
    jQuery(document).ready(function(){
        jQuery('strong').css('color', '#316AAF');
        jQuery.get(
            '<?php echo Yii::app()->createUrl('/driver/driverAjax') ;?>',
            {
                id : '<?php echo $driver_id;?>',
                act : 'get_driver_extend'
            },
            function(d) {
                if (d.status) {
                    jQuery.each(d.msg, function(i, v){
                        jQuery('#'+i).html(v);
                    });
                }
            },
            'json'
        )
    });
    */
</script>

