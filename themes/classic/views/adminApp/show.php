<html>
<body>
<?php 
#echo "hh".$model["description"];
$desc = $description;
#$desc = $model["description"];
$apps = ApiKey::model()->searchByDescription($desc);

?>
<?php
function buildTableCell($app){
	echo "<td>".$app->appkey."</td>";
	echo "<td>".$app->secret."</td>";
	echo "<td>".$app->enable."</td>";
	echo "<td>".$app->description."</td>";
	echo "<td>".$app->created."</td>";
	echo "<td>".$app->updateTime."</td>";
	echo "<td>".$app->channel."</td>";
	echo "<td>".$app->accessRight."</td>";
	buildEditCell($app);
}

function buildEditCell($app){
	echo "<td>";
	if($app->enable == 1){
		$href = "index.php?r=adminApp/disable&appkey=".$app->appkey;
		echo "<a href = ".$href." > disable </a>";
	}else{
		$href = "index.php?r=adminApp/enable&appkey=".$app->appkey;
		echo "<a href = ".$href." > enable </a>";
	}
	echo "</td>";

}

?>
<table border="1" 
cellspacing="30">
<tr>
	<td>appkey</td>
	<td>secret</td>
	<td>enable</td>
	<td>description</td>
	<td>created</td>
	<td>updated</td>
	<td>channel</td>
	<td>accessRight</td>
	<td>edit</td>
	<br>
</tr>
<?php
foreach ($apps as $app){
	echo "<tr>";
	buildTableCell($app);
	echo "</tr>";
}

?>
</table>
<a href="index.php?r=adminApp/add"> add new one </a>

</body>
</html>
