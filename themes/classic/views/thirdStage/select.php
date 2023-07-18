<?php
$format = "<input type='hidden' id='bill_instance' value='%s' />";
$json = '{}';

if($billInstance && !empty($billInstance['meta'])){
	    $json = json_encode($billInstance->attributes);
}
echo sprintf($format, $json);
?>
<div class="checkboxArr" id="checkbox">
		<div class="item" v-repeat="items">
			<p class="col col-a">
				<input type="checkbox" name="{{name}}" v-on="click:checked" value="{{''|stageData|json}}" v-attr="checked:basePrice ? 'checked' : '' "
								/>
				<label>{{label}}</label>
			</p>
			<p class="col col-b">
				基础分成金额：
				<input type="text" class="checkbox-txt" v-model="basePrice" value="{{basePrice}}" placeholder="0.00" />
				{{ceil}}
			</p>
			<p class="col col-c">
				<select class="checkbox-selct" v-model="selected" options="selectOption"></select>
				<a class="checkbox-btn" href="javascript:;" index={{$index}} v-on="click:showPop($index)" >调整阶梯价</a>

			</p>
		</div>
		<div class="item">
			<input type="radio" id="radio" value="不分成" v-on="click: noSelect()"  />
			<label>不分成</label>
		</div>
		<div class="pop {{isShow?'popshow':'pophide'}}" id="pop">
			<p class="title">设定阶梯价</p>
			<p class="intro">请设定正确的阶梯价格，上限若为正无穷大，请输入“+”号。</p>
			<table class="Mtable">
				<tr>
					<th></th>
					<th>成单数(单)</th>
					<th>单价(元/单)</th>
				</tr>
				<tr v-repeat="children">
					<td></td>
					<td>
						<input class="checkbox-txt" type="text" v-model='lowPrice' value="{{lowPrice}}" v-on="change:newPrice($index)" lazy /> 到 
						<input class="checkbox-txt" type="text" v-model='highPrice' value="{{highPrice}}" v-on="change:newPrice($index)" />
					</td>
					<td>
						<input class="checkbox-txt" type="text" v-model='perPrice' value="{{perPrice}}" v-on="change:newPrice($index)" />
					</td>
				</tr>
				<tr>
					<td><span class="addprice" v-on="click:addprice()">+</span></td>
					<td></td>
					<td></td>
				</tr>
			</table>
			<div class="btnArr"><a class="btn btn-blue" href="javascript:;" v-on="click:hidePop();">保存</a></div>
		</div>
		<div v-cloak class="mask {{isShow?'popshow':'pophide'}}" id="maskPop"></div>
	</div>
