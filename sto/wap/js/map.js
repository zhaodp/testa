function addClient(latitude, longitude){
    var point = new BMap.Point(longitude, latitude);
    var myIcon = new BMap.Icon("http://openapi.baidu.com/map/images/us_mk_icon.png", new BMap.Size(22, 21), {
		offset: new BMap.Size(22, 21),
		imageOffset: new BMap.Size(0, 0) 
	});
    

	translateCallback = function (point){
	    var marker = new BMap.Marker(point, {icon:myIcon});
	    map.addOverlay(marker);
	}    

    BMap.Convertor.translate(point,2,translateCallback);    
}


function addPoint(latitude, longitude, message, title){
	var marker;
    var point = new BMap.Point(longitude, latitude);

	translateCallback = function (point){
	    marker = new BMap.Marker(point);
		var opts = {title : '<span style="font-size:14px;color:#0A8021">' + title + '</span>'};
		var infoWindow = new BMap.InfoWindow(message, opts);  // 创建信息窗口对象

	    map.addOverlay(marker);
	    marker.addEventListener("click", function(){          
	       this.openInfoWindow(infoWindow);  
	    });
	}    

    BMap.Convertor.translate(point,2,translateCallback);    
}

function addPointWithPic(latitude, longitude, message, index){
    var point = new BMap.Point(longitude, latitude);
    var myIcon = new BMap.Icon("http://www.edaijia.cn/v2/sto/classic/i/us_cursor.gif", new BMap.Size(23, 21), {
		offset: new BMap.Size(10, 21),
		imageOffset: new BMap.Size(0 - index * 23,0) 
	});
	
	var marker = new BMap.Marker(point, {icon:myIcon});
	var opts = {};
	
	var infoWindow = new BMap.InfoWindow(message, opts);  // 创建信息窗口对象
	
	map.addOverlay(marker);
	
	marker.addEventListener("click", function(){          
		this.openInfoWindow(infoWindow);  
	});
}

function addPointIndex(latitude, longitude, index, message){
  var point = new BMap.Point(longitude, latitude);

  var myIcon = new BMap.Icon("http://api.map.baidu.com/img/markers.png", new BMap.Size(23, 25), {
    offset: new BMap.Size(10, 25),
    imageOffset: new BMap.Size(0, 0 - index * 25) 
  });
  var marker = new BMap.Marker(point, {icon: myIcon});
  map.addOverlay(marker);

  var opts = {
    /*
    width : 250,     // 信息窗口宽度
    height: 100,     // 信息窗口高度
    title : title // 信息窗口标题
     */
    }

    var infoWindow = new BMap.InfoWindow(message, opts);  // 创建信息窗口对象

    marker.addEventListener("click", function(){          
       this.openInfoWindow(infoWindow);  
    });
}
