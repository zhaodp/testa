<?php
$this->pageTitle = 'E代驾后台管理系统功能地图';
?>
<h4>E代驾后台管理系统功能地图</h4>

    <ul class="nav nav-tabs" id="myTab">
	    <li class="active"><a href="#mods_tree">E代驾后台管理系统功能地图</a></li>
<!--	    <li><a href="#group_mods_tree">E代驾角色（用户组）功能地图</a></li>-->
    </ul>
     
<div class="tab-content">
	    <div class="tab-pane active" id="mods_tree">
		<?php 
			$mods = AdminRoles::model()->getValidMods();
			$new_mods = array();
			foreach( $mods as $item )
			{
				$item['view_name'] = $item['name']."({$item['controller']}/{$item['action']})";
				$new_mods[$item['controller']][] = $item;
			}
			
			foreach( $new_mods as $g => $item_mods ) 
			{
				echo '<legend><div style="margin-bottom:0px;width:200px;" class="alert-success">'.CHtml::label($g, null)." </div></legend>";
				echo "<div id = '{$g}' >";
				$i = 0;
				foreach($item_mods as $item) {
					if($i==0){
						echo "&nbsp;&nbsp;";
						$i++;
					}
					echo '<label class="checkbox inline">'.$item['view_name'].'</label>';
				}
				echo "</div>";
			}

		?>    
	    
	    </div>
	    
	    
<!--	    <div class="tab-pane" id="group_mods_tree"> -->

<!--	    </div>-->
   
</div>

<script type="text/javascript">

$(document).ready(function(){
    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
     });
	
});

</script>