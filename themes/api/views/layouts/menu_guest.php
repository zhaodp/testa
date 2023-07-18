<li class="nav-header">API文档</li>
<li <?php if($route=='api/docs' && @$params['cat']=='docs' && @$params['ver']==1) echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/docs/?cat=docs&ver=1');?>"><i class="icon-headphones"></i>版本1(Stop)</a></li>
<li <?php if($route=='api/docs' && @$params['cat']=='docs' && @$params['ver']==2) echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/docs/?cat=docs&ver=2');?>"><i class="icon-headphones"></i>版本2(Stable)</a></li>
<li <?php if($route=='api/docs' && @$params['cat']=='docs' && @$params['ver']==3) echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/docs/?cat=docs&ver=3');?>"><i class="icon-headphones"></i>版本3(Developing)</a></li>
<li class="nav-header">测试用例</li>
<li <?php if($route=='api/docs' && @$params['cat']=='test') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/docs?cat=test');?>"><i class="icon-headphones"></i>测试</a></li>
<li class="nav-header">应用接入</li>
<li <?php if($route=='api/docs' && @$params['cat']=='object-c') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/docs?cat=object-c');?>"><i class="icon-headphones"></i>Object-C</a></li>
<li class="nav-header">常见问题</li>
<li <?php if($route=='api/docs' && @$params['cat']=='changes') echo 'class="active"'?>><a href="<?php echo Yii::app()->createUrl('/docs?cat=changes');?>"><i class="icon-headphones"></i>文档变更记录</a></li>
