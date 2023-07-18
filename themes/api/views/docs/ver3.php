系统调用接口

	1.使用中的接口
	
	读取指定司机400派单队列
	customer.order.queue
		params: user
		output: 'hello $user,waitting 10s,ok! 2012-09-30 23:23:23'
		
	获取当日的客户呼叫位置及时间分布
	customer.position
		params: city_id
		output: array(device, longitude,latitude,call_time)
	
	获取当日的客户呼叫位置及时间分布
	customer.positionofcallcenter
		params: city_id
		output: array(location_start)	
		
	充值码、邀请码充值
	customer.account.recharge
	充值码、邀请码充值，token = fee = 0
		params: token, phone, type, bonus, fee
			type = 5 呼叫中心充值
			
		output: result = json {"code":"","token":"","recharge":"","message":""}
			code = 0 充值成功
			code = 1 充值失败，号码不存在
			code = 2 充值失败，已充值	
		
用户客户端相关接口

	1.使用中的接口
	
		1.1 App信息
		
			读取客户端收集信息
			app.device.set
				params: deviceToken, token, type
				output: result = json {"code":"","message":""}
					code = 0 写入成功
					code = 1 记录已存在
			读取服务器上的客户端版本信息
			app.version.get
				output: result = json {"code":"","latest":"","deprecated":"","message":""}
					code = 0 读取成功
			读取服务器上的APP文字内容
			app.content
				output: result = json {"code":"","content":"","message":""}
					code = 0 读取成功
			读取价格表		
			app.price
				params: longitude, latitude, cityName
				output: result = json {"code":"","priceList":"","memo":"","message":""}
					code = 0 读取成功
					code = 1 当前城市尚未开通E代驾服务
			读取城市列表		
			app.city.list
				output: result = json {"code":"","cityList":"","message":""}
					code = 0 读取成功
		
		1.2 司机信息		
		
			读取附近的司机列表
			driver.nearby
				params: udid,gps_type{gps,google,baidu},longitude,latitude
				output: result = json {"code":"","driverList":"","message":""}
					code = 0 读取成功
					
			读取司机详细信息
			driver.get
				params: driverID 
				output: result = json {"code":"","driverInfo":"","message":""}
					code = 0 读取成功
					
			读取司机评价列表
			driver.comment.list
				params: driverID, pageNo, pageSize
				output: result = json {"code":"","commentList":"","message":""}
					code = 0 读取成功
					
			通过GPS位置获取城市价格表
			driver.city.price
				params: lng, lat, gps_type
				output: result = json {"code":"","price_list":"","message":""}
					code = 0 读取成功
					code = 2 参数有误（坐标传递有误）

			司机结伴返程
			driver.back_together
			params: token, goback
			output: result = json {"code":"","message":""}
			        code = 0 成功
			        code = 1 toke失效
			        code = 2 其它信息
					
		1.3 用户客户端其他接口
		
			留言墙
			comment.list
				params: pageNo, pageSize
				output: result = json {"code":"","commentList":"","message":""}
					code = 0 读取成功 
					
			记录呼叫记录
			customer.calllog
				params: phone, udid, driverID, device, os, version, longitude, latitude, callTime
				output: result = json {"code":"", "callTime":""}
					code = 0 记录成功
					code = 1 记录失败
					callTime 记录时间
				
			充值码、邀请码充值
			customer.account.recharge
			充值码、邀请码充值，token = fee = 0
				params: token, phone, type, bonus, fee
					type = 0 现金充值
					type = 1 支付宝充值
					type = 2 邀请码、充值码充值
					type = 3 
					type = 4 消费
					type = 5 呼叫中心充值
					
				output: result = json {"code":"","token":"","recharge":"","message":""}
					code = 0 充值成功
					code = 1 充值失败，号码不存在
					code = 2 充值失败，已充值	
					
			获取邀请码
			bonus.invite
				params: macaddress, from
				output: result = json {"code":"","message":"","invite_code":"","invite_count":"","invite_text_top":"","invite_text":"","weibo_invite_text":"","sms_invite_text":""}
					code = 0 获取成功
					code = -1 邀请码获取失败
			
			意见反馈
			customer.feedback
				params : macaddress, email, content, from, device, os, version
				output: result = json {"code":"","message":""}
						code = 0 反馈提交成功
						code = 1 保存失败
						
			城市价格列表
			customer.city.pricelist
				params : lng lat gps_type
				output: result = json {"code":"",price_list:"" , "message":""}
						code = 0 成功
						code = 1 参数有误
	
			意见反馈
			customer.city.currentprice
				params : lng lat gps_type
				output: result = json {"code":"","message":""}
						code = 0
						
	2.客户端未发布版本的接口
	
		预登录
		customer.prelogin
			params: phone, udid
			output: result = json {"code":"","message":""}
				code = 0 密码已成功发送。
				code = 1 系统延迟，请稍后再试。
				code = 2 感谢您使用E代驾，您已经连续三次请求获取E代驾预登录密码。如有疑问，请拨打4006-91-3939联系客服为您服务。。
				code = 3 十分钟之内只能请求一次预登录密码。
			other: sendsms 临时密码
			
		登录
		customer.login
			params: phone, passwd
			output: result = json {"code":"","token":"","message":""}
				code = 0 登陆成功
				code = 1 登录失败
					error = 1 未进行预登录
					error = 2 密码错误
					error = 3 密码过期
			临时密码发送时间 + 10分钟过期
			other: deletepretoken
				
		银联充值XML
		customer.account.rechargeapi
			params: token, fee, channel = 0
			output: result = json {"code":"","token":"","url":"","message":""}
				code = 1 token已失效请重新进行预注册 
				
				成功返回 XML
	
	3.通过测试未在客户端实现的接口
	
		个人信息
		customer.get
			params: token
			output: result = json {"code":"","customerInfo":"","message":""}
				code = 0 读取成功
				code = 1 token已失效请重新进行预注册
				
		更新客户称谓
		customer.update
			params: token
			output: result = json {"code":"","message":""}
				code = 0 更新成功
				code = 1 token已失效请重新进行预注册
				code = 2 更新失败
				
		订单历史
		customer.order.list
			params: token, pageNo, pageSize
			output: result = json {"code":"","orderList":"","message":""}
				code = 0 读取成功
				code = 1 token已失效请重新进行预注册
		
		客户账户交易历史列表
		customer.account.trade.list
			params: token, pageNo, pageSize
			output: result = json {"code":"","accountDealList":"","message":""}
				code = 0 读取成功
				code = 1 token已失效请重新进行预注册
				
		订单详情
		customer.order.get
			params: token, orderId
			output: result = json {"code":"","orderInfo":"","message":""}
				code = 0 读取成功
				code = 1 token已失效请重新进行预注册
				
		下单（一键预约）
		customer.orderqueue.booking
			params: token, order_timedelta_from_now_on , order_gps_latitude , order_gps_longitude , order_street_name , order_street_name_is_edited , order_contact_phone ,  order_customer_phone , order_drivers_count , passwd
			output: result = json {"code":"","orderqueue_info":"","token":"","message":""}
				code = 0 预约成功
				code = 1 操作失败
				code = 2 token已失效请重新进行预注册
				
		订单列表OrderQueue（一键预约）
		customer.orderqueue.list
			params: token, pageNo , pageSize
			output: result = json {"code":"","orderList":"" , "orderCount":"" ,"message":""}
				code = 0 读取成功
				code = 2 token已失效请重新进行预注册
				
		订单列表Order（一键预约）
		customer.orderqueue.orderlist
			params: token, pageNo , pageSize
			output: result = json {"code":"","orderList":"" , "orderCount":"" , "message":""}
				code = 0 读取成功
				code = 2 token已失效请重新进行预注册
				
		订单详情（一键预约）
		customer.orderqueue.detail
			params: token, order_id
			output: result = json {"code":"","orderqueue_info":"","message":""}
				code = 0 读取成功
				code = 1 操作失败
				code = 2 token已失效请重新进行预注册
				
		取消订单（一键预约）
		customer.orderqueue.cancel
			params: token, order_id
			output: result = json {"code":"","message":""}
				code = 0 操作成功
				code = 1 操作失败
				code = 2 参数验证失败
				
		司机客户端补单（一键预约）
		driver.order.booking
			params: token, name , phone , lng , lat , gps_type
			output: result = json {"code":"","message":""}
				code = 0 操作成功
				code = 1 token失效
				code = 2 参数验证失败
				
		呼叫生成订单
		driver.order.callorder
			params: token, phone , order_number
			output: result = json {"code":"","message":""}
				code = 0 操作成功
				code = 1 token失效
				code = 2 参数验证失败
				
		呼叫生成订单
		driver.order.favorable
			params: token, phone , booking_time
			output: result = json {"code":"" , 'fav':"" , "message":""}
				code = 0 操作成功
				code = 1 token失效
				code = 2 参数验证失败
				
		黑名单列表
		customer.blacklist
			params: token
			output: result = json {"code":"","message":""}
				code = 0 操作成功
				code = 1 操作失败
				
		白名单
		customer.whitelist
			params: token
			output: result = json {"code":"" whitelist : '' ,"message":""}
				code = 0 操作成功
				code = 1 操作失败
		获取历史地址记录
		customer.address.match
			params: token,lng,lat,gps_type = "wgs84",address,city_name=""
			output: result = json {"code":"" city_name : '' address_list :: '' ,"message":"获取成功"}
				code = 0 操作成功
				code = 10  参数错误
		注册客户端Client ID
		customer.push.register
			params: token,client_id,udid
			output: result = json {"code":","message":"获取成功"}
				code = 0 操作成功
				code = 1 token失效
				code = 2 参数错误

		返回又拍云上传下载地址
		customer.push.getUpyunInfo
			params: token,md5_file,filesize,type,from
			output: result = json {"code":","info":"" , message":"获取成功"}
				code = 0 操作成功
				code = 1 token失效
				code = 2 参数错误

		创建订单(仿滴滴打车 用音频约车)
		customer.order.create
			params: token,lng,lat,gps_type,order_street_name,audio_url_path
			output: result = json {"code":",message":"获取成功"}
				code = 0 操作成功
				code = 1 token失效
				code = 2 参数错误



	4.未通过测试接口
		支付宝充值地址
		customer.account.rechargeapi
			params: token, fee, channel = 1
			output: result = json {"code":"","token":"","url":"","message":""}
				code = 0 成功
				code = 1 token已失效请重新进行预注册 
	
	5.未实现接口
		客户交易历史
		customer.account.list
			params: token, pageNo, pageSize
			output: result = json {"code":"","tradeList":"","message":""}
				code = 0 读取成功
				code = 1 token已失效请重新进行预注册
				
司机客户端接口
	1.在客户端已实现的接口
		手机状态更新默认时间
		driver.define.get
			约定 返回时间为更新秒数
			output: result = json {"code":"","idle_call_log_step":"","idle_position_step":"","busy_call_log_step":"","busy_position_step":"","offline_call_log_step":"","offline_position_step":"","together_call_log_step":"","together_position_step":"","message":""}
				code = 0 读取成功
		
		返回服务器时间
		driver.define.timestamp
			约定 timestamp 格式 20121212193033 
			output: result = json {"code":"","timestamp":"","message":""}
					code = 0 读取成功
				
		手机预注册
		driver.register
			params: imei, sim , phone
			output: result = json {"code":"","user":"","is_bind":"","message":""}
			约定 is_bind 0 未绑定	 1 已绑定
			约定 user 未绑定的司机第一位为 V
				code = 0 注册成功
				code = 1 imei sim 在系统中已存在
				
		司机登录
		driver.login
			params: imei, sim, user, pwd
			约定 pwd = md5(pwd)
			约定 is_bind 0 未绑定	 1 已绑定
			约定 user 未绑定的司机第一位为 V
			output: result = json {"code":"","user":"","name":"","is_bind":"","token":"","message":""}
				code = 0 登录成功
				code = 1 用户名或密码错误
				code = 2 用户未激活，手机信息变更，请联系司机管理部
			
		司机呼叫记录上传
		driver.upload.calllog
			params: imei, sim, user, token, phone, type, longitude, latitude, callTime, endTime, talkTime, status
			约定 type = 0 呼入， 1 呼出， 2 挂断
			约定 无论是否开启服务都上传记录
			约定 callTime endTime 格式 20121208193028
			约定 tallTime 为通话时长的秒数
			output: result = json {"code":"","message":""}
				code = 0 上传成功
				code = 1 token 已过期请重新登录
				
		司机位置状态更新
		driver.upload.position
			params: imei, sim, user, token, status, longitude, latitude, mcc, mnc, towers, log_time
			约定：status = 0 空闲，1 服务中 2 下班
			约定：log_time 格式 20120123091034 年月日时分秒
			约定：longitude, latitude 地址为WGS84 如果读不到 longitude = latitude = 1
			约定：towers = [{"mcc":"460","lac":"4282","ci":"65535","ssi":"12","ta":"255"},{"mcc":"460","lac":"6311","ci":"49506","ssi":"100","ta":"255"},{"mcc":"460","lac":"6311","ci":"49506","ssi":"100","ta":"255"},{"mcc":"460","lac":"6311","ci":"49506","ssi":"100","ta":"255"},{"mcc":"460","lac":"6311","ci":"49506","ssi":"100","ta":"255"},{"mcc":"460","lac":"6311","ci":"49506","ssi":"100","ta":"255"},{"mcc":"460","lac":"6311","ci":"49506","ssi":"100","ta":"255"}]
			约定：无论是否开启服务都上传记录
			output: result = json {"code":"","message":""}
				code = 0 上传成功
				code = 1 token 已过期请重新登录
		
		司机客户端心跳接口
		driver.define.heartbeat
			params: user
			output: result = json {"code":"","message":""}
				code = 0 上传成功
		个推客户端注册
		driver.push.register
			params: client_id,udid,version,city,driver_user
			约定：version = driver 司机客户端 driver 用户客户端 customer
			output: result = json {"code":"","message":""}
				code = 0 上传成功
				code = 1 token
				code = 2 版本参数不正确
		获取收入和余额信息
		driver.account.info
			params: token
			约定：version = driver 司机客户端 driver 用户客户端 customer
			output: result = json {"code":"","message":"","info":""}
				code = 0 成功
				code = 1 token
				info = balance 当前余额
				info = yesterday 昨日收入
				info = month 当月收入
				info = total 总收入
		接收订单
		driver.order.accept
			params: token,queue_id,driver_id,type,push_msg_id,gps_type,lng,lat,log_time
			约定:type = order 订单类型order
			约定:gps_type = wgs84 默认为wgs84
			output: result = json {"code":"","message":""}
				code = 0 成功
				code = 1 token
				code = 2 参数不正确 司机信息无效 队列己分配
				
		接收订单
		driver.order.receive
			params: token,queue_id , order_id ,push_msg_id,gps_type,lng,lat,log_time
			约定:type = order_detail 订单类型order_detail
			约定:gps_type = wgs84 默认为wgs84
			output: result = json {"code":"","message":""}
				code = 0 成功
				code = 1 token
				code = 2 参数不正确 司机信息无效 队列己分配

		接收订单(仿滴滴打车)
		driver.order.audioReceive
			params: token,queue_id , order_id ,push_msg_id,gps_type,lng,lat,log_time
			约定:type = order_detail 订单类型order_detail
			约定:gps_type = wgs84 默认为wgs84
			output: result = json {"code":"","message":""}
				code = 0 成功
				code = 1 token
				code = 2 参数不正确 司机信息无效 队列己分配

		推送消息状态
		driver.push.feedback
			params: token,push_msg_id,flag
			约定：flag = 2 接收成功
			output: result = json {"code":"","message":""}
				code = 0 成功
				code = 1 token
				code = 2 参数不正确
				
		推送消息状态
		driver.push.callback
			params: token,push_msg_id,flag
			约定：flag = 2 接收成功
			output: result = json {"code":"","message":""}
				code = 0 成功
				code = 1 token
				code = 2 参数不正确
			
		到达客人处接口
		driver.order.arrive
		    params: token,order_id,gps_type,lng,lat,log_time
			约定: gps_type = wgs84 默认为wgs84
			output: result = json {"code":"","message":""}
		    code = 0 成功
		    code = 1 token
		    code = 2 参数不正确		
			
		开始(开车)状态接口
		driver.order.start
		    params: token,order_id,gps_type,lng,lat,log_time,car_number,name,phone
			约定: gps_type = wgs84 默认为wgs84
			output: result = json {"code":"","message":""}
		    code = 0 成功
		    code = 1 token
		    code = 2 参数不正确
		流量统计接口
		driver.upload.traffic
		    params: user,total_rx,total_tx,rx,tx,device,app_ver,created
		    约定: total_rx 当日接收流量(Kb)
		          total_tx	当日发送流量(Kb)
		          rx 司机客户端接收流量(Kb)
		          tx	司机客户端发送流量(Kb)
			output: result = json {"code":"","message":""}
		    code = 0 成功
		获取订单详接口
		driver.order.getOrderInfo
		    params: token,order_id
		    output: result = json {"code":"","message":"","info":""}
			code = 0 成功
			code = 1 token
			code = 2 参数不正确

2.在客户端已实现的接口
		2.1. 司机公告
		司机公告列表
		driver.notice.list
			params: token, category = 0, pageSize, pageNo
			output: result = json {"code":"","noticeList":"","message":""}
				code = 0 读取成功
				code = 1 token 已过期请重新登录
				
		司机公告内容
		driver.notice.get
			params: token, notice_id
			output: result = json {"code":"","notice":"","message":""}
				code = 0 读取成功
				code = 1 token 已过期请重新登录
				
		最新未读公司公告列表
		driver.notice.newestlist
			params: token, pageSize, pageNo
			output: result = json {"code":"","noticeList":"","is_newest":"","message":""}
				约定：is_newest = 0 已读 1 未读
				code = 0 读取成功
				code = 1 token 已过期请重新登录
				code = 2 无未读公告
				
		最新未读公司公告内容
		driver.notice.newest
			params: token
			output: result = json {"code":"","notice":"","message":""}
				code = 0 读取成功
				code = 1 token 已过期请重新登录
				code = 2 无未读公告
				
		司机公告内容阅读
		driver.notice.read
			params: token, notice_id
			output: result = json {"code":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
		
		2.2. 司机订单
		按照状态获取司机订单总数		
			driver.order.orderCount
			params: token 
			约定：status = 0 未报单，1 已报单, 2 销单待审核, 3 已销单, 4 销单审核不通过
			output: result = json {"code":"","orderCountWithStatus":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
				
		按照状态获取司机订单列表	
			driver.order.list
			params: token, status 
			约定：status = 0 未报单，1 已报单, 2 销单待审核, 3 已销单, 4 销单审核不通过
			output: result = json {"code":"","orderCount":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
		
		获取报单订单列表
			driver.order.submitlist
			params: token , pageSize , pageNo
			output: result = json {"code":"","orderList":"","message":""}
				code = 0 操作成功
				         orderList = array(order_id , source , booking_time , income（代驾费用）, location_start(开始位置))
				code = 1 token 已过期请重新登录
				code = 2 参数有误
				
		获取报单订单详情	
			driver.order.submitdetail
			params: token , order_id
			output: result = json {"code":"","order":"","message":""}
				code = 0 操作成功  
				         其中 order = array(order_id , name , phone , source , location_start , location_end , booking_time , reach_time , distance , income（代驾费用） , car_number(车牌号) , wait_time , tip(小费) , car_cost（打车费） , other_cost（其他费用） , total（总费用） , favorable（优惠信息）, complaint（是否被投诉）)
				code = 1 token失效
				code = 2 参数有误
		
		获取司机信息费余额	
			driver.account.balance
			params: token 
			output: result = json {"code":"","balance":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录	
				
				
		获取司机订单详情
			driver.order.detail
			params: token, order_id 
			output: result = json {"code":"","detail":"","log":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
		销单类型
			driver.order.cancelType
			params: token 
			output: result = json {"code":"","list":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
		司机补单
			driver.order.create
			params: token, call_time, booking_time, phone, source 
			约定：call_time booking_time 格式 201303210709
			output: result = json {"code":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
				code = 2 内容校验出错
		司机销单
			driver.order.cancel
			params: token, order_id, cancel_type, log, isComplaint, name, complaint_type, complaint
			output: result = json {"code":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
				code = 2 内容校验出错
		新司机销单
			driver.order.newCancel
			params: token, order_id, order_number, cancel_type, log, isComplaint, name, complaint_type, complaint ,content,phone
			output: result = json {"code":"","message":""}
			code = 0 操作成功
			code = 1 token 已过期请重新登录
			code = 2 内容校验出错
		司机报单
			driver.order.submit
			params: token, order_id, order_number, vipcard, name, start_time, end_time, waiting_time, location_start
			params: location_end, distance, income, log, car_number, car_brand, car_status, isComplaint, complaint_type, complaint
			params: OrderInvoice, invoice_title, invoice_content, invoice_contact, invoice_telephone, invoice_address, invoice_zipcode
			约定：start_time end_time 格式 201303210709 
			约定：waiting_time 分钟数
			约定：isComplaint = 1 需要投诉 OrderInvoice = 1 需要发票
			output: result = json {"code":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
				code = 2 内容校验出错
				
		发票信息
			driver.order.invoice
			params: token, order_id
			output: result = json {"code":"","detail":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
				code = 2 内容校验出错
				
		更新发票信息
			driver.order.invoiceUpdate
			params: token, order_id, invoice_title, invoice_content, invoice_contact, invoice_telephone, invoice_address, invoice_zipcode
			output: result = json {"code":"","message":""}
				code = 0 操作成功
				code = 1 token 已过期请重新登录
				code = 2 内容校验出错

        2.3 司机的帮助中心接口 知识库接口
            获取知识库一级分类
                driver.faq.home
                params: token,
                output: result = json{"code":"","message":"","data":{"faq_category":{"1":"fenlei1","2":"分类2","3":"\u5176\u4ed6"}}}
                code = 0 操作成功
                code = 1 token 已过期请重新登录
                code = 2 内容校验出错

            获取知识库二级分类
                driver.faq.list
                params: token,pid(父分类id 必选),cid（分类id 可选 无cid时 返回当前父分类所有知识库列表）pageNo,pageSize
                output: result = json{{"code":0,"data":{"tab_list":{"4":"category1","5":"category2","6":"category3"},"knowledge_list":{"count":"1","data":[{"id":"9","title":"bbbb"}]}},"message":""}}
                code = 0 操作成功
                code = 1 token 已过期请重新登录
                code = 2 内容校验出错
					
	3.未实现接口
		
		结伴返城
		记录ClientID
		
短信API接口
    1.指联在线
         上行接口(接收指联在线推送信息,写入t_sms_uplink表中)
             sms.zlzx.uplink
             params: name(名字), src(源号码-用户手机号码), dest(目的号码), content(上行内容), time(上行接收时间)
             output: result = string(info)
                 info = 'success'  成功
                 info = 'error:参数有误'
                 info = 'error:记录失败'
    		
其他待实现接口名称

order.list 订单
order.confirm
order.get
order.update

			case 'confirm' :
			//读取呼叫中心所派订单（读取短信中的订单号及手机号）
			case 'get' :
			//取消订单
			case 'cancel' :
			//代驾开始
			case 'start' :
			//代驾结束
			case 'end' :
			//代驾费用结构
			case 'fee' :
			//车辆运行轨迹
			case 'track' :
			//订单数量分状态 driver_id 司机工号，range 时间轴 参数值 today yestoday week month。返回值 init 未报单订单 waiting 销单待审核 finished 已完成订单
			case 'query' :