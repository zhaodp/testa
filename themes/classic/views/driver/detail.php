<?php
/* @var $this CityConfigController */
/* @var $model CityConfig */
$this->setPageTitle($_REQUEST['id'].'司机信息');


?>
<h3><?php echo $_REQUEST['id'].'司机信息'?></h3>

<?php echo '司机版本: '.$ver?>
<br>
<?php echo '手机型号: '.$device?>
<hr>
<h4>基本信息</h4>
<?php  echo '<pre>'; print_r($driverInfo->info); echo '</pre>'?>
<hr>
<h4>屏蔽状态信息</h4>
<?php echo 'mark: '.$driverInfo->mark?>
<br>
<?php echo 'block_at: '.$driverInfo->block_at?>
<br>
<?php echo 'block_mt: '.$driverInfo->block_mt?>
<hr>
<h4>皇冠信息</h4>
<?php echo '<pre>recommand: '; print_r($driverInfo->recommand); echo '</pre>';?>
<br>
<br<hr>
<h4>位置信息</h4>
<?php echo '<pre>position: '; print_r($driverInfo->position); echo '</pre>';?>
<br>
<hr>
<h4>服务信息</h4>
<?php echo '<pre>service: '; print_r($driverInfo->service); echo '</pre>'?>
<br>
<hr>
<h4>其他信息</h4>
<?php echo 'city_id: '.$driverInfo->city_id.','.Dict::item('city',$driverInfo->city_id);?>
<br>
<?php echo 'client_id: '.$driverInfo->client_id?>
<br>
<?php echo 'service_type: '.$driverInfo->service_type?>
<br>
<?php echo 'android: '.$driverInfo->android?>
<br>
<?php echo 'read_flag: '.$driverInfo->read_flag?>
<br>
<?php echo 'id: '.$driverInfo->id?>
<br>
<?php echo 'heartbeat: '.$driverInfo->heartbeat?>
<br>
<?php echo 'last_upload_position: '.$driverInfo->last_upload_position?>
<br>
<?php echo 'idle_time: '.$driverInfo->id?>
<br>
<?php echo 'udid: '.$driverInfo->udid?>
<br>
<?php echo 'status: '.$driverInfo->status?>
<br>
<?php echo 'token: '.$driverInfo->token?>
<br>
<?php echo 'phone: '.$driverInfo->phone?>
<br>
<?php echo 'last_upload_status: '.$driverInfo->last_upload_status?>
<br>
<?php echo 'last_heartbeat: '.$driverInfo->last_heartbeat;
echo '<br> last_heartbeat_h:'.date('Y-m-d H:i:s',$driverInfo->last_heartbeat);
?>