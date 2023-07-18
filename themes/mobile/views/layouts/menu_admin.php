<?php 
$yesterday = date('Y-m-d', time() - 24 * 3600) . ' 09:00';
$today = date('Y-m-d H:i', strtotime($yesterday) + 24 * 3600); 
?>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="<?php echo Yii::app()->createUrl('/');?>">e代驾</a>
          <div class="navbar">
            <ul class="nav">
              <li <?php if($route =='/report/online') echo 'class="active"';?>>
              	<a href="<?php echo Yii::app()->createUrl('/report/online');?>">在线</a></li>
              <li <?php if($route =='/report/ordertrends') echo 'class="active"';?>>
              	<a href="<?php echo Yii::app()->createUrl('/report/ordertrends');?>">统计</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
