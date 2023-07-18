<style>
    body {
        padding-top: 50px;
    }
</style>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="nav-collapse collapse">
                <ul class="nav">

                    <li class="" style="margin-right: 20px;">
                        <?php
                        $partner = Yii::app()->partner->info;
                        $channel_id = $partner['channel_id'];
                        $model = Partner::model()->find('channel_id=:channel_id', array(':channel_id'=>$channel_id));
                        if ($model) {
                            $img_url = isset($model->logo) && $model->logo ? $model->logo : 'http://www.edaijia.cn/v2/sto/classic/i/logo.gif';
                        }
                        ?>
                        <a style="padding:0px" href="/v2">
                            <img style="padding:0px;height:40px;margin: -2px 0px;" src="<?php echo $img_url;?>">
                        </a>
                    </li>

                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0)">运营<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo Yii::app()->createUrl('business/partnerOrder/index');?>">订单</a></li>
                            <!--
                            <li><a href="<?php echo Yii::app()->createUrl('business/partnerOrder/bill');?>">账单</a></li>
                            -->
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0)">呼叫中心<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo Yii::app()->createUrl('business/default/dispatch');?>">派单</a></li>
                            <li><a href="<?php echo Yii::app()->createUrl('business/partnerOrder/queueList');?>" >已下订单</a></li>
                        </ul>
                    </li>

                </ul>

                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0)"><?php echo Yii::app()->partner->name;?><b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo Yii::app()->createUrl('business/default/logout');?>">退出</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /navbar-inner -->
</div>
