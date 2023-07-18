<?php if(in_array(26, Yii::app()->user->roles)){?>
<li class="nav-header">公告</li>
<?php }?>
<?php if(AdminActions::model()->havepermission('notice', 'index')){?>
<li <?php if($route=='notice/index' && @$params['category']==0) echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/notice/index',array('category'=>0));?>"><i class="icon-home"></i>近期公告</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('notice', 'index')){?>
<li <?php if($route=='notice/index' && @$params['category']==1) echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/notice/index',array('category'=>1));?>"><i class="icon-book"></i>培训教程</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('notice', 'create')){?>
<li <?php if($route=='notice/create') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/notice/create');?>"><i class="icon-book"></i>发布公告</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('notice', 'admin')){?>
<li <?php if($route=='notice/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/notice/admin');?>"><i class="icon-book"></i>公告管理</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('notice', 'materialAdmin')){?>
<li <?php if($route=='notice/materialAdmin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/notice/materialAdmin');?>"><i class="icon-book"></i>资料管理</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('notice', 'materialCreate')){?>
<li <?php if($route=='notice/materialCreate') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/notice/materialCreate');?>"><i class="icon-book"></i>发布资料</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('notice', 'ranking')){?>
<li <?php if($route=='notice/ranking') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/notice/ranking');?>"><i class="icon-book"></i>排行榜</a></li>
<?php }?>

<?php if(in_array(57, Yii::app()->user->roles)){?>
<li class="nav-header">运营数据</li>
<?php }?>
<?php if(AdminActions::model()->havepermission('report', 'ordertrends')){?>
<li <?php if($route=='report/ordertrends') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/ordertrends');?>"><i class="icon-time"></i>订单趋势</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('report', 'trend')){?>
<li <?php if($route=='report/trend') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/trend');?>"><i class="icon-list"></i>每日订单统计</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('report', 'weekly')){?>
<li <?php if($route=='report/weekly') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/weekly');?>"><i class="icon-list"></i>订单周报统计</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('report', 'monthly')){?>
<li <?php if($route=='report/monthly') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/monthly');?>"><i class="icon-list"></i>订单月报统计</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('report', 'areadistribute')){?>
<li <?php if($route=='report/areadistribute') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/areadistribute');?>"><i class="icon-list"></i>订单分布统计</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('report', 'freshrepeat')){?>
<li <?php if($route=='report/freshrepeat') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/freshrepeat');?>"><i class="icon-list"></i>新老客统计</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('report', 'vipfit')){?>
<li <?php if($route=='report/vipfit') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/vipfit');?>"><i class="icon-list"></i>vip及散客统计</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('stat', 'customerltv')){?>
<li <?php if($route=='stat/customerltv') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/stat/customerltv');?>"><i class="icon-list"></i>用户付费统计</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('report', 'ranking')){?>
<li <?php if($route=='report/ranking') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/ranking');?>"><i class="icon-list"></i>司机排行榜</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('stat', 'activecustomer')){?>
<li <?php if($route=='stat/activecustomer') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/stat/activecustomer');?>"><i class="icon-time"></i>每日用户状况趋势</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('report', 'online')){?>
<li <?php if($route=='report/online') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/online');?>"><i class="icon-random"></i>司机在线</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'map')){?>
<li <?php if($route=='driver/map') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/map');?>" target="_blank"><i class="icon-random"></i>司机分布地图</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('holiday', 'admin')){?>
<li <?php if($route=='holiday/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/holiday/admin');?>" target="_blank"><i class="icon-random"></i>节假日管理</a></li>
<?php }?>

<?php if(in_array(108, Yii::app()->user->roles)){?>
<li class="nav-header">呼叫中心</li>
<?php }?>

<?php if(AdminActions::model()->havepermission('report', 'call')){?>
    <li <?php if($route=='report/call') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/call');?>"><i class="icon-headphones"></i>接单统计</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('client', 'callphone')){?>
<li <?php if($route=='client/callphone') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/client/callphone');?>" target="_target"><i class="icon-headphones"></i>话务中心</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('adminuser', 'agent')){?>
<li <?php if($route=='adminuser/agent') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/adminuser/agent');?>"><i class="icon-headphones"></i>坐席分配</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('order', 'admin')){?>
<li <?php if($route=='order/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/order/admin');?>"><i class="icon-list"></i>订单</a></li>
<?php }?>
<?php if(in_array(3, Yii::app()->user->roles)){?>
<li class="nav-header">客户关系</li>
<?php }?>
<?php if(AdminActions::model()->havepermission('vip', 'admin')){?>
<li <?php if($route=='vip/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/vip/admin');?>"><i class="icon-user"></i>VIP用户管理</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('vip', 'create')){?>
<li <?php if($route=='vip/create') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/vip/create');?>"><i class="icon-list"></i>新开VIP</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('bonusType', 'bindlistsearch')){?>
<li <?php if($route=='bonusType/bindlistsearch') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/bindlistsearch');?>"><i class="icon-list"></i>优惠劵查询</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('vip', 'vipsearch')){?>
<li <?php if($route=='vip/vipsearch') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/vip/vipsearch');?>"><i class="icon-list"></i>vip查询</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('vip', 'active')){?>
<li <?php if($route=='vip/active') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/vip/active');?>"><i class="icon-pencil"></i>充值卡激活</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('vip', 'cardlist')){?>
<li <?php if($route=='vip/cardlist') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/vip/cardlist');?>"><i class="icon-pencil"></i>充值卡列表</a></li>
<?php }?>

<?php if(in_array(21, Yii::app()->user->roles)){?>
<li class="nav-header">品质监控</li>
<?php }?>
<!-- 
<?php //if(AdminActions::model()->havepermission('comments', 'admin')){?>
<li <?php //if($route=='comments/admin') echo 'class="active"'?>><a href="<?php //echo Yii::app()->createUrl('/comments/admin');?>"><i class="icon-list"></i>客户评价</a></li>
<?php //}?>
 -->
<?php if(AdminActions::model()->havepermission('commentSms', 'admin')){?>
<li <?php if($route=='commentSms/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/commentSms/admin');?>"><i class="icon-list"></i>客户评价</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('client', 'callhistory')){?>
<li <?php if($route=='client/callhistory') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/client/callhistory');?>"><i class="icon-list"></i>司机通话历史</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('client', 'feedback')){?>
<li <?php if($route=='client/feedback') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/client/feedback');?>"><i class="icon-list"></i>App意见反馈</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('customer', 'admin')){?>
<li <?php if($route=='customer/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/customer/admin');?>"><i class="icon-list"></i>问卷调查</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('question', 'index')){?>
<li <?php if($route=='question/index') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/question/index');?>"><i class="icon-list"></i>题卷管理</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('drivercomplaint', 'admin')){?>
<li <?php if($route=='drivercomplaint/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driverComplaint/admin');?>"><i class="icon-list"></i>司机投诉</a></li>
<?php }?>
<?php if(in_array(101, Yii::app()->user->roles)){?>
<li class="nav-header">品质监控短信记录</li>
<?php }?>
<?php if(AdminActions::model()->havepermission('smscontent', 'admin')){?>
<li <?php if($route=='smscontent/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/smsContent/admin');?>"><i class="icon-list"></i>短信列表</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('driver', 'position')){?>
<li <?php if($route=='driver/position') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/position');?>" target="_blank"><i class="icon-random"></i>司机历史轨迹地图</a></li>
<?php }?>

<?php if( in_array(7, Yii::app()->user->roles) ){?>
<li class="nav-header">客户管理</li>
<?php }?>

<?php if(AdminActions::model()->havepermission('CustomerMain', 'index')){?>
<li <?php if($route=='CustomerMain/index') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/CustomerMain/index');?>"><i class="icon-user"></i>客户管理</a></li>
<?php }?>

<?php if( in_array(7, Yii::app()->user->roles) ){?>
<li class="nav-header">司机管理</li>
<?php }?>

<?php if(AdminActions::model()->havepermission('recruitment', 'admin')){?>
<li <?php if($route=='recruitment/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/recruitment/admin');?>"><i class="icon-user"></i>司机报名管理</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('recruitment', 'recycle')){?>
<li <?php if($route=='recruitment/recycle') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/recruitment/recycle');?>"><i class="icon-user"></i>司机回收站</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('recruitment', 'editspec')){?>
<li <?php if($route=='recruitment/editspec') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/recruitment/editspec');?>"><i class="icon-user"></i>服务规范编辑</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('driver', 'settle')){?>
<li <?php if($route=='driver/settle') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/settle');?>"><i class="icon-list"></i>司机对单</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'admin')){?>
<li <?php if($route=='driver/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/admin');?>"><i class="icon-user"></i>司机资料</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driverPhone', 'admin')){?>
<li <?php if($route=='driverPhone/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driverPhone/admin');?>"><i class="icon-book"></i>司机Andriod手机替换</a></li>
<?php }?>
<!-- <?php //if(AdminActions::model()->havepermission('driver', 'create')){?>
<li <?php //if($route=='driver/create') echo 'class="active"'?>><a href="<?php //echo Yii::app()->createUrl('/driver/create');?>"><i class="icon-book"></i>司机签约</a></li> -->
<?php //}?>
<?php //if(AdminActions::model()->havepermission('recruitment', 'driverbatchadmin')){?>
<!-- <li <?php //if($route=='recruitment/driverbatchadmin') echo 'class="active"'?>><a href="<?php //echo Yii::app()->createUrl('/recruitment/driverbatchadmin');?>"><i class="icon-user"></i>司机招聘</a></li> -->
<?php //}?>
<?php if(AdminActions::model()->havepermission('driver', 'imei')){?>
<li<?php if($route=='driver/imei') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/imei');?>"><i class="icon-info-sign"></i>可用imei列表</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'work')){?>
<li<?php if($route=='driver/work') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/work');?>"><i class="icon-info-sign"></i>出勤记录</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driverRecommand', 'admin')){?>
<li <?php if($route=='driverRecommand/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driverRecommand/admin');?>"><i class="icon-user"></i>司机皇冠管理</a></li>
<?php }?>

<?php if(in_array(45, Yii::app()->user->roles)){?>
<li class="nav-header">合作渠道／优惠码</li>
<?php }?>

<?php if(AdminActions::model()->havepermission('bonustype', 'admin')){?>
<li <?php if($route=='bonusType/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/admin');?>"><i class="icon-list"></i>优惠码管理</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('bonustype', 'create')){?>
<li <?php if($route=='bonusType/create') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/create');?>"><i class="icon-list"></i>新增优惠码</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('Bonustype', 'stat')){?>
<li <?php if($route=='bonusType/stat') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/stat');?>"><i class="icon-list"></i>优惠码使用情况</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('Bonustype', 'bindlist')){?>
<li <?php if($route=='bonusType/bindlist') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/bindlist');?>"><i class="icon-list"></i>优惠码绑定列表</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('Bonustype', 'multiorderlist')){?>
<li <?php if($route=='bonusType/multiorderlist') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/multiorderlist');?>"><i class="icon-list"></i>优惠码统计报表</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('Bonustype', 'citybonusrank')){?>
<li <?php if($route=='bonusType/driverbonusrank') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/citybonusrank');?>"><i class="icon-list"></i>司机联盟-发卡城市排行/a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('Bonustype', 'bounsdetail')){?>
<li <?php if($route=='bonusType/driverbonusrank') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/bounsdetail');?>"><i class="icon-list"></i>司机联盟-发卡赚钱明细</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('Bonustype', 'driverbonusall')){?>
<li <?php if($route=='bonusType/driverbonusall') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/bonusType/driverbonusall');?>"><i class="icon-list"></i>司机联盟-发卡赚钱汇总</a></li>
<?php }?>

<?php if(in_array(6, Yii::app()->user->roles)){?>
<li class="nav-header">财务管理</li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'driverBank')){?>
<li <?php if($route=='driver/driverBank') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/driverBank');?>"><i class="icon-list"></i>财务扣款管理</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('account', 'accountHistoryTotel')){?>
<li <?php if($route=='account/accountHistoryTotel') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/account/accountHistoryTotel');?>" target="_blank"><i class="icon-list"></i>公司台账汇总</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'account')){?>
<li <?php if($route=='driver/account') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/account');?>"><i class="icon-list"></i>司机日记账流水</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'accounthistory')){?>
<li <?php if($route=='driver/accounthistory') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/accounthistory');?>"><i class="icon-list"></i>司机日记账流水历史</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'accountlist')){?>
<li><a href="#"><i class="icon-list"></i>帐单</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'accountsum')){?>
<li <?php if($route=='driver/accountsum') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/accountsum');?>"><i class="icon-list"></i>司机台账</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'accountsumhistory')){?>
<li <?php if($route=='driver/accountsumhistory') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/accountsumhistory');?>"><i class="icon-list"></i>司机台账历史</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('account', 'recharge')){?>
<li <?php if($route=='account/recharge') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/account/recharge');?>"><i class="icon-list"></i>充值记录</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('recruitment', 'fees')){?>
<li<?php if($route=='recruitment/fees') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/recruitment/fees');?>"><i class="icon-info-sign"></i>财务收款确认</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('account', 'rechargehistory')){?>
<li <?php if($route=='account/rechargehistory') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/account/rechargehistory');?>"><i class="icon-list"></i>历史充值记录</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'fee')){?>
<li <?php if($route=='driver/fee') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/fee');?>"><i class="icon-download-alt"></i>充值</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('invoice', 'admin')){?>
<li <?php if($route=='invoice/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/invoice/admin');?>"><i class="icon-envelope"></i>发票</a></li>
<?php }?>

<?php if(in_array(56, Yii::app()->user->roles)){?>
<li class="nav-header">员工管理</li>
<?php }?>
<?php if(AdminActions::model()->havepermission('adminuser', 'admin')){?>
<li <?php if($route=='adminuser/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/adminuser/admin');?>"><i class="icon-book"></i>用户管理</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('adminuser', 'create')){?>
<li <?php if($route=='adminuser/create') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/adminuser/create');?>"><i class="icon-book"></i>新建用户</a></li>
<?php }?>
<?php if(in_array(41, Yii::app()->user->roles)){?>
<li class="nav-header">系统设置</li>
<?php }?>
<?php if(AdminActions::model()->havepermission('adminuser', 'define')){?>
<li <?php if($route=='adminuser/define') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/adminuser/define');?>"><i class="icon-bullhorn"></i>权限配置</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('adminuser', 'grouplist')){?>
<li <?php if($route=='adminuser/grouplist') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/adminuser/grouplist');?>"><i class="icon-bullhorn"></i>角色组管理</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('message', 'admin')){?>
<li <?php if($route=='message/admin') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/message/admin');?>"><i class="icon-list"></i>短信设置</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('report', 'sms')){?>
<li <?php if($route=='report/sms') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/report/sms');?>"><i class="icon-list"></i>短息发送列表</a></li>
<?php }?>
<?php if(AdminActions::model()->havepermission('driver', 'uploadSms')){?>
<li <?php if($route=='driver/uploadSms') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/driver/uploadSms');?>"><i class="icon-list"></i>短信上报信息管理</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('sms', 'getuimsg')){?>
<li <?php if($route=='sms/getuimsg') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/sms/getuimsg');?>"><i class="icon-list"></i>消息推送管理</a></li>
<?php }?>

<li class="divider"></li>
<?php if(AdminActions::model()->havepermission('profile', 'info')){?>
<li <?php if($route=='profile/info') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/profile/info');?>"><i class="icon-user"></i>个人信息</a></li>
<?php }?>
<li <?php if($route=='profile/changepasswd') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/profile/changepasswd');?>"><i class="icon-wrench"></i>修改密码</a></li>

<?php if(AdminActions::model()->havepermission('apilog', 'index')){?>
<li <?php if($route=='apilog/index') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/apiLog/index');?>"><i class="icon-user"></i>错误日志</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('customer', 'blacklist')){?>
<li <?php if($route=='customer/blacklist') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/customer/blacklist');?>"><i class="icon-user"></i>黑名单管理</a></li>
<?php }?>

<?php if(AdminActions::model()->havepermission('adminuser', 'allmodsmap')){?>
<li <?php if($route=='adminuser/allmodsmap') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/adminuser/allmodsmap');?>"><i class="icon-user"></i>系统功能地图</a></li>
<?php }?>

