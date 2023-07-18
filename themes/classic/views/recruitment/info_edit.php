

    <html lang="en"><head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>司机信息 修改</title>
    </head>
    <body>

    <h3>司机信息 修改 <?php echo($driver['id'].'（'.$driver['name'].'）'); ?></h3>

    <div class="form">

        <form id="vip-form" action="/v2/index.php?r=recruitment/infoEdit" method="post" onsubmit="return validateSubmit()">
            <input type="hidden" id="id" name="id" value="<?php echo($driver['id']); ?>">
            <h1>紧急联系人信息： </h1>
            <div class="row-fluid">
                <div class="span4">
                    <label for="contact">紧急联系人姓名：</label>
                    <input type="text" id="contact" name="contact" value="<?php echo($driver['contact']); ?>">
                </div>
                <div class="span4">
                    <label for="contact_phone">紧急联系人电话：</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="<?php echo($driver['contact_phone']); ?>">
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <?php
                    $arr = array(
                        '-1'=>'请选择',
                        '亲戚'=>'亲戚',
                        '朋友'=>'朋友',
                        '父亲'=>'父亲',
                        '母亲'=>'母亲',
                        '媳妇'=>'媳妇',
                        '丈夫'=>'丈夫',
                        '儿子'=>'儿子',
                        '女儿'=>'女儿'
                    );
                    echo CHtml::label('你与紧急联系人的关系：','contact_relate');
                    echo CHtml::dropDownList('contact_relate',
                        $driver['contact_relate'],
                        $arr
                    );?>
                </div>
            </div>


            <h1>工服信息： </h1>
            <div class="row-fluid">
                <div class="span4">
                    <label for="height">身高（厘米）：</label>
                    <input type="text" id="height" name="height" value="<?php echo($driver['height']); ?>">
                </div>
                <div class="span4">
                    <label for="size">工服尺寸：</label>
                    <select id="suitSize" name="size">
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                        <option value="XXXL">XXXL</option>
                    </select>
                    <input type="hidden" id="hidden_size" name="hidden_size" value="<?php echo($driver['size']); ?>">

                </div>
            </div>


            <h1>收货人信息： </h1>
            <div class="row-fluid">
                <div class="span4">
                    <label for="mail_name">收货人姓名：</label>
                    <input type="text" id="mail_name" name="mail_name" value="<?php echo($driver['mail_name'] ? $driver['mail_name']:$driver['name']); ?>">
                </div>
                <div class="span4">
                    <label for="mail_phone">收货人电话：</label>
                    <input type="text" id="mail_phone" name="mail_phone" value="<?php echo($driver['mail_phone']?$driver['mail_phone']:$driver['mobile']); ?>">
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
		<?php
            $province[-1]='请选择';
            ksort($province);
			echo CHtml::label('省选择','province');
			echo CHtml::dropDownList('province_id',
                $driver['mail_province'],
						$province,
				array(
					'ajax' => array(
					'type'=>'POST', //request type
					'url'=>Yii::app()->createUrl('recruitment/getSubDistricts'),
					'update'=>'#city_id', //selector to update
					'data'=>array('parent_id'=>'js:$("#province_id").val()', 'admin'=>'1')
                ),
                //    'ajax' => array(
                //        'type'=>'POST', //request type
                //        'url'=>Yii::app()->createUrl('recruitment/getSubDistricts'),
                //        'update'=>'#district_id', //selector to update
                //        'data'=>array('parent_id'=>'js:$("#city_id").val()', 'admin'=>'1')
                //    ),
            )
			);
        $city[-1]='请选择';
        ksort($city);
        echo CHtml::label('市选择','city');
            echo CHtml::dropDownList('city_id',
                $driver['mail_city'],
                $city,
                array(
                    'ajax' => array(
                        'type'=>'POST', //request type
                        'url'=>Yii::app()->createUrl('recruitment/getSubDistricts'),
                        'update'=>'#district_id', //selector to update
                        'data'=>array('parent_id'=>'js:$("#city_id").val()', 'admin'=>'1')
                    )
                )
            );
            $district[-1]='请选择';
            ksort($district);
        echo CHtml::label('区选择','district');
            echo CHtml::dropDownList('district_id',
                $driver['mail_district'],
                        $district,
						array(),
				array()
			);


		?>

                    <label for="mail_addr">收货人地址： </label>
                    <input type="text" id="mail_addr" name="mail_addr" value="<?php echo($driver['mail_addr']); ?>">
                </div>
            </div>



            <div class="row-fluid">
                <div class="span4">
                    <input class="btn btn-success" type="submit" name="yt0" value="保存">
                </div>
            </div>
        </form>
    </div>





    </body>

    <script type="text/javascript">
       var hidden_suit_size = $("#hidden_size").val();
       if(hidden_suit_size!=null&&hidden_suit_size!=''){
           $("#suitSize").val(hidden_suit_size);
       }

        function validateSubmit(){
            if(!checkisNull($("#contact").val())){
                alert("紧急联系人不能为空！");
                return false;
            };

            if(!checkisNull($("#contact_phone").val())){
                alert("紧急联系人电话不能为空！");
                return false;
            };
            if($("#contact_relate").val()==-1){
                alert("请选择与紧急联系人的关系！");
                return false;
            };
            if($("#height").val()==0){
                alert("请填写收货人身高！");
                return false;
            };
           if(!checkisNull($("#mail_name").val())){
                alert("收货人姓名不能为空！");
               return false;
           };
            if(!checkisNull($("#mail_phone").val())){
                alert("收货人电话不能为空！");
                return false;
            };

            if($("#province_id").val()==-1){
                alert("请选择省份！");
                return false;
            };

            if($("#city_id").val()==-1){
                alert("请选择城市！");
                return false;
            };
            if($("#district_id").val()==-1){
                alert("请选择区！");
                return false;
            };
            if(!checkisNull($("#mail_addr").val())){
                alert("请填写收货人地址！");
                return false;
            };
            return true;
        }
       function  checkisNull(validateObj){
           validateObj = validateObj.trim();
           if (validateObj != null && validateObj !='') {
               return true;
           } else {
               return false;
           }
       }
    </script>
    </html>
