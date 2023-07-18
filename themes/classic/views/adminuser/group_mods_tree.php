<?php
$this->pageTitle = 'E代驾角色－功能树';
?>
<h4>E代驾角色－功能树</h4>
<style type="text/css">
.big_box {
	width: 100%;
	height: auto;
	margin-top: 5px;
	clear: both;
}

.box_group {
	width: 100%;
	height: 23px;
	border-bottom: 1px solid #CCCCCC
}

.box_group_title {
	width: 100px;
	height: 20px;
	text-align: center;
	padding-top: 3px;
	overflow: hidden;
	background: #DFF9E4
}

.mods_box {
	width: 100%;
	height: auto;
	border-left: 1px solid #CCCCCC;
	border-bottom: 1px solid #CCCCCC;
	border-right: 1px solid #CCCCCC;
}

.mods_box_title {
	width: 200px;
	height: 40px;
	border-bottom: 1px solid #CCCCCC;
	float: left;
	text-align: center;
	padding-top: 3px;
	margin: 3px;
}

body {
	font-size: 12px;
}
</style>
<?php
if (! empty ( $group_mods_tree )) {
	foreach ( $group_mods_tree as $item ) {
		echo '<div class="big_box"><div class="box_group"><div class="box_group_title">' . $item ['name'] . '</div></div>';
		
		if (isset ( $item ['mods'] ) && ! empty ( $item ['mods'] )) {
			echo '<div class="mods_box">';
			foreach ( $item ['mods'] as $items ) {
				echo '<div class="mods_box_title">' . $items ['name'] .'<br>('.$items ['controller'].'/'.$items ['action'].')</div>';
			}
			echo '</div>';
		} else {
			echo '<center>暂无功能分配</center>';
		}
		
		echo '</div>';
	}
} else {
	
	echo '<center><h3>暂无相关权限配置</h3></center>';
}
?>

