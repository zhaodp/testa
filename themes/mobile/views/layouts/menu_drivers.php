    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="#">e代驾</a>
          <div class="navbar">
            <ul class="nav">
              <li <?php if(Yii::app()->getController()->getId()=='notice' && @$_GET['category']==0) echo 'class="active"';?>>
              	<a href="<?php echo Yii::app()->createUrl('/notice/index',array('category'=>0));?>">公告</a></li>
              <li <?php if(Yii::app()->getController()->getId()=='notice' && @$_GET['category']==1) echo 'class="active"';?>>
              	<a href="<?php echo Yii::app()->createUrl('/notice/index',array('category'=>1));?>">培训</a></li>
              <li <?php if(Yii::app()->getController()->getId()=='order' && Yii::app()->getController()->getAction()->getId()=='driver') echo 'class="active"';?>>
              	<a href="<?php echo Yii::app()->createUrl('/order/driver');?>">报单</a></li>
              <li <?php if(Yii::app()->getController()->getId()=='order' && Yii::app()->getController()->getAction()->getId()=='create') echo 'class="active"';?>>
              	<a href="<?php echo Yii::app()->createUrl('/order/create');?>">补单</a></li>
              <?php
              $road_exam = new DriverRoadExam();
              $is_examiner = $road_exam->checkDriverIsExaminer(Yii::app()->user->id);
              ?>
              <?php if ($is_examiner) {?>
              <li <?php if(Yii::app()->getController()->getId()=='recruitment' && Yii::app()->getController()->getAction()->getId()=='road') echo 'class="active"';?>>
                 <a href="<?php echo Yii::app()->createUrl('recruitment/road');?>">路考</a></li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
