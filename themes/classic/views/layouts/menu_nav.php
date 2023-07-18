<?php
function getClassNameByLabel($name){
	switch($name){
	case '公告': $class = 'edj-v2-ico-notice'; break;
	case '运营': $class = 'edj-v2-ico-operate'; break;
	case '呼叫中心': $class = 'edj-v2-ico-call'; break;
	case '客户': $class = 'edj-v2-ico-client'; break;
	case '品质监控': $class = 'edj-v2-ico-brand'; break;
	case '司机管理': $class = 'edj-v2-ico-driverm'; break;
	case '市场': $class = 'edj-v2-ico-market'; break;
	case '财务': $class = 'edj-v2-ico-finance'; break;
	case '系统': $class = 'edj-v2-ico-sys'; break;
		default: $class = 'edj-v2-ico-notice'; break;
	}
	return $class;
}

function initMenu($label, $labelId, $className, $link = '', $is_target = '0'){
	$menu = array();
	$menu['label'] = $label;
	$menu['labelId'] = $labelId;
	if($link){
		$menu['link'] = $link;
		$menu['is_target'] = $is_target;
	}
	if($className){
		$menu['className'] = $className;
	}
	$menu['hasSub'] = false;

	return $menu;
}

$appv1 = AdminApp::model()->findByPk(1);
$appv2 = AdminApp::model()->findByPk(2);
$v1url = $appv1->url;
$v2url = $appv2->url;
$home_url = $v2url;
$actions_url = $v2url . '/index.php?r=account/summary';
$daily_url = $v2url . '/index.php?r=adminWorkLog/index';
$event_url = $v2url . '/index.php?r=adminEvent/index';
$change_pwd_url = $v1url . '/index.php?r=profile/changepasswd';
$auth_url = $v1url . '/index.php?r=adminuserNew/auth';
$logout_url = $v1url . '/index.php?r=site/logout';

// 菜单集合
$menus = array();

// 一级菜单
$menu = initMenu(Yii::app()->user->name, "0", 'edj-v2-ico-user'); 
$navList = array();
$navList[] = initMenu('我的控制台', "0.1", '', $actions_url);
$navList[] = initMenu('我的日报', "0.2", '', $daily_url);
$navList[] = initMenu('待办事项', "0.3", '', $event_url);
$navList[] = initMenu('修改密码', "0.4", '', $change_pwd_url);
if(Yii::app()->user->admin_level > AdminUserNew::LEVEL_NORMAL){
	$navList[] = initMenu('权限管理', "0.5", '', $auth_url);
}
$navList[] = initMenu('退出', "0.6", '', $logout_url);
$menu['hasSub'] = true;
$menu['navList'] = $navList;

$menus[] = $menu;

$menu = array();
$isDriver = isset(Yii::app()->user->type) && Yii::app()->user->type == 1 ? true: false;
if (!$isDriver) {
	$menuList = Menu::model()->getMenuArr(Yii::app()->user->user_id);
	//print_r($menuList);die;
	if ($menuList && is_array($menuList)) {
		foreach ($menuList as $m) {
			$menu = initMenu($m['name'], $m['id'], getClassNameByLabel($m['name'])); 

			if(!empty($m['sub'])){
				//构建二级菜单
				$navList = array();
				foreach ($m['sub'] as $s) {
					$menuSub = initMenu($s['name'], $m['id'].'.'.$s['id'], '', $s['app_url'].'/'.$s['action_url'], $s['is_target']);
					if(!empty($s['third'])){
						$navListSub = array();
						foreach($s['third'] as $third){
							$navListSub[] = initMenu($third['name'],$m['id'].'.'.$s['id'].'.'.$third['id'], '', $third['app_url'].'/'.$third['action_url'], $third['is_target']);
						}
						$menuSub['hasSub'] = true;	
						$menuSub['navList'] = $navListSub;	
					}
					$navList[] = $menuSub;
				}
			}else{
				$navList = array();
			}
			$menu['hasSub'] = true;	
			$menu['navList'] = $navList;	

			$menus[] = $menu;
		}
	}
}

$data = array();
$data['menus'] = $menus;
$data['v2url'] = $v2url;
$callback = empty($_GET['callback'])?'':$_GET['callback'];
if(!empty($callback)){
	echo "$callback(".json_encode($data).")";
}

