var hexcase=0;function hex_md5(a){return rstr2hex(rstr_md5(str2rstr_utf8(a)))}function hex_hmac_md5(a,b){return rstr2hex(rstr_hmac_md5(str2rstr_utf8(a),str2rstr_utf8(b)))}function md5_vm_test(){return hex_md5("abc").toLowerCase()=="900150983cd24fb0d6963f7d28e17f72"}function rstr_md5(a){return binl2rstr(binl_md5(rstr2binl(a),a.length*8))}function rstr_hmac_md5(c,f){var e=rstr2binl(c);if(e.length>16){e=binl_md5(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binl_md5(a.concat(rstr2binl(f)),512+f.length*8);return binl2rstr(binl_md5(d.concat(g),512+128))}function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}function rstr2binl(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(c%32)}return a}function binl2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(c%32))&255)}return a}function binl_md5(p,k){p[k>>5]|=128<<((k)%32);p[(((k+64)>>>9)<<4)+14]=k;var o=1732584193;var n=-271733879;var m=-1732584194;var l=271733878;for(var g=0;g<p.length;g+=16){var j=o;var h=n;var f=m;var e=l;o=md5_ff(o,n,m,l,p[g+0],7,-680876936);l=md5_ff(l,o,n,m,p[g+1],12,-389564586);m=md5_ff(m,l,o,n,p[g+2],17,606105819);n=md5_ff(n,m,l,o,p[g+3],22,-1044525330);o=md5_ff(o,n,m,l,p[g+4],7,-176418897);l=md5_ff(l,o,n,m,p[g+5],12,1200080426);m=md5_ff(m,l,o,n,p[g+6],17,-1473231341);n=md5_ff(n,m,l,o,p[g+7],22,-45705983);o=md5_ff(o,n,m,l,p[g+8],7,1770035416);l=md5_ff(l,o,n,m,p[g+9],12,-1958414417);m=md5_ff(m,l,o,n,p[g+10],17,-42063);n=md5_ff(n,m,l,o,p[g+11],22,-1990404162);o=md5_ff(o,n,m,l,p[g+12],7,1804603682);l=md5_ff(l,o,n,m,p[g+13],12,-40341101);m=md5_ff(m,l,o,n,p[g+14],17,-1502002290);n=md5_ff(n,m,l,o,p[g+15],22,1236535329);o=md5_gg(o,n,m,l,p[g+1],5,-165796510);l=md5_gg(l,o,n,m,p[g+6],9,-1069501632);m=md5_gg(m,l,o,n,p[g+11],14,643717713);n=md5_gg(n,m,l,o,p[g+0],20,-373897302);o=md5_gg(o,n,m,l,p[g+5],5,-701558691);l=md5_gg(l,o,n,m,p[g+10],9,38016083);m=md5_gg(m,l,o,n,p[g+15],14,-660478335);n=md5_gg(n,m,l,o,p[g+4],20,-405537848);o=md5_gg(o,n,m,l,p[g+9],5,568446438);l=md5_gg(l,o,n,m,p[g+14],9,-1019803690);m=md5_gg(m,l,o,n,p[g+3],14,-187363961);n=md5_gg(n,m,l,o,p[g+8],20,1163531501);o=md5_gg(o,n,m,l,p[g+13],5,-1444681467);l=md5_gg(l,o,n,m,p[g+2],9,-51403784);m=md5_gg(m,l,o,n,p[g+7],14,1735328473);n=md5_gg(n,m,l,o,p[g+12],20,-1926607734);o=md5_hh(o,n,m,l,p[g+5],4,-378558);l=md5_hh(l,o,n,m,p[g+8],11,-2022574463);m=md5_hh(m,l,o,n,p[g+11],16,1839030562);n=md5_hh(n,m,l,o,p[g+14],23,-35309556);o=md5_hh(o,n,m,l,p[g+1],4,-1530992060);l=md5_hh(l,o,n,m,p[g+4],11,1272893353);m=md5_hh(m,l,o,n,p[g+7],16,-155497632);n=md5_hh(n,m,l,o,p[g+10],23,-1094730640);o=md5_hh(o,n,m,l,p[g+13],4,681279174);l=md5_hh(l,o,n,m,p[g+0],11,-358537222);m=md5_hh(m,l,o,n,p[g+3],16,-722521979);n=md5_hh(n,m,l,o,p[g+6],23,76029189);o=md5_hh(o,n,m,l,p[g+9],4,-640364487);l=md5_hh(l,o,n,m,p[g+12],11,-421815835);m=md5_hh(m,l,o,n,p[g+15],16,530742520);n=md5_hh(n,m,l,o,p[g+2],23,-995338651);o=md5_ii(o,n,m,l,p[g+0],6,-198630844);l=md5_ii(l,o,n,m,p[g+7],10,1126891415);m=md5_ii(m,l,o,n,p[g+14],15,-1416354905);n=md5_ii(n,m,l,o,p[g+5],21,-57434055);o=md5_ii(o,n,m,l,p[g+12],6,1700485571);l=md5_ii(l,o,n,m,p[g+3],10,-1894986606);m=md5_ii(m,l,o,n,p[g+10],15,-1051523);n=md5_ii(n,m,l,o,p[g+1],21,-2054922799);o=md5_ii(o,n,m,l,p[g+8],6,1873313359);l=md5_ii(l,o,n,m,p[g+15],10,-30611744);m=md5_ii(m,l,o,n,p[g+6],15,-1560198380);n=md5_ii(n,m,l,o,p[g+13],21,1309151649);o=md5_ii(o,n,m,l,p[g+4],6,-145523070);l=md5_ii(l,o,n,m,p[g+11],10,-1120210379);m=md5_ii(m,l,o,n,p[g+2],15,718787259);n=md5_ii(n,m,l,o,p[g+9],21,-343485551);o=safe_add(o,j);n=safe_add(n,h);m=safe_add(m,f);l=safe_add(l,e)}return Array(o,n,m,l)}function md5_cmn(h,e,d,c,g,f){return safe_add(bit_rol(safe_add(safe_add(e,h),safe_add(c,f)),g),d)}function md5_ff(g,f,k,j,e,i,h){return md5_cmn((f&k)|((~f)&j),g,f,e,i,h)}function md5_gg(g,f,k,j,e,i,h){return md5_cmn((f&j)|(k&(~j)),g,f,e,i,h)}function md5_hh(g,f,k,j,e,i,h){return md5_cmn(f^k^j,g,f,e,i,h)}function md5_ii(g,f,k,j,e,i,h){return md5_cmn(k^(f|(~j)),g,f,e,i,h)}function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};
var $ = function(sid){
	return document.querySelector(sid);
};
$.deffered = function(){
};
$.deffered.prototype.resolve = function(params){
	this.callback && this.callback(params);
};
$.deffered.prototype.then = function(cbk){
	this.callback=cbk;
};
$.getDeffered = function(){
	return new $.deffered();
};
$.getJSONP = function(url,error){
	var cbnum="cb"+$.getJSONP.counter++;
	var cbname="$.getJSONP."+cbnum;
	if(url.indexOf("?")===-1){
		url+="?callback="+cbname;
	}else{
		url+="&callback="+cbname;
	}
	var script=document.createElement("script");
	var deffer = $.getDeffered();
	timeoutTimer = setTimeout( function(){
		error && error();
	},3000);
	$.getJSONP[cbnum]=function(response){
		try{
			clearTimeout(timeoutTimer);
			deffer.resolve(response);
		}finally{
			delete $.getJSONP[cbnum];
			script.parentNode.removeChild(script);
		}
	};
	script.src=url;
	document.body.appendChild(script);
	return deffer;
};
$.getJSONP.counter=0;
$.extend = function(target,source){
	for(name in source){
		target[name] = source[name];
	};
	return target;
};
var easyTemplate = function(id,d){
	if(!id){return '';}
	else{s=document.getElementById(id).innerHTML;}
	if(s!==easyTemplate.template){
		easyTemplate.template = s;
		easyTemplate.aStatement = easyTemplate.parsing(easyTemplate.separate(s));
	}
	var aST = easyTemplate.aStatement;
	var process = function(d2){
		if(d2){d = d2;}
		return arguments.callee;
	};
	process.toString = function(){
		return (new Function(aST[0],aST[1]))(d);
	};
	return process;
};
easyTemplate.separate = function(s){
	var r = /\\'/g;
	var sRet = s.replace(/(<(\/?)#(.*?(?:\(.*?\))*)>)|(')|([\r\n\t])|(\$\{([^\}]*?)\})/g,function(a,b,c,d,e,f,g,h){
		if(b){return '{|}'+(c?'-':'+')+d+'{|}';}
		if(e){return '\\\'';}
		if(f){return '';}
		if(g){return '\'+('+h.replace(r,'\'')+')+\'';}
	});
	return sRet;
};
easyTemplate.parsing = function(s){
	var mName,vName,sTmp,aTmp,sFL,sEl,aList,aStm = ['var aRet = [];'];
	aList = s.split(/\{\|\}/);
	var r = /\s/;
	while(aList.length){
		sTmp = aList.shift();
		if(!sTmp){continue;}
		sFL = sTmp.charAt(0);
		if(sFL!=='+'&&sFL!=='-'){
			sTmp = '\''+sTmp+'\'';aStm.push('aRet.push('+sTmp+');');
			continue;
		}
		aTmp = sTmp.split(r);
		switch(aTmp[0]){
			case '+macro':mName = aTmp[1];vName = aTmp[2];aStm.push('aRet.push("<!--'+mName+' start--\>");');break;
			case '-macro':aStm.push('aRet.push("<!--'+mName+' end--\>");');break;
			case '+if':aTmp.splice(0,1);aStm.push('if'+aTmp.join(' ')+'{');break;
			case '+elseif':aTmp.splice(0,1);aStm.push('}else if'+aTmp.join(' ')+'{');break;
			case '-if':aStm.push('}');break;
			case '+else':aStm.push('}else{');break;
			case '+list':aStm.push('if('+aTmp[1]+'&&'+aTmp[1]+'.constructor === Array){with({i:0,l:'+aTmp[1]+'.length,'+aTmp[3]+'_index:0,'+aTmp[3]+':null}){for(i=l;i--;){'+aTmp[3]+'_index=(l-i-1);'+aTmp[3]+'='+aTmp[1]+'['+aTmp[3]+'_index];');break;
			case '-list':aStm.push('}}}');break;
			default:break;
		}
	}
	aStm.push('return aRet.join("");');
	if(!vName){aStm.unshift('var data = arguments[0];');}
	return [vName,aStm.join('')];
};
Common = {};
Common.context = {};
Common.controllers = {};
Common.params={};

var Class = {
    create: function () {
        return function () {
            this.initialize.apply(this, arguments);
        };
    }
};
var Model = Class.create();
Model.prototype.initialize = function(params){
	this.attributes = params;
};

var MD5KEY = '0c09bc02-c74e-11e2-a9da-00163e1210d9';
var dataURL = "http://api.edaijia.cn/rest/";
Common.config = {
    appkey: '51000031',
    ver: 3,
    udid: 'h5__94654d843d04d93677be9b3a31779b22',
    from:'wap',
    macaddress:'12:34:56:78:9A:BC'
};

Common.info={
	lng: "",
	lat: "",
	device:"",
	version:"1.0.0"
};
Common.distribute = function (action) {
	if (window.currentAction) {
        window.currentAction.destroy();
    }
    try{
    	window.currentAction = new Common.controllers[action]($("#main"));
		$("#promptBox").show();
    }catch(e){
    	console.log(e.message);
    }
};

Common.increseId = function () {
    var count = 0;
    function increse() {
        return ++count;
    };
    return increse;
};
Common.getId = Common.increseId();

function isArray(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
};

Common.getTimestamp = function () {
    return (new Date()).valueOf() + "";
};

var CU = Common.CU = {
    isSucceed: function (data) {
    	if(data.code!=0){
			//alert(data.message);
			var srt = easyTemplate("alert-tpl", data);
			$("#modalBox").innerHTML = srt;
			$("a.btn_close").onclick = function () {$("#modalBox").innerHTML="";}
    	}
        return data.code==0;
    },
    dateFormat: function (date, format) {
        format = format || 'yyyy-MM-dd hh:mm:ss';
        var o = {
            "M+": date.getMonth() + 1,
            "d+": date.getDate(),
            "h+": date.getHours(),
            "m+": date.getMinutes(),
            "s+": date.getSeconds(),
            "q+": Math.floor((date.getMonth() + 3) / 3),
            "S": date.getMilliseconds()
        };
        if (/(y+)/.test(format)) {
            format = format.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
        }
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(format)) {
                format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
            }
        }
        return format;
    },
    getSig: function (param) {
        var paramStr = [], paramStrSorted = [];
        for (var n in param) {
            paramStr.push(n);
        }
        paramStr = paramStr.sort();
        for (var i=0,len = paramStr.length;i<len;i++){
        	paramStrSorted.push(paramStr[i] + param[paramStr[i]]);
        }
        var text = paramStrSorted.join('') + MD5KEY;
        return hex_md5(text);
    }
};

Common.stringify = function (data) {
    var value = "";
    for (prop in data) {
        value += prop + "=" + data[prop] + "&";
    }
    return value.substr(0, value.length - 1);
};

Common.postRequest = function (model) {
    var model = model.attributes ? model.attributes : model;
    var req = $.extend({}, Common.config);
    req.method = model.method;
    model.gps_type && (req.gps_type = model.gps_type);
    req = $.extend(req, model.params);
    req.timestamp = CU.dateFormat(new Date());
	req.sig = CU.getSig(req);
	var eurl=dataURL+"?"+Common.stringify(req);
	return $.getJSONP(eurl,function(){
		alert("network error!");
	});
};

Common.dataHandle = function (data, idx, redirectAction) {
    var idx = idx || 0;
    if (data) {
        var _r = data.response;
        if (_r && _r.length && _r[idx]) {
            if (_r[idx].status == "s") {
                var rst = _r[idx].result;
                if (rst) {
                    return rst;
                }
            } else {
                Common.showMsg(_r[idx].error);
            }
        }
    }
    return null;
};

Common.isDataLength = function (data) {
    if (data) {
        var len = data.response[0].result.data.length;
        return len;
    }
    return null;
};

Common.showMsg = function () {

};

Common.switchL = function (el) {
    this.local = this.local || new Local();
    lan = lan || "C";
    this.local.switchLInContainer(lan, el);
};
Interface = {};
Interface.tabArr = [];
Interface.email = "ok";

Common.TimeCounter = function(params){
	this.getParams(params);
	this.times = this.second;
};
Common.TimeCounter.prototype.start=function(){
	var _t = this;
	_t.beforestart && _t.beforestart();
	_t.timer = window.setInterval(function(){
		if(_t.times && _t.times>1){
			_t.times--;
			_t.callback && _t.callback();
		}else{
			_t.end && _t.end();
			window.clearInterval(_t.timer);
		}
	},1000);
};
Common.TimeCounter.prototype.getParams=function(params){
	this.second = params.second || 0;
	this.beforestart = params.beforestart || null;
	this.end = params.end || null;
	this.callback = params.callback || null;
};
Common.TimeCounter.prototype.stop=function(){
	this.end && this.end();
	window.clearInterval(this.timer);
};

Common.CutString = function(str,n){
	var arr=str.toString().split(".");
	var tt=arr[0]+"."+arr[1].slice(0,n);
	return tt;
};
Common.UrlGet=function() {
	var args = new Object();
	var query = location.search.substring(1);
	if(query=="") alert("无坐标参数");
	var pairs = query.split("&");
	for (var i = 0; i < pairs.length; i++) {
	  var pos = pairs[i].indexOf('=');
	  if (pos == -1) continue;
	  var argname = pairs[i].substring(0, pos);
	  var value = pairs[i].substring(pos + 1);
	  value = decodeURIComponent(value);
	  args[argname] = value;
	}
	return args;
}
if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(function (position) {
        var mPriceList = new Model({
            method:"customer.city.pricelist",
            gps_type:"wgs84",
            params: {
                lng: position.coords.longitude,
                lat:position.coords.latitude
            }
        });
        Common.postRequest(mPriceList).then(function (data) {
            var html = easyTemplate("priceList-tpl", data);
            $("#edaijia_price").innerHTML = html;
            $("#edaijia_price").style.display='block';
        });
    },function(error){
        switch(error.code){
            case error.TIMEOUT :
                $("#edaijia_price").style.display='block';
                break;
            case error.PERMISSION_DENIED :
                $("#edaijia_price").style.display='block';
                break;
            case error.POSITION_UNAVAILABLE :
                $("#edaijia_price").style.display='block';
                break;
            default :
                $("#edaijia_price").style.display='block';
                break;
        }
    },{enableHighAccuracy:true,maximumAge:3000,timeout:2000});
}else{
    jQuery("#edaijia_price").css('display','block');
}