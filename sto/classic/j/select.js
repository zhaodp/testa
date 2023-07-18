(function(win,$){
	var location = win.location,
		id = getQueryString('id') || '';
	function getQueryString (param) {
        if (location.href.indexOf('?') === -1 || location.href.indexOf(param + '=' ) === -1) {
            return '';
        }
        var queryString = location.href.substring(location.href.indexOf('?') + 1);
        if (queryString.indexOf('#') !== -1) {
            queryString = queryString.substring(0, queryString.indexOf('#'));
        }
        var parameters = queryString.split('&');
        var pos, paraName, paraValue;
        for (var i = 0; i < parameters.length; i++) {
            pos = parameters[i].indexOf('=');
            if (pos === -1) { continue; }

            paraName = parameters[i].substring(0, pos);
            paraValue = parameters[i].substring(pos + 1);

            if (paraName === param) {
                return paraValue.replace(/\+/g, ' ');
            }
        }
        return '';
    }

    function initData(){
    	$.ajax({
    		url: '/v2/index.php?r=thirdStage/billInstance',
    		data: {id: id},
    		dataType: 'json',
    		type: 'GET',
    		success: function(json){
    			if(json.code == 0){
    				tplInit(json.data);
    			}
    		}
    	});
    }
    function tplInit(data){
    	var	billData = data.billForm || {},
    		newUserData = data.newUserForm || {},
    		flowData = data.flowForm || {},
    		oldUserData = data.oldUserForm || {},
    		billBase = billData.base || '',
    		billStage = billData.stage || '',
    		billChildren = billData.children || '',
    		newUserBase = newUserData.base || '',
    		newUserStage = newUserData.stage || '',
    		newChildren = newUserData.children || '',
    		flowBase = flowData.base || '',
    		flowStage = flowData.stage || '',
    		flowChildren = flowData.children || '',
    		oldUserBase = oldUserData.base || '',
    		oldUserStage = oldUserData.stage || '',
    		oldChildren = oldUserData.children || '',
    		metaData = {
			items: [{
				name: 'billForm',
				label: '按报单数分成',
				ceil: '元/单',
				basePrice: billBase,
				selected: getPrice(billStage, 'select'),
				selectOption: getPrice(billChildren, 'selectOption'),//(billData.lowPrice || '') + '-' + (billData.highPrice||'') + '单，' + (billData.perPrice||'') + '元/单',
				children: getPrice(billChildren, 'children')
				// [{
				// 	lowPrice: billData.lowPrice || '',
				// 	highPrice: billData.highPrice || '',
				// 	perPrice: billData.perPrice || ''
				// }]
			},{
				name: 'newUserForm',
				label: '按新客报单数分成',
				ceil: '元/单',
				basePrice: newUserBase,
				selected: getPrice(newUserStage, 'select'),
				selectOption: getPrice(newChildren, 'selectOption'),
				children: getPrice(newChildren, 'children')
			},{
				name: 'flowForm',
				label: '按流水金额分成',
				ceil: '元/单',
				basePrice: flowBase,
				selected: getPrice(flowStage, 'select'),
				selectOption: getPrice(flowChildren, 'selectOption'),
				children: getPrice(flowChildren, 'children')
			},{
				name: 'oldUserForm',
				label: '按老客报单数分成',
				ceil: '元/单',
				basePrice: oldUserBase,
				selected: getPrice(oldUserStage, 'select'),
				selectOption: getPrice(oldChildren, 'selectOption'),
				children: getPrice(oldChildren, 'children')
			}],
			children:[],
			isShow: false
		},
		// 填充添加或编辑分成方式
		mask = document.getElementById('maskPop'),
		pop = document.getElementById('pop'),
		checkboxDiv = document.getElementById('checkbox'),
		input, checkboxArr=[],radio,

		demo = new Vue({
			el: '#checkbox',
			data: metaData,

			filters: {
				stageData: function() {
					var cur;
					for (var i=0,len=this.selectOption.length; i<len; i++){
						if(this.selected == this.selectOption[i]){
							 cur = i;
						}
					}
					return {
						base: (this.basePrice||''),
						stage: (this.children[cur]||''),
						children: this.children
					}
				}
			},
			methods: {
				noSelect: function(){
					this.getEle();
					if(radio.checked){
						for(var j=0,len=checkboxArr.length;j<len;j++){
							checkboxArr[j].checked = false;
						}
					}
				},
				checked: function(){
					this.getEle();
					console.log(this);
					if(this.checked = true){
						radio.checked = false;
					}
				},
				getEle: function(){
					checkboxArr=[];
					input = checkboxDiv.getElementsByTagName('input');
					for(var i=0,len=input.length,inputItem; i<len; i++){
						inputItem = input[i];
						if(inputItem.type == 'checkbox'){
							checkboxArr[checkboxArr.length] = inputItem;
						}else if(inputItem.type == 'radio'){
							radio = inputItem;
						}
					}
				},
				addprice: function(){
					this.children.push({lowPrice:'',highPrice:'',perPrice:''});
				},
				showPop: function(index){
					// 显示调整阶梯价格的弹出框，并填充获取的当前数据
					
					if(!this.isShow){
						this.isShow = !this.isShow;
						this.children = this.items[index].children;
						pop.setAttribute('item', index);

					}
				},
				hidePop: function(){
					this.isShow = !this.isShow;
				},
				newPrice: function(index){
					var child=this.children[index],
					pop = document.getElementById('pop'),
					item = pop.getAttribute('item'),
					selectOption = this.items[item].selectOption;
					// 需要获取当前的弹窗的索引值给items
					if(child){
						selectOption.$set(index, (child.lowPrice || '') + '-' + (child.highPrice||'') + '单，' + (child.perPrice||'') + '元/单');
					}
					this.items[item].selected = selectOption[index];

				}
			}
		});
    }
    function getPrice(stage, type){
    	var selectArr=[],children = [], lowPrice, highPrice, perPrice;
    	if(type == 'select'){
    		return (stage.lowPrice || '') + '-' + (stage.highPrice||'') + '单，' + (stage.perPrice||'') + '元/单';
    	}else{
    		for (var i=0,len=stage.length,stageItem; i<len; i++){
    			stageItem = stage[i];
    			lowPrice = stageItem.lowPrice;
    			highPrice = stageItem.highPrice;
    			perPrice = stageItem.perPrice;
    			selectArr.push(lowPrice + '-' + highPrice + '单，' + perPrice + '元/单');
	    		children.push({
	    			lowPrice: lowPrice,
	    			highPrice: highPrice,
	    			perPrice: perPrice
	    		});
	    	}
	    	if(type == 'selectOption'){
	    		return selectArr;
	    	}else{
	    		return children;
	    	}
    	}
    }
    initData();
	
})(window,jQuery);
