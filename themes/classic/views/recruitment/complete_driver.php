<style>
.row{ margin-left:0px;}
</style>
<?php
/* @var $this DriverRecruitmentController */
/* @var $model DriverRecruitment */
/* @var $form CActiveForm */
$gender = Dict::items('gender');
unset($gender[0]);
$marry = Dict::items('marry');
$political_status = Dict::items('political');
$edu = Dict::items('edu');
$driver_type = Dict::items('driver_type');
$status = Dict::items('driver_status');
$arrCars = Dict::items('car_type');
$dataZhaopin = $model->attributes;
$rank = array(''=>'','A'=>'A','B'=>'B','C'=>'C');
?>
<h1>修改司机信息</h1>
<br>
重要信息
<hr>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-recruitment-complete_driver-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

    <div class="row">
		<?php echo $form->labelEx($model,'id_card'); ?>
		<?php echo $form->textField($model,'id_card'); ?>
		<?php echo $form->error($model,'id_card'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_card'); ?>
		<?php echo $form->textField($model,'driver_card'); ?>
		<?php echo $form->error($model,'driver_card'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'id_driver_card'); ?>
		<?php echo $form->textField($model,'id_driver_card'); ?>
		<?php echo $form->error($model,'id_driver_card'); ?>
	</div>

    <div>
        <?php echo $form->labelEx($model,'company'); ?>
        <?php echo $form->textField($model,'company'); ?>
        <?php echo $form->error($model,'company'); ?>
    </div>
    <div>
        <?php echo $form->labelEx($model,'company_contact'); ?>
        <?php echo $form->textField($model,'company_contact'); ?>
        <?php echo $form->error($model,'company_contact'); ?>
    </div>
    <div>
        <?php echo $form->labelEx($model,'company_mobile'); ?>
        <?php echo $form->textField($model,'company_mobile'); ?>
        <?php echo $form->error($model,'company_mobile'); ?>
    </div>
    <div>
        <?php echo $form->labelEx($model,'mobile'); ?>
        <?php echo $form->textField($model,'mobile'); ?>
        <?php echo $form->error($model,'mobile'); ?>
    </div>

    <div class="row">
		<?php echo $form->labelEx($model,'driver_type'); ?>
		<?php echo $form->dropDownList($model,
						'driver_type',
						$driver_type, array()); ?>
		<?php echo $form->error($model,'driver_type'); ?>
	</div>

	<div class="row">
	<?php echo $form->labelEx($model,'driver_year'); ?>
	<?php
	$driver_year = $dataZhaopin['driver_year'] ? $dataZhaopin['driver_year'] : '';
	Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
	$this->widget('CJuiDateTimePicker', array (
		'name'=>'DriverRecruitment[driver_year]',
//		'model'=>$model,  //Model object
		'value'=>$driver_year,
		'mode'=>'date',  //use "time","date" or "datetime" (default)
		'options'=>array (
			'dateFormat'=>'yy-mm-dd'
		),  // jquery plugin options
		'language'=>'zh',
	));
    ?>
    <?php echo $form->error($model,'driver_year'); ?>
	</div>

    <div class="row">
    <?php echo $form->labelEx($model,'address'); ?>
    <?php echo $form->textField($model,'address'); ?>
    <?php echo $form->error($model,'address'); ?>
    </div>

	<div class="row">
		<label class="required" for="DriverRecruitment_domicile">户籍所在地</label>
		<?php //echo $form->textField($model,'domicile'); ?>
        <select name="DriverRecruitment[domicile]" id="s1" style="width:110px;">

        </select>
        <select name="DriverRecruitment[register_city]" id="s2" style="width:110px;">
            <option value="">选择城</option>
        </select>
		<?php echo $form->error($model,'domicile'); ?>
	</div>
<br>
其它信息
<hr>

	<div class="row">
		<?php echo $form->labelEx($model,'gender'); ?>
		<?php echo $form->dropDownList($model,
								'gender',
								$gender,
					array()
				); ?>
		<?php echo $form->error($model,'gender'); ?>
	</div>

    <div class="row">
		<?php echo $form->labelEx($model,'age'); ?>
		<?php echo $form->textField($model,'age'); ?>
		<?php echo $form->error($model,'age'); ?>
	</div>

	<div class="row">
		<div >熟练驾驶车型*</div>
<?php
echo $form->checkBoxList($model,'driver_cars',$arrCars,array('separator'=>'','template'=>'<div  style="float:left">{input} {label}</div>','labelOptions'=>array('style'=>'display:inline;')), array());
?>
<?php
echo CHtml::checkBox('checkallcars','', array ("class" => "checkallcars"));
?>
<label style='display:inline;' for='checkallcars'>全选</label>
<script language="javascript">

  $(document).ready(function(){
    // powerful jquery ! Clicking on the checkbox 'checkAll' change the state of all checkbox
    $('.checkallcars').click(function () {
      $("input[id^='DriverRecruitment_driver_cars']:not([disabled='disabled'])").attr('checked', this.checked);
    });
  });
</script>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'experience'); ?>
		<?php echo $form->textField($model,'experience'); ?>
		<?php echo $form->error($model,'experience'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'src'); ?>
			<?php
			$src = Dict::items('recruitment_src');

			ksort($src);
			echo $form->dropDownList($model,
							'src',
							$src, array()); ?>
			<?php echo $form->error($model,'src'); ?>
			<?php echo $form->textField($model,'other_src',array('size'=>20,'maxlength'=>20,'style'=>'display:none'));?>
			<?php echo $form->error($model,'other_src'); ?>
	</div>

    <div class="row">
    <?php echo $form->labelEx($model,'work_type'); ?>
    <?php
    $work_type = Dict::items('work_type');
    echo $form->dropDownList($model,
        'work_type',
        $work_type,
        array()
    );
    echo $form->error($model, 'work_type');
    ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'city_id'); ?>
		<?php
						$citys = Dict::items('city');
						$citys[0] = '--请选择城市--';

						echo $form->dropDownList($model,
									'city_id',
									$citys,
						array(
							'ajax' => array(
							'type'=>'POST', //request type
							'url'=>Yii::app()->createUrl('recruitment/district'),
							'update'=>'#DriverRecruitment_district_id', //selector to update
							'data'=>array('city_id'=>'js:$("#DriverRecruitment_city_id").val()')
							))
						);
						echo $form->error($model, 'city_id');
					?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'district_id'); ?>
        <?php
        $districts = District::model()->findAll('city_id=:city_id', array(':city_id' => $dataZhaopin['city_id']));
        $districts = CHtml::listData($districts,'id','name');
        $districts[0] = '--请选择区域--';
        ksort($districts);
        echo $form->dropDownList($model,
            'district_id',
            $districts,
            array()
        );
        echo $form->error($model, 'district_id');
        ?>
	</div>
<br>
紧急联系人
<hr>
	<div class="row">
		<?php echo $form->labelEx($model,'contact'); ?>
		<?php echo $form->textField($model,'contact'); ?>
		<?php echo $form->error($model,'contact'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_phone'); ?>
		<?php echo $form->textField($model,'contact_phone'); ?>
		<?php echo $form->error($model,'contact_phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_relate'); ?>
		<?php echo $form->textField($model,'contact_relate'); ?>
		<?php echo $form->error($model,'contact_relate'); ?>
	</div>


	<div class="row" style="display: none">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>
	<div class="row" >
		<label>单位名称</label>
		<?php echo $form->textField($model,'join_company'); ?>
		<?php echo $form->error($model,'join_company'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('保存',array('name' => 'save')); ?>
		<?php //echo CHtml::submitButton('保存并签约', array('name' => 'saveEntry')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
			
			<script language="javascript">
	$(function(){
		<?php if($model->src=='8'){?>
			$("#DriverRecruitment_other_src").show();
		<?php }?>
		
		$("#DriverRecruitment_src").change(function(){
			
			$("#DriverRecruitment_other_src").hide();
			
			if($("#DriverRecruitment_src").val()=='8')
			{
				$("#DriverRecruitment_other_src").show();
			}else{
				$("#DriverRecruitment_other_src").hide();
			}
		});
		complete = '<?php echo $model->complete?>';
		if(complete=='1'){ $("#complete").attr("checked","checked");}
		else{$("#complete").removeAttr("checked");}

	});
</script>

<script>
    /**
     * 省联动
     */

        window.onload = function() {
        /*
         for(i=0;i<s.length-1;i++)
         document.getElementById(s[i]).onchange=new Function("change("+(i+1)+")");

        <?php if ($this->getAction()->getId() == 'update') {?>
         jQuery('[value="<?php echo $model->domicile; ?> "]').attr('selected', 'selected');
         change(1);
        <?php } else {?>
         change(0);
        <?php } ?>
         */
        setup();
    }

    function Dsy()
    {
        this.Items = {};
    }
    Dsy.prototype.add = function(id,iArray)
    {
        this.Items[id] = iArray;
    }
    Dsy.prototype.Exists = function(id)
    {
        if(typeof(this.Items[id]) == "undefined") return false;
        return true;
    }

    function change(v){
        var str="0";
        for(i=0;i<v;i++){ str+=("_"+(document.getElementById(s[i]).selectedIndex-1));};
        var ss=document.getElementById(s[v]);
        with(ss){
            length = 0;
            options[0]=new Option(opt0[v],opt0[v]);
            if(v && document.getElementById(s[v-1]).selectedIndex>0 || !v)
            {
                if(dsy.Exists(str)){
                    ar = dsy.Items[str];
                    for(i=0;i<ar.length;i++)options[length]=new Option(ar[i],ar[i]);
                    if(v)options[1].selected = true;
                }
            }
            if(++v<s.length){change(v);}
        }
    }

    var dsy = new Dsy();

    dsy.add("0",["北京","天津","河北","山西","内蒙古","辽宁","吉林","黑龙江","上海","江苏","浙江","安徽","福建","江西","山东","河南","湖北","湖南","广东","广西","海南","重庆","四川","贵州","云南","西藏","陕西","甘肃","青海","宁夏","新疆","香港","澳门","台湾"]);

    dsy.add("0_0",["北京"]);
    dsy.add("0_1",["天津"]);
    dsy.add("0_2",["石家庄","张家口","承德","秦皇岛","唐山","廊坊","衡水","沧州","邢台","邯郸","保定"]);
    dsy.add("0_3",["太原","朔州","大同","长治","晋城","忻州","晋中","临汾","吕梁","运城"]);
    dsy.add("0_4",["呼和浩特","包头","赤峰","呼伦贝尔","鄂尔多斯","乌兰察布","巴彦淖尔","兴安","阿拉善","锡林郭勒","通辽"]);
    dsy.add("0_5",["沈阳","朝阳","阜新","铁岭","抚顺","丹东","本溪","辽阳","鞍山","大连","营口","盘锦","锦州","葫芦岛"]);
    dsy.add("0_6",["长春","白城","吉林","四平","辽源","通化","白山","延边"]);
    dsy.add("0_7",["哈尔滨","七台河","黑河","大庆","齐齐哈尔","伊春","佳木斯","双鸭山","鸡西","加格达奇","牡丹江","鹤岗","绥化"]);
    dsy.add("0_8",["上海"]);
    dsy.add("0_9",["南京","徐州","连云港","宿迁","淮安","盐城","扬州","泰州","南通","镇江","常州","无锡","苏州"]);
    dsy.add("0_10",["杭州","湖州","嘉兴","舟山","宁波","绍兴","衢州","金华","台州","温州","丽水"]);
    dsy.add("0_11",["合肥","宿州","淮北","亳州","阜阳","蚌埠","淮南","滁州","马鞍山","芜湖","铜陵","安庆","黄山","六安","巢湖","池州","宣城"]);
    dsy.add("0_12",["福州","南平","莆田","三明","泉州","厦门","漳州","龙岩","宁德"]);
    dsy.add("0_13",["南昌","九江","景德镇","鹰潭","新余","萍乡","赣州","上饶","抚州","宜春","吉安"]);
    dsy.add("0_14",["济南","聊城","德州","东营","淄博","潍坊","烟台","威海","青岛","日照","临沂","枣庄","济宁","泰安","莱芜","滨州","菏泽"]);
    dsy.add("0_15",["郑州","三门峡","洛阳","焦作","新乡","鹤壁","安阳","濮阳","开封","商丘","许昌","漯河","平顶山","南阳","信阳","周口","驻马店"]);
    dsy.add("0_16",["武汉","十堰","襄樊","荆门","孝感","黄冈","鄂州","黄石","咸宁","荆州","宜昌","随州","恩施","仙桃","天门","潜江","神农架"]);
    dsy.add("0_17",["长沙","张家界","常德","益阳","岳阳","株洲","湘潭","衡阳","郴州","永州","邵阳","怀化","娄底","湘西"]);
    dsy.add("0_18",["广州","清远","韶关","河源","梅州","潮州","汕头","揭阳","汕尾","惠州","东莞","深圳","珠海","中山","江门","佛山","肇庆","云浮","阳江","茂名","湛江"]);
    dsy.add("0_19",["南宁","桂林","柳州","梧州","贵港","玉林","钦州","北海","防城港","崇左","百色","河池","来宾","贺州"]);
    dsy.add("0_20",["海口","三亚"]);
    dsy.add("0_21",["重庆"]);
    dsy.add("0_22",["成都","广元","绵阳","德阳","南充","广安","遂宁","内江","乐山","自贡","泸州","宜宾","攀枝花","巴中","资阳","眉山","雅安","阿坝","甘孜","凉山","达州"]);
    dsy.add("0_23",["贵阳","六盘水","遵义","安顺","毕节","铜仁","黔东南","黔南","黔西南"]);
    dsy.add("0_24",["昆明","曲靖","玉溪","保山","昭通","丽江","普洱","临沧","宁德","德宏","怒江","楚雄","红河","文山","大理","迪庆","西双版纳"]);
    dsy.add("0_25",["拉萨","那曲","昌都","林芝","山南","日喀则","阿里"]);
    dsy.add("0_26",["西安","延安","铜川","渭南","咸阳","宝鸡","汉中","安康","商洛"]);
    dsy.add("0_27",["兰州 ","嘉峪关","金昌","白银","天水","武威","酒泉","张掖","庆阳","平凉","定西","陇南","临夏","甘南"]);
    dsy.add("0_28",["西宁","海东","海北","黄南","玉树","海南","果洛","海西"]);
    dsy.add("0_29",["银川","石嘴山","吴忠","固原","中卫"]);
    dsy.add("0_30",["乌鲁木齐","克拉玛依","喀什","阿克苏","和田","吐鲁番","哈密","塔城","阿勒泰","克孜勒","博尔塔拉","昌吉", "伊犁","巴音郭楞","河子","阿拉尔","五家渠","图木舒克"]);
    dsy.add("0_31",["香港"]);
    dsy.add("0_32",["澳门"])
    dsy.add("0_33",["台湾"])

    var s=["s1","s2"];
    var opt0 = ["请选择","请选择"];

    function setup()
    {
        for(i=0;i<s.length-1;i++)
            document.getElementById(s[i]).onchange=new Function("change("+(i+1)+")");
        change(0);
        <?php if ($this->getAction()->getId() == 'update') {?>
        var user_domicile = '<?php echo $model->domicile;?>';
        var user_city = '<?php echo $model->register_city ? $model->register_city : '';?>';
        var index;
        for(var i=0; i<dsy.Items["0"]; i++) {
            if (dsy.Items["0"][i] == user_domicile) {
                index = i;
            }
        }
        jQuery('[value="'+user_domicile+'"]').attr('selected', 'selected');
        change(1);
        if (user_city) {
            jQuery('[value="'+user_city+'"]').attr('selected', 'selected');
        } else {
            jQuery('#s2 option').eq(0).attr('selected', 'selected');
        }
        <?php } ?>
    }

</script>

