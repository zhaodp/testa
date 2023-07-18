<div id="sidebar" class="sidebar">
        <div id="sidebar-shortcuts" class="sidebar-shortcuts">
<!--
            <div id="sidebar-shortcuts-large" class="sidebar-shortcuts-large">
                <button class="btn btn-small btn-success">
                    <i class="icon-signal"></i>
                </button>
                <button class="btn btn-small btn-info">
                    <i class="icon-pencil"></i>
                </button>
                <button class="btn btn-small btn-warning">
                    <i class="icon-group"></i>
                </button>
                <button class="btn btn-small btn-danger">
                    <i class="icon-cogs"></i>
                </button>
            </div>
            <div id="sidebar-shortcuts-mini" class="sidebar-shortcuts-mini">
                <span class="btn btn-success"></span>
                <span class="btn btn-info"></span>
                <span class="btn btn-warning"></span>
                <span class="btn btn-danger"></span>
            </div>
-->
        </div>
        <!--sidebar-shortcuts-->
        <ul class="nav nav-list">

			<?php
$appv2 = AdminApp::model()->findByPk(2);
$v2url = $appv2->url;
$current_user = Yii::app()->user->name;
$home_url = $v2url;
$actions_url = $v2url . '/index.php?r=account/summary';
$daily_url = $v2url . '/index.php?r=adminWorkLog/index';
$event_url = $v2url . '/index.php?r=adminEvent/index';
$change_pwd_url = Yii::app()->createUrl('profile/changepasswd');
$auth_url = Yii::app()->createUrl('adminuserNew/auth');
$logout_url = Yii::app()->createUrl('site/logout');
echo <<<eof
<li class="active">
<a href="#" class="dropdown-toggle">
<i class="icon-user"></i>
{$current_user}
<b class="arrow icon-angle-down"></b>
</a>
<ul class="submenu" style="display:none;">
<li><a href="{$actions_url}"><i class="icon-double-angle-right"></i>我的控制台</a></li>
<li><a href="{$daily_url}"><i class="icon-double-angle-right"></i>我的日报</a></li>
<li><a href="{$event_url}"><i class="icon-double-angle-right"></i>待办事项</a></li>
<li><a href="{$change_pwd_url}"><i class="icon-double-angle-right"></i>修改密码</a></li>
eof;
if(Yii::app()->user->admin_level > AdminUserNew::LEVEL_NORMAL){
    echo "<li><a href=\"{$auth_url}\"><i class=\"icon-double-angle-right\"></i>权限管理</a></li>";
}
echo "
<li><a href=\"{$logout_url}\" target=\"__parent\"><i class=\"icon-double-angle-right\"></i>退出</a></li>
</ul></li> 
";


		    $isDriver = isset(Yii::app()->user->type) && Yii::app()->user->type == 1 ? true: false;

                    if (!$isDriver) {
                        $menuList = Menu::model()->getMenuArr(Yii::app()->user->user_id);
                        //print_r($menuList);die;
                        if ($menuList && is_array($menuList)) {
                            foreach ($menuList as $m) {
                                if (isset($m['sub'])) {
                                    echo '<li class="active">';
				    echo '<a class="dropdown-toggle" href="#">';
				    echo '<i class="icon-book"></i>';
				    echo '<span class="menu-text">' . $m['name'] . '</span>';
				    echo '<b class="arrow icon-angle-down"></b>';
				    echo '</a>';
                                    echo '<ul class="submenu" style="display:none;">';
                                    foreach ($m['sub'] as $s) {
                                        $class = $target = '';
                                        if (isset($route) && $route == $s['controller'] . '/' . $s['action']) {
                                            $class = 'active';
                                        }
                                        if (isset($s['is_target']) && $s['is_target']) {
                                            $target = 'target="_blank"';
                                        }
					//$target = 'target="mainFrame"';
                                        if(isset($s['third'])){
                                            $class .= ' thirdmenu' ;
					    echo '<li class="' . $class . '">';
					    echo '<a ' . $target . ' href="' . $s['app_url'] . '/' . $s['action_url'] . '"> ';
					    echo '<i class="icon-double-angle-right"></i>';
					    echo $s['name'];
				    	    echo '<b class="arrow icon-angle-down"></b>';
					    echo '</a>';
                                            echo '<ul class="submenu">';
                                            foreach($s['third'] as $third){
                                                $class_sub = $target = '';
                                                if (isset($route) && $route == $third['controller'] . '/' . $third['action']) {
                                                    $class_sub = 'class="active"';
                                                }
                                                if (isset($third['is_target']) && $third['is_target']) {
                                                    $target = 'target="_blank"';
                                                }
						echo '<li ' . $class_sub . '>';
						echo '<a ' . $target . ' href="' . $third['app_url'] . '/' . $third['action_url'] . '"> ';
						echo '‧ ' . $third['name'];
					        echo '</a></li>';
                                            }
                                            echo '</ul></li>';
                                        }else{
					    echo '<li class="' . $class . '">';
					    echo '<a ' . $target . ' href="' . $s['app_url'] . '/' . $s['action_url'] . '"> ';
					    echo '<i class="icon-double-angle-right"></i>';
					    echo $s['name'];
					    echo '</a>';
                                        }

                                    }
                                    echo '</ul>';
                                }
                                echo '</li>	';
                            }
                        }

                    }?>
 
		        </ul>
        <!--/.nav-list-->
        <div id="sidebar-collapse" class="sidebar-collapse">
            <i class="icon-double-angle-left"></i>
        </div>
    </div>

<script>
jQuery(function () {
    handle_side_menu();
});

function handle_side_menu() {
    var click_event = $.fn.tap ? "tap" : "click"
    $("#menu-toggler").on(click_event, function () {
        $("#sidebar").toggleClass("display");
        $(this).toggleClass("display");
        return false
    });
    var b = $("#sidebar").hasClass("menu-min");
    $("#sidebar-collapse").on(click_event, function () {
        $("#sidebar").toggleClass("menu-min");
        //$(this).find('[class*="icon-"]:eq(0)').toggleClass("icon-double-angle-right");
        b = $("#sidebar").hasClass("menu-min");
        if (b) {
			$(this).find('[class*="icon-"]:eq(0)').removeClass().addClass("icon-double-angle-right");
	                $(".open > .submenu").removeClass("open")
			//$.cookie('ci_sidebar_flag', 'close', { expires: 365, path: '/' });
			$("#leftFrame",parent.document.body).parent().attr("cols","50,*"); 
        } else {
			$(this).find('[class*="icon-"]:eq(0)').removeClass().addClass("icon-double-angle-left");
			//$.cookie('ci_sidebar_flag', 'open', { expires: 365, path: '/' });
			$("#leftFrame",parent.document.body).parent().attr("cols","198,*"); 
		}
		if ($("[data-toggle=table]").length > 0) {
			$("[data-toggle=table]").bootstrapTable("resetView");
		}
    });
    var a = "ontouchend" in document;
    $(".nav-list").on(click_event, function (g) {
	
        var f = $(g.target).closest("a");
        if (!f || f.length == 0) {
            return
        }

	//切换 右侧小标
	if(f.find("b").hasClass("icon-angle-down")){
		f.find("b").removeClass().addClass("arrow icon-angle-up");
	}else{
		f.find("b").removeClass().addClass("arrow icon-angle-down");
	}

        if (!f.hasClass("dropdown-toggle")) {
            if (b && click_event == "tap" && f.get(0).parentNode.parentNode == this) {
                var h = f.find(".menu-text").get(0);
                if (g.target != h && !$.contains(h, g.target)) {
                    return false
                }
            }
            return
        }
        var d = f.next().get(0);
        if (!$(d).is(":visible")) {
            var c = $(d.parentNode).closest("ul");
            if (b && c.hasClass("nav-list")) {
                return
            }
            c.find("> .open > .submenu").each(function () {
                if (this != d && !$(this.parentNode).hasClass("active")) {
                    $(this).slideUp(200).parent().removeClass("open")
                }
            })
        } else {} if (b && $(d.parentNode.parentNode).hasClass("nav-list")) {
            return false
        }
        $(d).slideToggle(200).parent().toggleClass("open");
        return false
    })
}

var lastA = null;
$(".submenu li a").click(function(){
	var target = $(this).attr("target");
	var href = $(this).attr("href");
	if(target != "__parent"){
		//$(this).addClass("on").parent().siblings().find("a").removeAttr("class");
		if(href != "#"){
			$("#mainFrame",parent.document.body).attr("src",href); 
		}

		if(lastA != null){
			lastA.css("color","#616161");
			lastA.find("i").css("color","#616161");
			lastA.find("i").css("display","none");
		}
		lastA = $(this);
		lastA.css("color","#4b88b7");
		lastA.find("i").css("color","#4b88b7");
		lastA.find("i").css("display","block");
		//再请求一次top，刷新用户状态
		$("#topFrame",parent.document.body).attr("src","/index.php?r=default/top"); 
		//再次判断，如果变成login则全部跳转到首页
		
		return false;
	}else{
		//$("#topFrame",parent.document.body).attr("src","<?php echo $v2url;?>/index.php?r=site/logout"); 
		$("#leftFrame",parent.document.body).attr("src",href); 
		return false;
	}

})

$(".thirdmenu a").find("b").click(function(){

	//切换 右侧小标
	if($(this).hasClass("icon-angle-down")){
		$(this).removeClass().addClass("arrow icon-angle-up");
	}else{
		$(this).removeClass().addClass("arrow icon-angle-down");
	}

	$(this).parent().parent().find(".submenu").toggle();
	return false;
})


</script>
