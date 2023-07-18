<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-30
 * Time: 下午12:23
 * To change this template use File | Settings | File Templates.
 */
$background_name = $type == CompanyKpiCommon::BACKGROUND_OPERATE ? '运营' : '市场';
$this->pageTitle = '分公司绩效考核('.$background_name.')';
?>
<div class="container">

    <h1>分公司绩效考核</h1>

    <div class="row-fluid">
        <form action="<?php echo Yii::app()->createUrl('companyKpi/index');?>" method="post">
            <?php $city[0] = '全部'; ksort($city);?>
            <div class="span2">城市选择 <?php echo CHtml::dropDownList('city_id', $city_id, $city, array('style'=>'width:110px;'));?></div>
            <div class="span2">考核月份 <?php echo CHtml::dropDownList('use_date', $use_date, $use_date_list, array('style'=>'width:110px;'));?></div>
            <div class="span2"><input  class="btn" type="submit" value="查询" /></div>
        </form>
    </div>

    <div id="content" style="margin-top: 20px;">
        <?php if(is_array($data) && count($data)) { ?>
            <?php foreach($data as $city_id=>$info) { ?>
                <div id="city_content">
                    <div>
            <span class="label label-info" lable="city">
                <h5><?php echo Dict::item("city", $city_id);?>&nbsp;&nbsp;<?php echo $use_date;?></h5>
            </span>
            <span>
                服务品质项得分为<?php echo $total_score[$city_id]['service']; ?>分，
                运营业绩项得分为<?php echo $total_score[$city_id]['operate'];?>分，
                <?php if ($type == CompanyKpiCommon::BACKGROUND_BUSINESS) {?>
                市场推广项得分为<?php $total_score[$city_id]['business'] = isset($total_score[$city_id]['business']) ? $total_score[$city_id]['business'] : 0; echo $total_score[$city_id]['business'];?>分，
                <?php }  ?>
                <?php
                if ($type == CompanyKpiCommon::BACKGROUND_BUSINESS) {
                    echo date('Ym', time()) > $use_date ? '总分：'.($total_score[$city_id]['service']+$total_score[$city_id]['operate']+$total_score[$city_id]['business']) : '请继续努力';
                } else {
                    echo date('Ym', time()) > $use_date ? '总分：'.($total_score[$city_id]['service']+$total_score[$city_id]['operate']) : '请继续努力';
                }
                ?>
            </span>
                    </div>
                    <div class="mini-layout fluid" style="height: <?php echo $type==CompanyKpiCommon::BACKGROUND_OPERATE ? '270px' : '510px'; ?>">
                        <div class="row-fluid">
                            <div class="span8">
                                <p><strong>服务品质</strong></p>
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <td>品质分类</td>
                                        <td>挑战值<small>(万分之)</small></td>
                                        <td>目标值<small>(万分之)</small></td>
                                        <td>合格值<small>(万分之)</small></td>
                                        <td>实际完成<small>(万分之)</small></td>
                                        <td>完成得分</td>
                                    </tr>
                                    </thead>
                                    <tbody label="service_container">
                                    <?php foreach($info['service'] as $v) {?>
                                        <tr>
                                            <td><?php echo $v['name'];?></td>
                                            <td><?php echo $v['chanllenge'];?></td>
                                            <td><?php echo $v['goal'];?></td>
                                            <td><?php echo $v['standard'];?></td>
                                            <td><?php echo $v['complete']?></td>
                                            <td>
                                                <?php
                                                if ($service_score_flag[$city_id]) {
                                                    echo $v['score'];
                                                } else {
                                                    echo $v['score'].'('.$v['real_score'].')';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="span4">
                                <p><strong>运营业绩</strong></p>
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <td>运营业绩</td>
                                        <td>目标数值</td>
                                        <td>实际完成</td>
                                        <td>完成得分</td>
                                    </tr>
                                    </thead>
                                    <tbody lable="operate_container">
                                    <?php foreach($info['operate'] as $v) {?>
                                        <tr>
                                            <td><?php echo $v['name'];?></td>
                                            <td><?php echo isset($v['grade']) ? $v['grade'] : 0;?></td>
                                            <td><?php echo isset($v['complete']) ? $v['complete']:0;?></td>
                                            <td><?php echo isset($v['score']) ? $v['score'] : 0; ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if ($type == CompanyKpiCommon::BACKGROUND_BUSINESS) {?>
                            <div class="row-fluid">
                                <div class="span8">
                                    <p><strong>市场推广</strong></p>
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <td>品质分类</td>
                                            <td>挑战值<small>(个)</small></td>
                                            <td>目标值<small>(个)</small></td>
                                            <td>合格值<small>(个)</small></td>
                                            <td>实际完成<small>(个)</small></td>
                                            <td>完成得分</td>
                                        </tr>
                                        </thead>
                                        <tbody label="service_container">
                                        <?php foreach($info['business'] as $v) {?>
                                            <tr>
                                                <td><?php echo $v['name'];?></td>
                                                <td><?php echo isset($v['chanllenge']) ? $v['chanllenge'] : 0; ?></td>
                                                <td><?php echo isset($v['goal']) ? $v['goal'] : 0;?></td>
                                                <td><?php echo isset($v['standard']) ? $v['standard'] : 0;?></td>
                                                <td><?php echo isset($v['complete']) ? $v['complete'] : 0;?></td>
                                                <td>
                                                    <?php
                                                        echo isset($v['score']) ? $v['score'] : 0;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="span4"></div>
                            </div>
                        <?php } ?>
                    </div>

                </div>
            <?php } ?>
        <?php } else {
            echo '没有数据';
        } ?>
    </div>
</div>
