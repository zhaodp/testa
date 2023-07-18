<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl("driver/addCityVersion"),'method' => 'post',)); ?>

	<div>
          <?php echo CHtml::label('选择版本','version_id');?>
          <?php echo CHtml::dropDownList('version_id',$model->version_id, $version_list); ?>        </div>
	
	<div>
           <?php echo "适用地区" ?>
        	<input type="checkbox" name="che_all" id="che_all" value="1">&nbsp;全选&nbsp;&nbsp;<input type="checkbox" name="unche_all" id="unche_all" value="1">&nbsp;反选
        	<br><br>
          <?php
                //$citys = Dict::items('city');
                $citys = RCityList::model()->getOpenCityList();
                unset($citys[0]);
                foreach ($citys as $key=>$item){
                  $disabled = false;
                  if(in_array($key, $city_array)){ //如果城市已经配置，则置为不可用
                    $disabled = true;
                  }
                  echo CHtml::checkBox("city[]",false,array("value"=>$key,'class'=>'city_id','disabled'=>$disabled)).$item.'&nbsp;&nbsp;';
                }

          ?>
        </div>
	 <div>
	      <a class="btn btn-info" href="javascript:;" id="btn">确认提交</a>
       	</div>

     </div>
    <?php $this->endWidget(); ?>

</div>
