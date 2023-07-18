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
                    <li class="">
                        <?php
                        $home_url = '/v2';
                        $isDriver = Yii::app()->user->type == AdminUserNew::USER_TYPE_DRIVER;
                        /*
                        if (!$isDriver) {
                            $department = Yii::app()->user->department;
                            switch ($department) {
                                case '司机管理部':
                                    $home_url = Yii::app()->createUrl('/notice/myhome'); //司管控制台
                                    break;
                                default:
                                    break;
                            }
                        }
                        */
                        ?>
                        <a href="<?php echo $home_url; ?>" style="padding:0px"><img
                                src="<?php echo SP_URL_IMG; ?>logo.gif"
                                style="padding:0px;height:40px;margin: -2px 0px;"></a>
                    </li>
                    <?php

                    if (!$isDriver) {
                        if(!isset(Yii::app()->user->user_id))
                            $this->redirect(array('site/login'));
                        $menuList = Menu::model()->getMenuArr(Yii::app()->user->user_id);
                        if ($menuList && is_array($menuList)) {
                            foreach ($menuList as $m) {
                                if (isset($m['sub'])) {
                                    echo '<li class="dropdown">';
                                    //echo '<a class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" href="#">'.$m['name'].'<b class="caret"></b></a>';
                                    echo '<a style="padding-left:8px;padding-right:8px;" class="dropdown-toggle js-activated" href="#">' . $m['name'] . '<b class="caret"></b></a>';
                                    echo '<ul class="dropdown-menu">';
                                    foreach ($m['sub'] as $s) {
                                        $class = $target = '';
                                        if (isset($route) && $route == $s['controller'] . '/' . $s['action']) {
                                            $class = 'class="active"';
                                        }
                                        if (isset($s['is_target']) && $s['is_target']) {
                                            $target = 'target="_blank"';
                                        }
                                        echo '<li ' . $class . '><a ' . $target . ' href="' . Yii::app()->createUrl('/' . $s['controller'] . '/' . $s['action']) . '"> ' . $s['name'] . '</a></li>';
                                    }
                                    echo '</ul>';
                                }
                                echo '</li>	';
                            }
                        }

                    }?>
                    <?php
                    if (AdminActions::model()->havepermission("system", "search")) {
                        ?>
                        <li style="margin-left:7px;">
                            <?php echo CHtml::Form(array('system/search'), 'get', array('title'=>'可输入订单号、手机号、司机工号', 'style' => 'margin:10px 0 0 0;', 'target'=>$this->q ? '' : '_blank')); ?>
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

                if (!$isDriver) {
                    $ua = Yii::app()->request->getuserAgent();
                    $user_agent = strtolower($ua);
                    $current_user = Yii::app()->user->id;
                    $chpwd_url = Yii::app()->createUrl('profile/changepasswd');
                    $logout_url = Yii::app()->createUrl('site/logout');
                    $daily_url = Yii::app()->createUrl('adminWorkLog/index');
                    $event_url= Yii::app()->createUrl('adminEvent/index');
                    //如果是safari浏览器，就不出问候了，误伤一批
/*                   if (false !== strpos($user_agent, 'safari')) {

                        echo <<<eof
<ul class="nav pull-right">
<li class="dropdown">
<a href="#" class="dropdown-toggle js-activated">{$current_user}<b class="caret"></b></a>
<ul class="dropdown-menu">
<li><a href="{$home_url}">我的控制台</a></li>
<li><a href="{$chpwd_url}">修改密码</a></li>
<li><a href="{$daily_url}">我的日报</a></li>
<li><a href="{$event_url}">待办事项</a></li>
<li><a href="{$logout_url}">退出</a></li>
</ul></li> 
</ul>
eof;


                    } else {
*/                    
                        $hello = "";
                        $hello = Helper::getHelloWord();
                        echo '<p class="navbar-text pull-right" style="color:#FF0;"><span class="visible-desktop">' . $hello . '</span></p>';
                        echo <<<eof
<ul class="nav pull-right">
<li class="dropdown">
<a href="#" class="dropdown-toggle js-activated">{$current_user}<b class="caret"></b></a>
<ul class="dropdown-menu">
<li><a href="{$home_url}">我的控制台</a></li>
<li><a href="{$chpwd_url}">修改密码</a></li>
<li><a href="{$daily_url}">我的日报</a></li>
<li><a href="{$event_url}">待办事项</a></li>
<li><a href="{$logout_url}">退出</a></li>
</ul></li> 
</ul>

eof;

//                   }


                }

                ?>

            </div>
        </div>
    </div>
    <!-- /navbar-inner -->
</div>
<script>
    // very simple to use!
    $(document).ready(function () {
        $('.js-activated').dropdownHover().dropdown();
    });
</script>
