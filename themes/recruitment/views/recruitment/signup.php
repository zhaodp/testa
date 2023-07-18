<?php
$this->pageTitle = '在线报名 - e代驾';
?>
	<div class="areaL bs-docs-sidebar">
        <ul class="nav nav-list bs-docs-sidenav affix">
          <li><a href="#driverinfo"><i class="icon-chevron-right"></i> 1.个人信息</a></li>
          <li><a href="#certificateinfo"><i class="icon-chevron-right"></i> 2.合作相关信息</a></li>
          <li><a href="#experience"><i class="icon-chevron-right"></i> 3.代驾经验</a></li>
          <li><a href="#confirm"><i class="icon-chevron-right"></i> 4.确认同意代驾协议</a></li>
          <li><a href="#submit"><i class="icon-chevron-right"></i> 提交报名表</a></li>         
        </ul>
	</div>		
	<div class="areaR">
		<div style="height:67px;"></div>
		<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
	</div>
