<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-16
 * Time: 下午12:55
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

<div class="navbar">
    <div class="navbar-inner");">
        <a class="brand" href="#">司机运营信息</a>
    </div>
</div>

<h5>上线情况</h5>

<table class="table table-bordered">
    <tr>
        <td class="tit" style="width : 15%"><strong>签约天数</strong></td>
        <td style="width : 10%"><?php echo $data['entry_time'];?></td>

        <td class="tit" style="width : 15%"><strong>上线天数</strong></td>
        <td style="width : 10%"><?php echo $data['online_days'];?></td>

        <td class="tit" style="width : 15%"><strong>正常天数</strong> <a href="javascript:void(0)" title="司机状态正常的天数（所有非屏蔽、非解约的天数）从13年3月1日起"><i class="icon-question-sign"></i></a></td>
        <td style="width : 10%"><?php echo $data['normal_days'];?></td>

        <td class="tit" style="width : 15%"><strong>峰值时段上线天数</strong> <a href="javascript:void(0)" title="19-23点时段中上线天数"><i class="icon-question-sign"></i></a></td>
        <td style="width : 10%"><?php echo $data['p_online'];?></td>
    </tr>

    <tr>
        <td class="tit" ><strong>上线率</strong> <a href="javascript:void(0)" title="上线天数/正常天数（自13-03-01）"><i class="icon-question-sign"></i></a></td>
        <td><?php echo $data['normal_days'] ? sprintf('%.2f%%',$data['online_days']/$data['normal_days']*100) : 0;?></td>

        <td class="tit" ></td>
        <td></td>

        <td class="tit" ></td>
        <td></td>

        <td class="tit" ></td>
        <td></td>
    </tr>
</table>

<div style="border-bottom:1px dashed #DDDDDD;"></div>

<h5>接单情况</h5>

<table class="table table-bordered">
    <tr>
        <td class="tit" style="width : 15%"><strong>接单数</strong> <a href="javascript:void(0)" title="包含报单，销单，补单，未报单"><i class="icon-question-sign"></i></a></td>
        <td style="width : 10%"><?php echo $data['all_count'];?></td>

        <td class="tit" style="width : 15%"><strong>报单数</strong></td>
        <td style="width : 10%"><?php echo $data['complate_count'];?></td>

        <td class="tit" style="width : 15%"><strong>补单数</strong></td>
        <td style="width : 10%"><?php echo $data['add_count'];?></td>

        <td class="tit" style="width : 15%"><strong>补单率</strong> <a href="javascript:void(0)" title="补单数/接单数 (接单数，包含报单，销单，补单，未报单)"><i class="icon-question-sign"></i></a></td>
        <td style="width : 10%"><?php echo $data['all_count'] ?  sprintf('%.2f%%',$data['add_count']/$data['all_count']*100) : 0; ?></td>
    </tr>
    <tr>
        <td class="tit" ><strong>销单数</strong> </td>
        <td><?php echo $data['cancel_count'];?></td>

        <td class="tit" ><strong>销单率</strong> <a href="javascript:void(0)" title="销单数/接单数（接单数，包含报单，销单，补单，未报单"><i class="icon-question-sign"></i></a></td>
        <td><?php echo $data['all_count'] ? sprintf('%.2f%%',$data['cancel_count']/$data['all_count']*100) : 0;?></td>

        <td class="tit" ><strong>接单天数</strong> <a href="javascript:void(0)" title="此司机有过接单的天数"><i class="icon-question-sign"></i></a></td>
        <td><?php echo $data['accept_days'];?></td>

        <td class="tit" ></td>
        <td></td>
    </tr>
</table>

<div style="border-bottom:1px dashed #DDDDDD;"></div>

<h5>品质情况</h5>

<table class="table table-bordered">
    <tr>
        <td class="tit" style="width : 15%"><strong>客诉次数</strong> </td>
        <td style="width : 10%"><?php echo $data['d_complain'];?></td>

        <td class="tit" style="width : 15%"><strong>投诉率</strong>  <a href="javascript:void(0)" title="投诉数/接单数 (接单数，包含报单，销单，补单，未报单)"><i class="icon-question-sign"></i></a></td>
        <td style="width : 10%"><?php echo $data['all_count'] ? sprintf('%.2f%%',$data['d_complain']/$data['all_count']*100) : 0;?></td>

        <td class="tit" style="width : 15%"><strong>被客人投诉数</strong></td>
        <td style="width : 10%"><?php echo $data['c_complain'];?></td>

        <td class="tit" style="width : 15%"><strong>被投诉率</strong> <a href="javascript:void(0)" title="被投诉数/接单数（接单数，包含报单，销单，补单，未报单)"><i class="icon-question-sign"></i></a></td>
        <td style="width : 10%"><?php echo $data['all_count'] ? sprintf('%.2f%%',$data['c_complain']/$data['all_count']*100) : 0;?></td>
    </tr>

    <tr>
        <td class="tit" ><strong>为公司带来收</strong> <a href="javascript:void(0)" title="公司通过此司机获得的收入"><i class="icon-question-sign"></i></a></td>
        <td><?php echo $data['deductions'];?></td>

        <td class="tit" ><strong>信息费充值总额</strong>  <a href="javascript:void(0)" title="司机充值的金额总额（包括VIP，奖励）"><i class="icon-question-sign"></i></a></td>
        <td><?php echo $data['recharge'];?></td>

        <td class="tit" ><strong>差评数</strong></td>
        <td><?php echo $data['low_opinion_times'];?></td>

        <td class="tit" style="width : 15%"><strong>好评数</strong></td>
        <td style="width : 10%"><?php echo $data['high_opinion_times'];?></td>
    </tr>
</table>

<script>
    jQuery(document).ready(function(){
        jQuery('strong').css('color', '#316AAF');
    });
</script>