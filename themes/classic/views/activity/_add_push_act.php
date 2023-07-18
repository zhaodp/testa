<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl("activity/saveActPush"),'method' => 'post',)); ?>
    <div class="span12">

        <div>
         <?php
            echo "推送时间";
	    Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'ActPush[push_time]',
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
                'htmlOptions' => array('style' => "width:123px")
            ));
                     ?>
       </div>
        <div>
            <?php echo "活动标题" ?>
            <?php  echo $form->textField($model,'title',array('size'=>10,'maxlength'=>128)); ?>
        </div>
 	  <div>
	   <?php echo "活动内容" ?>
            <?php  echo $form->textField($model,'content',array('size'=>10,'maxlength'=>128)); ?>
        </div>

	<div>
           <?php echo "活动地址" ?>
            <?php  echo $form->textField($model,'url',array('size'=>10,'maxlength'=>255)); ?>
        </div>

	<div>
           <?php echo "新老客选择" ?>
           <?php  echo $form->dropDownList($model,'customer_type',MarketingActivity::$customers,array()); ?>
        </div>

	<div>
           <?php echo "适用平台" ?>
	  <?php  echo $form->dropDownList($model,'platform',MarketingActivity::$platforms,array()); ?>
        </div>

	<div>
           <?php echo "适用版本" ?>
           <?php  echo $form->textField($model,'app_ver',array('size'=>10,'maxlength'=>50)); ?>
        </div>
	
	<div>
           <?php echo "适用地区" ?>
        	<input type="checkbox" name="che_all" id="che_all" value="1">&nbsp;全选&nbsp;&nbsp;<input type="checkbox" name="unche_all" id="unche_all" value="1">&nbsp;反选
        	<br><br>
          <?php
                $city = explode(',', $model->citys);
                $citys = Dict::items('city');
                unset($citys[0]);
                foreach ($citys as $key=>$item){
                                        echo CHtml::checkBox("city[]",false,array("value"=>$key,'class'=>'city_id')).$item.'&nbsp;&nbsp;';
                }

          ?>
        </div>
	 <div>
	      <a class="btn btn-info" href="javascript:;" id="btn">确认提交</a>
       	</div>

     </div>
    <?php $this->endWidget(); ?>

</div>
