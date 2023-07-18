hojo.provide("icallcenter.callProcessor");
hojo.require("hojo.io.script");

hojo.declare("icallcenter.callProcessor", null, {
    _phone: null,

    constructor: function (phone) {
        this._phone = phone;
        var evtHandle = this._phone.register("EvtRing", this, "onRing");
        this._phone._handles.push(evtHandle);
        var evtHandle = this._phone.register("EvtHangup", this, "onHangup");
        this._phone._handles.push(evtHandle);
		evtHandle = this._phone.register("EvtDialing", this, "onDialing");
		this._phone._handles.push(evtHandle);
		evtHandle = this._phone.register("EvtPeerStatusChanged", this, "peerStatusChanged"); 
		this._phone._handles.push(evtHandle);
		evtHandle = this._phone.register("EvtCallStatusChanged", this, "callStatusChanged"); 
		this._phone._handles.push(evtHandle);
		evtHandle = this._phone.register("EvtConnected", this, "EvtConnected"); 
		this._phone._handles.push(evtHandle);
		evtHandle = this._phone.register("EvtLogon", this, "EvtLogon"); 
		this._phone._handles.push(evtHandle);
		evtHandle = this._phone.register("EvtMonitorQueue", this, "EvtMonitorQueue"); 
		this._phone._handles.push(evtHandle);
    },
    onRing: function (data) {
    	var callsheetId = data.callSheetId;
		var agent = data.agent;
		var callNo = data.originCallNo;
		var calledNo = data.originCalledNo;
		var callType = data.callType; 
		var status = data.status;
		var ringTime= data.offeringTime;
		var beginTime= "";
		var endTime= "";
		var monitorFilename= "";
		hojo.byId("icallcenter.dialout.input").value = callNo;
		var phoneJson = {
	    		Command: "Action",
	    		Action: "Ring",
	    		ActionID: "Ring"+Math.random(),
	    		CallsheetId: callsheetId,
	    		CallNo: callNo,
	    		CalledNo: calledNo,
	    		CallType: callType,
	    		RingTime: ringTime,
	    		Agent: agent,
	    		Status: status
		};
		this.sendAction(phoneJson);
		//location.href = "index.php?r=client/dispatch&phone="+callNo+"&callid="+callsheetId;
		//$("#cru-frame").attr("src","index.php?r=client/dispatch&phone="+callNo+"&callid="+callsheetId+"&asDialog=1");
		//$("#cru-dialog").dialog("open");
		//控制弹窗
		url = '';
		title = callNo;
		if (calledNo.lastIndexOf('64149599') > -1
		        || calledNo.lastIndexOf('58103540') > -1
		        || calledNo.lastIndexOf('58103539') > -1
                || calledNo.lastIndexOf('64149596') > -1) {
		    url = "index.php?r=client/dispatch&phone="+callNo+"&callid="+callsheetId+"&dialog=1";
		} else if (calledNo.lastIndexOf('64149598') > -1
		        || calledNo.lastIndexOf('58103537') > -1) {
		    url = "index.php?r=client/service&phone="+callNo+"&callid="+callsheetId+"&dialog=1";
            title = '咨询';
		}
		
		addRing(callsheetId,title,url);
    },
    
    onHangup: function(data) {
//		var callsheetId = data.callSheetId;
//		var agent = data.agent;
//		var callNo = data.originCallNo;
//		var calledNo = data.originCalledNo;
//		var callType = data.callType;
//		var status = data.status;
//		var ringTime= data.ringTime;
//		var beginTime= data.beginTime;
//		var endTime= data.endTime;
//		var monitorFilename= data.data.MonitorFilename;
		//console.log("agent:" + agent +";callNo:" + callNo+";calledNo:"+calledNo+";callType:"+callType+";status:"+status+";ringTime:"+ringTime+";beginTime:"+beginTime+";endTime:"+endTime+";monitorFilename:"+monitorFilename);
//	   	var phoneJson = {
//	    		Command: "Action",
//	    		Action: "Hangup",
//	    		ActionID: "Hangup"+Math.random(),
//	    		CallsheetId: callsheetId,
//	    		CallNo: callNo,
//	    		CalledNo: calledNo,
//	    		CallType: callType,
//	    		RingTime: ringTime,
//	    		Agent: agent,
//	    		Status: status,
//	    		BeginTime: beginTime,
//	    		EndTime: endTime,
//	    		MonitorFilename: monitorFilename
//		};
//		phone.setBusy(true,'1');
//	   	this.sendAction(phoneJson);
    },
    
    onDialing: function(data) {
		var callsheetId = data.callSheetId;
		var agent = data.agent;
		var callNo = data.originCallNo;//锟斤拷锟斤拷
		var calledNo = data.originCalledNo;//锟斤拷锟斤拷
		var callType = data.callType;
		var status = data.status;
		var ringTime= data.offeringTime;
		var beginTime= "";
		var endTime= "";
		var monitorFilename= "";
	   	var phoneJson = {
	    		Command: "Action",
	    		Action: "Dialing",
	    		ActionID: "Dialing"+Math.random(),
	    		CallsheetId: callsheetId,
	    		CallNo: callNo,
	    		CalledNo: calledNo,
	    		CallType: callType,
	    		RingTime: ringTime,
	    		Agent: agent,
	    		Status: status,
	    		BeginTime: beginTime,
	    		EndTime: endTime,
	    		MonitorFilename: monitorFilename
		};
	   	this.sendAction(phoneJson);
    },
    
    EvtConnected: function(data) {
		var callsheetId = data.callSheetId;
		var agent = data.agent;
		var callNo = data.originCallNo;//锟斤拷锟斤拷
		var calledNo = data.originCalledNo;//锟斤拷锟斤拷
		var callType = data.callType;
		var status = data.status;
		var ringTime= data.offeringTime;
		var beginTime= data.beginTime;
		var endTime= "";
		var monitorFilename= "";
	   	var phoneJson = {
	    		Command: "Action",
	    		Action: "Connected",
	    		ActionID: "Connected"+Math.random(),
	    		CallsheetId: callsheetId,
	    		CallNo: callNo,
	    		CalledNo: calledNo,
	    		CallType: callType,
	    		RingTime: ringTime,
	    		Agent: agent,
	    		Status: status,
	    		BeginTime: beginTime,
	    		EndTime: endTime,
	    		MonitorFilename: monitorFilename
		};
	   	this.sendAction(phoneJson);
    },
    
    EvtLogon: function(data) {
    	var status = data; 
	   	var phoneJson = {
	    		Command: "Action",
	    		Action: "Logon",
	    		ActionID: "Logon" + Math.random(),
	    		Status: status
		};
	   	//this.sendAction(phoneJson);
    },
    
    EvtMonitorQueue: function(data) {
//    	var queueNumber = data;
//    	//console.log(data);
//    	
//	   	var phoneJson = {
//	    		Command: "Action",
//	    		Action: "Queue",
//	    		ActionID: "Queue"+Math.random(),
//	    		queueWaitCount: data.queueWaitCount,
//	    		busyAgentCount: data.busyAgentCount,
//	    		idleAgentCount: data.idleAgentCount,
//	    		totalAgentCount: data.totalAgentCount,
//	    		totalCalls: data.totalCalls
//		};
// 		//this.sendAction(phoneJson);
// 		//var call_lose = parseInt(data.abadonedCalls/data.totalCalls*100);
// 		var call_lose = data.abadonedCalls;
// 		if(data.DisplayName=="北京"){
// 			$('input#beijing').val(data.queueWaitCount);
// 		}else{
// 			$('input#outside').val(data.queueWaitCount);
// 		}
// 		all_queueWaitCount = parseInt($('input#beijing').val()) + parseInt($('input#outside').val());
// 		$('h3.alert').html('队列：' + all_queueWaitCount);
    },
    
    
    peerStatusChanged: function(data) {
//    	var peerStatus = data; 
//	   	var phoneJson = {
//	    		Command: "Action",
//	    		Action: "Peer",
//	    		ActionID: "Peer" + Math.random(),
//	    		Status: peerStatus
//		};
//	   	this.sendAction(phoneJson);
    },
    
    callStatusChanged: function(data) {
//    	var peerStatus = data; 
//	   	var phoneJson = {
//	    		Command: "Action",
//	    		Action: "Call",
//	    		ActionID: "Call" + Math.random(),
//	    		Status: peerStatus
//		};
//	   	this.sendAction(phoneJson);
    },
    
    sendAction: function(json) {
    	//console.log(json['Action']);
    	//2013-08-02 废弃代码 dayuer
//	    $.ajax({url: "index.php",
//        		data: {r:'client/ajax',method:'callcenter_calllog', json:json},
//                type: "GET",
//                dataType: "json",
//                success: function(data, dataStatus){
//			        
//			    }
//	    });
                
    	//console.log(json);
    	//hojo.byId("icallcenter.iframe").src="http://localhost:15062/?json=" + hojo.toJson(json) + "&random=" + Math.floor(Math.random()*100000);
    }
    
});
