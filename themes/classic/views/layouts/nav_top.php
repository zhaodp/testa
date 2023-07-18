<style>
    body {
        padding-top: 50px;
    }
</style>
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container" style="width: 98%;">
            <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="nav-collapse collapse">

                <ul class="nav">
		    <li class="" style="margin-left:0px;">
		    <a style="padding:0px" target="__parent" href="/">
		    <img style="padding:0px;height:40px;margin: -2px 0px;" src="<?php echo Yii::app()->request->hostInfo; ?>/sto/classic/i/logo.gif"/>
			</a>
                    </li>

                    <?php
                    if (AdminActions::model()->havepermission("system", "search")) {

			$appv2 = AdminApp::model()->findByPk(2);
			$v2url = $appv2->url;
                        ?>
                        <li style="margin-left:80px;">
                            <?php echo CHtml::Form($v2url . '/index.php?r=system/search', 'get', array('title'=>'可输入订单号、手机号、司机工号', 'style' => 'margin:10px 0 0 0;', 'target'=>'mainFrame')); ?>
                            <div class="input-append" style="margin:0;">
                                <?php echo CHtml::textField('q', CHtml::encode($this->q), array('id' => 'global_search_q','class' => 'span1','style'=>'font-size:11px;width:90px;height:18px;padding:0px;','placeholder'=>'司机/订单/客户/用户ID')); ?>
                                <button onclick="if($('#global_search_q').val().length < 1){alert('请输入搜索内容');return false;}" type="submit" class="btn" style="height:20px;padding:0px 2px 0px;"><i style="" class="icon-search"></i></button>
                            </div>
                            <?php echo CHtml::endForm(); ?>
                        </li>
                    <?php
                    }
                    ?>

                </ul>

                <?php

                if (true) {
                    $ua = Yii::app()->request->getuserAgent();
                    $user_agent = strtolower($ua);
                    $current_user = Yii::app()->user->id;
                    $chpwd_url = Yii::app()->createUrl('profile/changepasswd');
                    $logout_url = Yii::app()->createUrl('site/logout');
                    $daily_url = Yii::app()->createUrl('adminWorkLog/index');
                    $event_url= Yii::app()->createUrl('adminEvent/index');
                    $auth_url = Yii::app()->createUrl('adminuserNew/auth');
                  
                        $hello = "";
                        $hello = Helper::getHelloWord();
                        echo '<p class="navbar-text pull-right" style="color:#FF0;"><span class="visible-desktop">' . $hello . '</span></p>';
                }

                ?>

            </div>
        </div>
    </div>
    <!-- /navbar-inner -->
</div>
<script>
/*
$("#logo-home").on("click",function(){
	$("#mainFrame",parent.document.body).attr("src",href); 
    });
 */

</script>
