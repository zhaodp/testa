<div class="container-fluid">
    <form id="material" method="post" action="<?php echo Yii::app()->createUrl('material/index');?>">
        <input type="hidden" name="user_id" value="<?php echo $id;?>">
        <input type="hidden" name="type" value="<?php echo $type;?>">
        <div class="tab-content" id="myTabContent">
            <div id="home" class="tab-pane active">
                <div class="row-fluid">
                    <div class="span9" style="width:1100px;">
                        <!--Left content-->
                        <strong><?php echo $title;?></strong>

                        <div style="padding: 19px;" class="alert alert-success" use="form" id="form_0">
                            <button data-dismiss="alert" class="close" type="button" id="del_0" style="margin-right:20px; display: none">×</button>
                            <div class="input-prepend">
                                <span class="add-on">司机姓名：</span>
                                <span class="add-on"><?php if(isset($userinfo['name'])){ echo $userinfo['name'];}?></span>
                            </div>
                            <div class="input-prepend">
                                <span class="add-on"><?php if($id_type == 'signed'){ echo '司机工号';} else{echo '报名编号';} ?>：</span>
                                <span class="add-on"><?php echo $id;?></span>
                                <input type="hidden" name="material[driver_id]" value="<?php echo $id;?>" >
                            </div><br>
                            <div class="input-prepend "><!--input-append-->
                                <span class="add-on">签约时间：</span>
                                <input type="text" style="width:150px;" readonly="readonly" name="material[signed_date]" id="signed_date"  value="<?php echo $time['signed'];?>">

                            </div>

                            <div class="input-prepend "><!--input-append-->
                                <span class="add-on">解约时间：</span>
                                <input type="text" style="width:150px;" readonly="readonly" name="material[signed_date]" id="signed_date"  value="<?php echo $time['unsigned'];?>">
                            </div>
                            <br>
                            <div>
                                <span class="add-on">备注：</span>
                                <?php if($type == 3){
                                    $array = array('礼包补领'=>'礼包补领','免费赠送'=>'免费赠送','其他'=>'其他');
                                    echo CHtml::dropDownList('mark_tag', '', $array , array('id'=>'mark_tag','style'=>'width:100px;'));
                                }?>
                                <input type="text"  value="" maxlength="200"  name="mark" id="mark" style="width:500px;">
                            </div>

                        </div>