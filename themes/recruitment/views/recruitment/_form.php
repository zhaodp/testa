<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */
/* @var $form CActiveForm */


// $gender = array( '女', '男');
// $marry = array( '已婚', '未婚');
// $political_status = array('群众', '无党派人士', '民主党派 ', '团员', '中共党员(含预备党员)');
// $edu = array('大专','本科','硕士','博士','MBA','EMBA','中专','中技','高中','初中','其他');
// $driver_type = array('A1','A2','A3','B1','B2','C1');
// $status = array('全部','已报名', '已通知培训', '已培训考核', '已签约');
// $arrCars = array(
// '1'=>'微/小型车',
// '2'=>'普通轿车',
// '3'=>'高档轿车',
// '4'=>'商务/大型车辆',
// '5'=>'MPV/SUV等'
// );
$gender = Dict::items('gender');
unset($gender[0]);
$marry = Dict::items('marry');
$political_status = Dict::items('political');
$edu = Dict::items('edu');
$driver_type = Dict::items('driver_type');
$status = Dict::items('driver_status');
$arrCars = Dict::items('car_type');
$domicile = array(
	0=>'请选择',
	'北京'=>'北京',
	'上海'=>'上海',
	'广东'=>'广东',
	'浙江'=>'浙江',
	'河北'=>'河北',
	'山东'=>'山东',
	'辽宁'=>'辽宁',
	'四川'=>'四川',
	'重庆'=>'重庆',
	'天津'=>'天津',
	'黑龙江'=>'黑龙江',
	'吉林'=>'吉林',
	'甘肃'=>'甘肃',
	'青海'=>'青海',
	'河南'=>'河南',
	'江苏'=>'江苏',
	'湖北'=>'湖北',
	'湖南'=>'湖南',
	'江西'=>'江西',
	'云南'=>'云南',
	'福建'=>'福建',
	'海南'=>'海南',
	'山西'=>'山西',
	'陕西'=>'陕西',
	'贵州'=>'贵州',
	'安徽'=>'安徽',
	'广西'=>'广西',
	'内蒙古'=>'内蒙古',
	'西藏'=>'西藏',
	'新疆'=>'新疆',
	'宁夏'=>'宁夏',
	'澳门'=>'澳门',
	'香港'=>'香港',
	'台湾'=>'台湾',	
);

$dataZhaopin = $model->attributes;


?>

<?php
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-zhaopin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error',
));
?>

<?php echo $form->errorSummary($model); ?>

<section id="driverinfo" class="driverinfo">
    <div class="row-fluid">
	    <div class="span12">
		<div class="page-header">
		<h2>1. 个人信息（必填）</h2>
		</div>
	</div>
	</div>
	<div class="row-fluid">
        <!--姓名-->
		<div class="span4">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'name',array('class','alert alert-error')); ?>
		</div>
        <!--手机-->
        <div class="span4">
	    <?php echo $form->labelEx($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'mobile'); ?>
		</div>
        <div class="span4">
            <font color="red">
            请确保手机号填写正确。
            我们将会通过短信通知面试信息
            </font>
        </div>
    </div>
    <div class="row-fluid">
        <!--身份证号-->
        <div class="span4">
	    <?php echo $form->labelEx($model,'id_card'); ?>
		<?php echo $form->textField($model,'id_card',array('size'=>20,'maxlength'=>20,'onkeyup'=>"document.getElementById('DriverRecruitment_driver_card').value =this.value;")); ?>
		<?php echo $form->error($model,'id_card'); ?>
		</div>
        <!--驾驶证号-->
        <div class="span4">
	    <?php echo $form->labelEx($model,'driver_card'); ?>
		<?php echo $form->textField($model,'driver_card',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'driver_card'); ?>
		</div>
        <!--驾照档案编号-->
        <div class="span4">
	    <?php echo $form->labelEx($model,'id_driver_card'); ?>
		<?php echo $form->textField($model,'id_driver_card'); ?>
		<?php echo $form->error($model,'id_driver_card'); ?>
		</div>
    </div>
    <div class="row-fluid">
        <!--驾照申领日期-->
	    <div class="span4">
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
                        'dateFormat'=>'yy-mm-dd',
                        'changeYear'=>true,
                        'changeMonth'=> true
                    ),  // jquery plugin options
                    'language'=>'zh',
                    'htmlOptions'=>array(
                        'style'=>'height:18px'
                    ),
            ));
        ?>
		<?php echo $form->error($model,'driver_year'); ?>
	    </div>
        <!--性别-->
        <div class="span4">
	    <?php echo $form->labelEx($model,'gender'); ?>
	    <?php echo $form->dropDownList($model,
								'gender',
								$gender,
					array()
				); ?>
				<?php echo $form->error($model,'gender'); ?>
	    </div>
	    <!--年龄-->
	    <div class="span4">
		<?php echo $form->labelEx($model,'age'); ?>
		<?php echo $form->textField($model,'age',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'age'); ?>
		</div>
    </div>
    <div class="row-fluid">
        <!--户籍-->
        <div class="span4">
	        <label class="required" for="DriverRecruitment_domicile">户籍 <span class="required">*</span></label>
            <select name="DriverRecruitment[domicile]" id="s1" style="width:110px;">

            </select>
            <select name="DriverRecruitment[register_city]" id="s2" style="width:110px;">
                <option value="">选择城</option>
            </select>
		</div>
    </div>
</section>
<section id="certificateinfo" class="certificateinfo">
	<div class="row-fluid">								
	<div class="span12">
	<div class="page-header">
		<h2>2. 合作相关信息（必填）</h2>
	</div>
	</div>
	</div>
	<div class="row-fluid">								
	    <!--准驾车型-->
        <div class="span4">
		    <?php echo $form->labelEx($model,'driver_type'); ?>
		    <?php echo $form->dropDownList($model,
						'driver_type',
						$driver_type, array()); ?>
		    <?php echo $form->error($model,'driver_type',array('class','alert alert-error')); ?>
	    </div>
        <!-熟练驾驶车型->
        <div class="span4" style="width:470px">
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
    </div>

    <div class="row-fluid">
        <!--城-->
        <div class="span4">
		    <?php echo $form->labelEx($model,'city_id'); ?>
			<?php
                /*
			    $citys = Dict::items('city');
			    $citys[0] = '--请选择城--';
				echo $form->dropDownList($model,
				            'city_id',
							$citys,
						    array(
							    'ajax' => array(
							    'type'=>'POST', //request type
							    'url'=>Yii::app()->createUrl('district'),
							    'update'=>'#DriverRecruitment_district_id', //selector to update
							    'data'=>array('city_id'=>'js:$("#DriverRecruitment_city_id").val()')
							))
						);
			    echo $form->error($model,'city_id');
                */
            $city_list = CityTools::cityPinYinSort();
            $callback = "function(city_id, city_name){
                var city_id = arguments[0];
                jQuery.post(
                    '".Yii::app()->createUrl('district')."',
                    {
                        'city_id' : city_id
                    },
                    function(d) {
                        jQuery('#DriverRecruitment_district_id').html(d);
                    }
                );

            }";
            $this->widget("application.widgets.common.DropDownCity", array(
                'cityList' => $city_list,
                'name' => 'DriverRecruitment[city_id]',
                'type' => 'modal',
                'defaultText' => '请选择城市',
                'callback' => $callback,
                'htmlOptions' => array(
                    'style' => 'width: 134px; cursor: pointer;',
                    'readonly' => 'readonly',
                )
            ));
            ?>
		</div>
        <!--居住区域-->
		<div class="span4">
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
        <!--工作方式-->
		<div class="span4">
            <input type="hidden" name="DriverRecruitment[work_type]" value="1" />
            <!--
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
			-->
		</div>
    </div>
    <div class="row-fluid">
        <!--居住详细地址-->
        <div class="span6">
		    <?php echo $form->labelEx($model,'address'); ?>
			<?php echo $form->textField($model,'address',array('size'=>50,'maxlength'=>50, 'style'=>"width:500px;")); ?>
			<?php echo $form->error($model, 'address'); ?>
	    </div>
	</div>
    <div class="row-fluid">
		<!--来源渠道-->
        <div class="span4">
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
        <div class="span4" id="recommender" style="display: none">
			<label for="DriverRecruitment_recommender">推荐人编号(如：BJ9000)</label>
			<?php echo $form->textField($model,'recommender',array('size'=>20,'maxlength'=>10)); ?>
			<?php echo $form->error($model,'recommender'); ?>
		</div>
	</div>
    <div class="row-fluid">
        <!--紧急联系人姓名-->
		<div class="span4">
			<?php echo $form->labelEx($model,'contact'); ?>
			<?php echo $form->textField($model,'contact',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'contact'); ?>
		</div>
        <!--紧急联系人电话-->
		<div class="span4">
			<?php echo $form->labelEx($model,'contact_phone'); ?>
			<?php echo $form->textField($model,'contact_phone',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'contact_phone'); ?>
		</div>
        <!--紧急联系人关系-->
		<div class="span4">
			<?php echo $form->labelEx($model,'contact_relate'); ?>
			<?php echo $form->textField($model,'contact_relate',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'contact_relate'); ?>
		</div>
	</div>
</section>
<section id="experience" class="experience">
	<div class="row-fluid">
	<div class="span12">	
	<div class="page-header">
		<h2>3.代驾经验（选填）</h2>
	</div>
	</div>
	</div>		
	<div class="row-fluid">
		<div class="span12">
			<?php echo $form->labelEx($model,'experience'); ?>
			<?php echo $form->textArea($model,'experience', array('class'=>'span12','style' => 'height: 200px;')); ?>
			<?php echo $form->error($model,'experience'); ?>	
		</div>			
	</div>
</section>
<section id="confirm" class="confirm">
    <div class="row-fluid">
        <div class="span12">
            <div class="page-header">
                <h2>4.请阅读并同意<a href="/agreement" target="_blank">《e代驾代驾员合作协议》</a></label></h2>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <p><label><?php echo CHtml::checkBox("agree");?>确认同意</label></p><?php echo $form->error($model,'agree');?>
            <p>1.以上填写的信息属实，提供的证件真实有效，一旦被发现有不属实的地方，愿意接受e代驾任何处理方式，甚至解除合作。</p>
            <p>2.确认与公司的合作出于自愿，接受服务信息，获得合作利益的同时依法履行合作的义务。</p>
        </div>
    </div>
</section>
<section id="submit" class="submit">
	<div style='margin:0 auto;width:400px'>
		请输入验证码:
        <?php
            $this->widget("CCaptcha");
            echo $form->textField($model,'verifyCode',array('size'=>20,'maxlength'=>20));
            echo $form->error($model,'verifyCode');
            echo "<br><br>";
            echo CHtml::submitButton('提交报名表',array('class'=>'span3 btn-large btn-success btn-block'));
            echo "<br><br><br><br>";
        ?>
	</div>
</section>
<?php $this->endWidget(); ?>
<script language="javascript">
	$(function(){
		<?php if($model->src=='8'){?>
			$("#DriverRecruitment_other_src").show();
		<?php }?>

        $("#DriverRecruitment_src option").removeAttr('selected');

        $("#DriverRecruitment_src option").eq(0).before("<option selected='selected' value=''>请选择来源</option>");

		$("#DriverRecruitment_src").change(function(){
			
			$("#DriverRecruitment_other_src").hide();
			
			if($("#DriverRecruitment_src").val()=='8')
			{
				$("#DriverRecruitment_other_src").show();
			}else if ($("#DriverRecruitment_src").val()=='6'){
                $("#recommender").show();
            } else {
                $("#recommender").hide();
				$("#DriverRecruitment_other_src").hide();
			}
		});
	});

    jQuery('document').ready(function(){
        jQuery('#driver-zhaopin-form').submit(function(){
            var id_card = jQuery('#DriverRecruitment_id_card').val();
            var domicile = jQuery('#s1').val();
            var register_city = jQuery('#s2').val();
            if (domicile=='请选择' || register_city=='请选择') {
                alert('请选择户籍');
                return false;
            }
            if (!checkIdcard(id_card)) {
                alert('请输入正确的身份证号码');
                return false;
            }
        });

    });
    function checkIdcard(idcard){
        var Errors=new Array(
            "验证通过!",
            "身份证号码位数不对!",
            "身份证号码出生日期超出范围或含有非法字符!",
            "身份证号码校验错误!",
            "身份证地区非法!"
        );
        var area={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"}
        var idcard,Y,JYM;
        var S,M;
        var idcard_array = new Array();
        idcard_array = idcard.split("");
        /*地区检验*/
        if(area[parseInt(idcard.substr(0,2))]==null)
        {
            alert(Errors[4]);
            return false;
        }
        /*身份号码位数及格式检验*/
        switch(idcard.length){
            case 15:
                if ( (parseInt(idcard.substr(6,2))+1900) % 4 == 0 || ((parseInt(idcard.substr(6,2))+1900) % 100 == 0 && (parseInt(idcard.substr(6,2))+1900) % 4 == 0 )){
                    ereg=/^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}$/;//测试出生日期的合法性
                } else {
                    ereg=/^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}$/;//测试出生日期的合法性
                }
                if(ereg.test(idcard)){
                    //alert(Errors[0]+"15");
                    return true;
                }
                else {
                    //alert(Errors[2]);
                    return false;
                }
                break;

            case 18:
                //18位身份号码检测
                //出生日期的合法性检查
                //闰年月日:((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))
                //平年月日:((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))
                if ( parseInt(idcard.substr(6,4)) % 4 == 0 || (parseInt(idcard.substr(6,4)) % 100 == 0 && parseInt(idcard.substr(6,4))%4 == 0 )){
                    ereg=/^[1-9][0-9]{5}19[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/;//闰年出生日期的合法性正则表达式
                } else {
                    ereg=/^[1-9][0-9]{5}19[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/;//平年出生日期的合法性正则表达式
                }
                if(ereg.test(idcard)){//测试出生日期的合法性
                    //计算校验位
                    S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7
                        + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9
                        + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10
                        + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5
                        + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8
                        + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4
                        + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2
                        + parseInt(idcard_array[7]) * 1
                        + parseInt(idcard_array[8]) * 6
                        + parseInt(idcard_array[9]) * 3 ;
                    Y = S % 11;
                    M = "F";
                    JYM = "10X98765432";
                    M = JYM.substr(Y,1);/*判断校验位*/
                    if(M == idcard_array[17]){
                        //alert(Errors[0]+"18");
                        return true; /*检测ID的校验位*/
                    }
                    else {
                        //alert(Errors[3]);
                        return false;
                    }
                }
                else {
                    //alert(Errors[2]);
                    return false;
                }
                break;

            default:
                //alert(Errors[1]);
                return false;
        }
    }
</script>


<script>
    /**
     * 省联动
     */

    window.onload = function() {
        for(i=0;i<s.length-1;i++)
            document.getElementById(s[i]).onchange=new Function("change("+(i+1)+")");
        change(0);
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
    dsy.add("0_4",["呼和浩特","包头","赤峰","呼伦贝尔","鄂尔多斯","乌兰察布","巴彦淖尔","兴安","阿拉善","锡林郭勒"]);
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
    dsy.add("0_22",["成都","广元","绵阳","德阳","南充","广安","遂宁","内江","乐山","自贡","泸州","宜宾","攀枝花","巴中","资阳","眉山","雅安","阿坝","达州","甘孜","凉山"]);
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
    }

</script>
