<html>
	<head>
		<title>show</title>

	</head>

	<body>
		<?php
			if(!isset($model) || empty($model->appkey)) {
			echo "	<a href=\"index.php?r=adminApp/add\"> once more </a>";
				//form
			}else{
				echo "success!!";
				echo "<table  border=\"1\" cellspacing=\"60\">";
				echo "<tr>";
					echo "<td>appkey</td>";
					echo "<td>secret</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td>".$model->appkey."</td>";
					echo "<td>".$model->secret."</td>";
				echo "</tr>";
				echo "</table>";
			}

			
		?>
	</body>
</html>
