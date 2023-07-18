<?php
	/* @var $this ZhaopinController */
	$this->pageTitle = 'e代驾';
	
	$source = isset($_GET['s']) ? trim($_GET['s']):0;
	switch ($source){
		case 1:
			$trackview = '/sms/';
			break;
		case '2':
			$trackview = '/unicom/';
			break;
		default:
			$trackview = '/';
			break;
	}
?>
<div style="text-align:center;">
	<div>
		<img src="<?php echo SP_URL_STO;?>img/logo_b.png" width="180px" border="0"/><br/>
		<h4>谁说开车不喝酒，e代驾只需39！</h4>
		<a class='btn btn-primary btn-large' 
			style="margin-top:20px;width:250px; background:#0074CC url('<?php echo SP_URL_STO;?>img/android.png') no-repeat 16px 40px;" 
			href='<?php echo $params['appVersionAndroid']['ysxurl'];?>' 
			onClick="_gaq.push(['_setCustomVar', 1, 'Download', 'Android',3]);_gaq.push(['_trackPageview', '<?php echo $trackview; ?>download/android']);">
			  Android（安卓版）<br/>
			<span style="color:#FF9900;font-size:12px;">最新版本：<?php echo $params['appVersionAndroid']['latest'];?></span>
			<br/><span style="font-size:12px;">更新日期：<?php echo $params['appVersionAndroid']['updatetime'];?></span></a>
	</div>
</div>