<?php
$this->breadcrumbs=array('ActivitySeller'=>array('index'), 'Create',);
?>
<h1>商家信息登记</h1>
<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'activity-seller-create-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error',
        'htmlOptions'=>array(
            'enctype'=>'multipart/form-data',
            'onsubmit' => 'return false',
            'onkeypress' => 'if(event.keyCode == 13){send();}'
        ),
    )); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="span4">
	        <div>
                <?php echo '商家姓名：'; ?>
                <?php echo $form->textField($model, 'name', array('size' => 50, 'maxlength' =>50)); ?>
                <?php echo $form->error($model, 'name'); ?>
            </div>
            <div>
                <?php echo '商家地址：'; ?>
                <select name="ActivitySeller[province]" id="s1" style="width:110px;"></select>
                <select name="ActivitySeller[city]" id="s2" style="width:110px;"></select>

            </div>
            <div>
                <?php echo '详细地址：'; ?>
                <?php echo $form->textField($model, 'details', array('size' =>50, 'maxlength' =>50,'value'=>'**区**街**号')); ?>
                <?php echo $form->error($model, 'details'); ?>
            </div>
            <div>
                <?php echo '所属行业：'; ?>
                <?php echo $form->dropDownList($model, 'industry', ActivitySeller::$industry); ?>
                <?php echo $form->error($model, 'industry'); ?>
            </div>
            <div>
                <?php echo '联系电话：'; ?>
                <?php echo $form->textField($model, 'phone', array('size' =>15, 'maxlength' =>15)); ?>
                <?php echo $form->error($model, 'phone'); ?>
            </div>
            <div>
                <?php echo '验证码：'; ?>
                <?php echo $form->textField($model, 'code', array('size' =>6, 'maxlength' =>6)); ?>
                <?php echo $form->error($model, 'code'); ?>
            </div>

           <div class="span2">
		 <?php echo CHtml::submitButton('保存', array('class' => 'btn btn-success btn-block')); ?>
           </div>
    </div>

    <?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
    /**
   $('#customer_invoice-create-form').submit(function() {
      	if ($('#CustomerInvoice_customer_phone').val()==''){
		alert('客户电话不能为空');
		return false;
	}

	if ($('#CustomerInvoice_title').val()==''){
                alert('抬头不能为空');
                return false;
        }
	if ($('#CustomerInvoice_contact').val()==''){
                alert('收件人不能为空');
                return false;
        }
	if ($('#CustomerInvoice_telephone').val()==''){
                alert('收件人电话不能为空');
                return false;
        }
	if ($('#CustomerInvoice_address').val()==''){
                alert('收件人地址不能为空');
                return false;
        }
	return true;
    });
**/
</script>
<script>
    /**
     * 省市联动
     */
    window.onload = function () {
        for (i = 0; i < s.length - 1; i++){
            document.getElementById(s[i]).onchange = new Function("change(" + (i + 1) + ")");
        }
        change(0);
        setup();
    }

    function Dsy() {
        this.Items = {};
    }
    Dsy.prototype.add = function (id, iArray) {
        this.Items[id] = iArray;
    }
    Dsy.prototype.Exists = function (id) {
        if (typeof(this.Items[id]) == "undefined") return false;
        return true;
    }

    function change(v) {
        var str = "0";
        for (i = 0; i < v; i++) {
            str += ("_" + (document.getElementById(s[i]).selectedIndex - 1));
        }
        var ss = document.getElementById(s[v]);
        with (ss) {
            length = 0;
            options[0] = new Option(opt0[v], opt0[v]);
            if (v && document.getElementById(s[v - 1]).selectedIndex > 0 || !v) {
                if (dsy.Exists(str)) {
                    ar = dsy.Items[str];
                    for (i = 0; i < ar.length; i++)options[length] = new Option(ar[i], ar[i]);
                    if (v)options[1].selected = true;
                }
            }
            if (++v < s.length) {
                change(v);
            }
        }
    }

    var dsy = new Dsy();
    dsy.add("0", ["北京", "天津", "河北", "山西", "内蒙古", "辽宁", "吉林", "黑龙江", "上海", "江苏", "浙江", "安徽", "福建", "江西", "山东", "河南", "湖北", "湖南", "广东", "广西", "海南", "重庆", "四川", "贵州", "云南", "西藏", "陕西", "甘肃", "青海", "宁夏", "新疆", "香港", "澳门", "台湾"]);
    dsy.add("0_0", ["北京"]);
    dsy.add("0_1", ["天津"]);
    dsy.add("0_2", ["石家庄", "张家口", "承德", "秦皇岛", "唐山", "廊坊", "衡水", "沧州", "邢台", "邯郸", "保定"]);
    dsy.add("0_3", ["太原", "朔州", "大同", "长治", "晋城", "忻州", "晋中", "临汾", "吕梁", "运城"]);
    dsy.add("0_4", ["呼和浩特", "包头", "赤峰", "呼伦贝尔", "鄂尔多斯", "乌兰察布", "巴彦淖尔", "兴安", "阿拉善", "锡林郭勒"]);
    dsy.add("0_5", ["沈阳", "朝阳", "阜新", "铁岭", "抚顺", "丹东", "本溪", "辽阳", "鞍山", "大连", "营口", "盘锦", "锦州", "葫芦岛"]);
    dsy.add("0_6", ["长春", "白城", "吉林", "四平", "辽源", "通化", "白山", "延边"]);
    dsy.add("0_7", ["哈尔滨", "七台河", "黑河", "大庆", "齐齐哈尔", "伊春", "佳木斯", "双鸭山", "鸡西", "加格达奇", "牡丹江", "鹤岗", "绥化"]);
    dsy.add("0_8", ["上海"]);
    dsy.add("0_9", ["南京", "徐州", "连云港", "宿迁", "淮安", "盐城", "扬州", "泰州", "南通", "镇江", "常州", "无锡", "苏州"]);
    dsy.add("0_10", ["杭州", "湖州", "嘉兴", "舟山", "宁波", "绍兴", "衢州", "金华", "台州", "温州", "丽水"]);
    dsy.add("0_11", ["合肥", "宿州", "淮北", "亳州", "阜阳", "蚌埠", "淮南", "滁州", "马鞍山", "芜湖", "铜陵", "安庆", "黄山", "六安", "巢湖", "池州", "宣城"]);
    dsy.add("0_12", ["福州", "南平", "莆田", "三明", "泉州", "厦门", "漳州", "龙岩", "宁德"]);
    dsy.add("0_13", ["南昌", "九江", "景德镇", "鹰潭", "新余", "萍乡", "赣州", "上饶", "抚州", "宜春", "吉安"]);
    dsy.add("0_14", ["济南", "聊城", "德州", "东营", "淄博", "潍坊", "烟台", "威海", "青岛", "日照", "临沂", "枣庄", "济宁", "泰安", "莱芜", "滨州", "菏泽"]);
    dsy.add("0_15", ["郑州", "三门峡", "洛阳", "焦作", "新乡", "鹤壁", "安阳", "濮阳", "开封", "商丘", "许昌", "漯河", "平顶山", "南阳", "信阳", "周口", "驻马店"]);
    dsy.add("0_16", ["武汉", "十堰", "襄樊", "荆门", "孝感", "黄冈", "鄂州", "黄石", "咸宁", "荆州", "宜昌", "随州", "恩施", "仙桃", "天门", "潜江", "神农架"]);
    dsy.add("0_17", ["长沙", "张家界", "常德", "益阳", "岳阳", "株洲", "湘潭", "衡阳", "郴州", "永州", "邵阳", "怀化", "娄底", "湘西"]);
    dsy.add("0_18", ["广州", "清远", "韶关", "河源", "梅州", "潮州", "汕头", "揭阳", "汕尾", "惠州", "东莞", "深圳", "珠海", "中山", "江门", "佛山", "肇庆", "云浮", "阳江", "茂名", "湛江"]);
    dsy.add("0_19", ["南宁", "桂林", "柳州", "梧州", "贵港", "玉林", "钦州", "北海", "防城港", "崇左", "百色", "河池", "来宾", "贺州"]);
    dsy.add("0_20", ["海口", "三亚"]);
    dsy.add("0_21", ["重庆"]);
    dsy.add("0_22", ["成都", "广元", "绵阳", "德阳", "南充", "广安", "遂宁", "内江", "乐山", "自贡", "泸州", "宜宾", "攀枝花", "巴中", "资阳", "眉山", "雅安", "阿坝", "甘孜", "凉山"]);
    dsy.add("0_23", ["贵阳", "六盘水", "遵义", "安顺", "毕节", "铜仁", "黔东南", "黔南", "黔西南"]);
    dsy.add("0_24", ["昆明", "曲靖", "玉溪", "保山", "昭通", "丽江", "普洱", "临沧", "宁德", "德宏", "怒江", "楚雄", "红河", "文山", "大理", "迪庆", "西双版纳"]);
    dsy.add("0_25", ["拉萨", "那曲", "昌都", "林芝", "山南", "日喀则", "阿里"]);
    dsy.add("0_26", ["西安", "延安", "铜川", "渭南", "咸阳", "宝鸡", "汉中", "安康", "商洛"]);
    dsy.add("0_27", ["兰州 ", "嘉峪关", "金昌", "白银", "天水", "武威", "酒泉", "张掖", "庆阳", "平凉", "定西", "陇南", "临夏", "甘南"]);
    dsy.add("0_28", ["西宁", "海东", "海北", "黄南", "玉树", "海南", "果洛", "海西"]);
    dsy.add("0_29", ["银川", "石嘴山", "吴忠", "固原", "中卫"]);
    dsy.add("0_30", ["乌鲁木齐", "克拉玛依", "喀什", "阿克苏", "和田", "吐鲁番", "哈密", "塔城", "阿勒泰", "克孜勒", "博尔塔拉", "昌吉", "伊犁", "巴音郭楞", "河子", "阿拉尔", "五家渠", "图木舒克"]);
    dsy.add("0_31", ["香港"]);
    dsy.add("0_32", ["澳门"])
    dsy.add("0_33", ["台湾"])

    var s = ["s1", "s2"];
    var opt0 = ["请选择", "请选择"];

    function setup() {
        for (i = 0; i < s.length - 1; i++){
            document.getElementById(s[i]).onchange = new Function("change(" + (i + 1) + ")");
        }
        change(0);
    }

    (function () {
        if (document.readyState && document.readyState == 'complete') {
            console.log(s);
            for (i = 0; i < s.length - 1; i++){
                document.getElementById(s[i]).onchange = new Function("change(" + (i + 1) + ")");
            }
            change(0);
            setup();
        }
    })();
</script>

                     
