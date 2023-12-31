<?php
/**
 * 司机报名h5---根据省获取城市列表
 * @author luzhe
 * @version 2014-08-04
 */
$province = isset($params['province']) ? trim($params['province']) : '';
$callback=isset($params['callback'])?$params['callback']:'';

if(empty($province)){
    $ret = array('code' => 1 , 'message' => '请选择省');
    $json_str = json_encode($ret);
    if(isset($callback)&&!empty($callback)){
        $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
}
$provinces = array(
    "北京" => array("北京"),
    "天津" => array("天津"),
    "河北" => array("石家庄","张家口","承德","秦皇岛","唐山","廊坊","衡水","沧州","邢台","邯郸","保定"),
    "山西" => array("太原","朔州","大同","长治","晋城","忻州","晋中","临汾","吕梁","运城"),
    "内蒙古" => array("呼和浩特","包头","赤峰","呼伦贝尔","鄂尔多斯","乌兰察布","巴彦淖尔","兴安","阿拉善","锡林郭勒","通辽"),
    "辽宁" => array("沈阳","朝阳","阜新","铁岭","抚顺","丹东","本溪","辽阳","鞍山","大连","营口","盘锦","锦州","葫芦岛"),
    "吉林" => array("长春","白城","吉林","四平","辽源","通化","白山","延边"),
    "黑龙江" => array("哈尔滨","七台河","黑河","大庆","齐齐哈尔","伊春","佳木斯","双鸭山","鸡西","加格达奇","牡丹江","鹤岗","绥化"),
    "上海" => array("上海"),
    "江苏" => array("南京","徐州","连云港","宿迁","淮安","盐城","扬州","泰州","南通","镇江","常州","无锡","苏州"),
    "浙江" => array("杭州","湖州","嘉兴","舟山","宁波","绍兴","衢州","金华","台州","温州","丽水"),
    "安徽" => array("合肥","宿州","淮北","亳州","阜阳","蚌埠","淮南","滁州","马鞍山","芜湖","铜陵","安庆","黄山","六安","巢湖","池州","宣城"),
    "福建" => array("福州","南平","莆田","三明","泉州","厦门","漳州","龙岩","宁德"),
    "江西" => array("南昌","九江","景德镇","鹰潭","新余","萍乡","赣州","上饶","抚州","宜春","吉安"),
    "山东" => array("济南","聊城","德州","东营","淄博","潍坊","烟台","威海","青岛","日照","临沂","枣庄","济宁","泰安","莱芜","滨州","菏泽"),
    "河南" => array("郑州","三门峡","洛阳","焦作","新乡","鹤壁","安阳","濮阳","开封","商丘","许昌","漯河","平顶山","南阳","信阳","周口","驻马店"),
    "湖北" => array("武汉","十堰","襄樊","荆门","孝感","黄冈","鄂州","黄石","咸宁","荆州","宜昌","随州","恩施","仙桃","天门","潜江","神农架"),
    "湖南" => array("长沙","张家界","常德","益阳","岳阳","株洲","湘潭","衡阳","郴州","永州","邵阳","怀化","娄底","湘西"),
    "广东" => array("广州","清远","韶关","河源","梅州","潮州","汕头","揭阳","汕尾","惠州","东莞","深圳","珠海","中山","江门","佛山","肇庆","云浮","阳江",
		    "茂名","湛江"),
    "广西" => array("南宁","桂林","柳州","梧州","贵港","玉林","钦州","北海","防城港","崇左","百色","河池","来宾","贺州"),
    "海南" => array("海口","三亚"),
    "重庆" => array("重庆"),
    "四川" => array("成都","广元","绵阳","德阳","南充","广安","遂宁","内江","乐山","自贡","泸州","宜宾","攀枝花","巴中","资阳","眉山","雅安","阿坝",
		    "甘孜","凉山","达州"),
    "贵州" => array("贵阳","六盘水","遵义","安顺","毕节","铜仁","黔东南","黔南","黔西南"),
    "云南" => array("昆明","曲靖","玉溪","保山","昭通","丽江","普洱","临沧","宁德","德宏","怒江","楚雄","红河","文山","大理","迪庆","西双版纳"),
    "西藏" => array("拉萨","那曲","昌都","林芝","山南","日喀则","阿里"),
    "陕西" => array("西安","延安","铜川","渭南","咸阳","宝鸡","汉中","安康","商洛"),
    "甘肃" => array("兰州 ","嘉峪关","金昌","白银","天水","武威","酒泉","张掖","庆阳","平凉","定西","陇南","临夏","甘南"),
    "青海" => array("西宁","海东","海北","黄南","玉树","海南","果洛","海西"),
    "宁夏" => array("银川","石嘴山","吴忠","固原","中卫"),
    "新疆" => array("乌鲁木齐","克拉玛依","喀什","阿克苏","和田","吐鲁番","哈密","塔城","阿勒泰","克孜勒","博尔塔拉","昌吉", "伊犁","巴音郭楞","河子",
		   "阿拉尔","五家渠","图木舒克"),
    "香港" => array("香港"),
    "澳门" => array("澳门"),
    "台湾" => array("台湾")
);

$citylist = $provinces[$province];
if(empty($citylist)){
    $ret = array('code' => 1 , 'message' => '请输入正确的省份');
    $json_str = json_encode($ret);
    if(isset($callback)&&!empty($callback)){
        $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
}
$ret = array('code' => 0 , 'message' => '获取成功','citylist' => $citylist);
$json_str = json_encode($ret);
if(isset($callback) && !empty($callback)){
   $json_str = $callback.'('.$json_str.')';
}
echo $json_str;Yii::app()->end();
