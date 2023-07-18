(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {
    var defaults = {
    	url: '/index.php?r=CityConfig/getopencityinchina',
		multiple: false,
		limit: null,
		data: [],
		select: function() {},
		remove: function() {}
	},

	tmpl = [
		'<div class="e-city-selector">',
			'<a class="close" href="javascript:" title="关闭">×</a>',
			'<div class="city-seletor-header">',
				'<div class="search-box">',
					'<a href="javascript:" class="clear">×</a>',
					'<input type="text" class="search-input" placeholder="输入城市名称搜索">',
				'</div>',
			'</div>',
			'<div class="city-selector-body">',
				'<ul class="region-tabs">',
					'{{#regionTabs}}',
				'</ul>',
				'<div class="region-tab-container">',
					'<div class="tab-content">{{#cities}}</div>',
					'<div class="region-cities search-result">',
						'<div class="province">',
							'<ul class="province-cities"></ul>',
							'</div>',
						'</div>',
					'</div>',
				'</div>',
			'</div>',
		'</div>'
	].join('');

	var CitySelector = function($element, option) {
		var self = this;

		option = this.option = $.extend({}, defaults, option);

		option.multiple = option.multiple || (!!$element.attr('multiple'));

		this.selected = option.multiple ? option.data : (option.data.length ? [option.data[0]] : []);

		this.$element = $element;

		var width = $element.outerWidth(),
			height = $element.outerHeight();

		this.$inputContainer = $('<div class="e-city-input-container" style="width:' + width + 'px;min-height:' + height + 'px;"></div>').appendTo($element.parent());
		
		if(this.selected.length) {
			this.initSelectedData();
		}

		this.fetchCityData(function(data) {
			self.cityData = data;

			var $container = self.$container = self.viewRender(data);

			$element.hide();
			
			$container.hide().css(self.caculateContainerPos());

			self.bindEvent();
		})
	}

	CitySelector.prototype.fetchCityData = function(success) {
		$.getJSON(this.option.url).done(function(ret) {
			if(ret.code === 0) {
				if(success) {
					success(ret.data);
				}
			} else {
				alert(ret.message);
			}
		}).fail(function() {
			alert('获取城市列表网络请求错误，请检查网络');
		})
	}

	CitySelector.prototype.viewRender = function() {
		var cityData = this.cityData;
		var regionTabHtml = [],
			citiesHtml = [];
		$.each(cityData, function(i) {
			var region = cityData[i];
			regionTabHtml.push('<li class="region"><a href="javascript:">' + region.region_name + '</a></li>');
			
			citiesHtml.push('<div class="region-cities">');
				
			$.each(region.province, function(j) {
				var province = region.province[j];

				var citiesOfProvinceHtml = [];

				$.each(province.city, function(k) {
					var city = province.city[k];
					citiesOfProvinceHtml.push('<li><a class="city" href="javascript:" data-id="' + city.city_id + '">' + city.city_name + '</a></li>');
				})
				citiesHtml.push([
					'<div class="province">',
						'<div class="province-name">' + province.province_name + '</div>',
						'<ul class="province-cities">',
							citiesOfProvinceHtml.join(''),
						'</ul>',
					'</div>'
				].join(''))
			})
			citiesHtml.push('</div>');
		})

		return $(tmpl.replace('{{#regionTabs}}', regionTabHtml.join('')).replace('{{#cities}}', citiesHtml.join(''))).appendTo($('body'));
	}

	CitySelector.prototype.isSelected = function(city) {
		var selectedCity = this.selected;
		var isExist = false;
		$.each(selectedCity, function(i) {
			if(selectedCity[i].id === city.id) {
				isExist = true;
				return false;
			}
		});
		return isExist;
	}

	CitySelector.prototype.removeSelectedCity = function(id) {
		var selectedCity = this.selected;
		$.each(selectedCity, function(i) {
			if(selectedCity[i].id === id) {
				selectedCity.splice(i, 1);
				return false;
			}
		});
		this.$element.val(this.getSelectedIds());
	}

	CitySelector.prototype.select = function(city) {
		if(this.isSelected(city)) {
			return;
		}

		if(!this.option.multiple) {
			this.selected = [city];
		} else {
			if(typeof this.option.limit === 'number' && this.option.limit <= this.selected.length) {
				return;
			}
			this.selected.push(city);
		}

		this.selectRender(city);

		this.$element.val(this.getSelectedIds());

		if(typeof this.option.select === 'function') {
			var selectedData = this.option.multiple ? this.selected : this.selected[0];
			this.option.select(selectedData);
		}
	}

	CitySelector.prototype.initSelectedData = function(cities) {
		var self = this;
		var selectedData = this.selected;
		if(cities && cities.length) {
			selectedData = selectedData.concat(cities);
		}
		$.each(selectedData, function(i) {
			var city = selectedData[i];
			self.selectRender(city);
		});
		this.$element.val(this.getSelectedIds());
	}

	CitySelector.prototype.getSelectedIds = function() {
		var ids = [];
		var selected = this.selected;
		$.each(selected, function(i) {
			ids.push(selected[i].id);
		});
		return ids.join(',');
	}

	CitySelector.prototype.selectRender = function(city) {
		var html = [
			'<span class="city-item">',
				'<span class="city-name">' + city.name + '</span>',
				'<a class="city-remove" data-id="' + city.id + '" href="javascript:">×</a>',
			'</span>'
		].join('');

		if(this.option.multiple) {
			this.$inputContainer.append(html);
		} else {
			this.$inputContainer.html(html);
		}
	}

	CitySelector.prototype.mixCities = function() {
		var self = this;
		this.cities = [];
		var regions = this.cityData;
		$.each(regions, function(i) {
			var provinces = regions[i].province;

			$.each(provinces, function(j) {
				var cities = provinces[j].city;
				self.cities = self.cities.concat(cities);
			})
		})
	}

	CitySelector.prototype.findCityByName = function(key) {
		var matched = [];
		if(!this.cities) {
			this.mixCities();
		}
		var cities = this.cities;
		$.each(cities, function(i) {
			var city = cities[i];
			if(city.city_name.indexOf(key) !== -1) {
				matched.push(city);
			}
		})
		return matched;
	}

	CitySelector.prototype.searchResultRender = function(list) {
		var html = [];
		$.each(list, function(i) {
			var item = list[i];
			html.push('<li><a href="javascript:" class="city" data-id="' + item.city_id + '">' + item.city_name + '</a></li>');
		});
		this.$container.find('.search-result').html(html.join(''));
	}

	CitySelector.prototype.caculateContainerPos = function() {
		var $element = this.$inputContainer;
		var $container = this.$container;

		var winWidth = $(window).width(),
			offset = $element.offset(),
			top = offset.top,
			left = offset.left,
			width = $element.outerWidth(),
			height = $element.outerHeight(),
			containerWidth = $container.width();
		var pos = {
			top: top + height + 'px'
		}

		if(winWidth - left > containerWidth) {
			pos['left'] = left + 'px';
		} else {
			pos['left'] = (left + width) - containerWidth + 'px';
		}
		return pos;
	}

	CitySelector.prototype.resizeFix = function() {
		var pos = this.caculateContainerPos();
		this.$container.css(pos);
	}

	CitySelector.prototype.bindEvent = function() {
		var $container = this.$container;
		var $element = this.$element;
		var option = this.option;
		var self = this;
		$container.on('click', function(e) {
			return false;
		});

		$container.find('.region').on('click', function() {
			if($(this).hasClass('active')) return;
			$container.find('.region.active').removeClass('active');
			var index = $(this).index();
			$container.find('.region-cities.show').removeClass('show');
			$container.find('.region-cities').eq(index).addClass('show');
			$(this).addClass('active');
		});

		$container.find('.close').on('click', function() {
			$container.hide();
		});

		$container.on('click', '.city', function() {
			var id = $(this).attr('data-id');
			var name = $(this).text();
			self.select({
				id: id,
				name: name
			});
			if($(this).parents('.search-result').length) {
				$clear.trigger('click');
			}
		});

		$container.find('.region').eq(0).trigger('click');

		this.$inputContainer.on('click', function(e) {
			var $target = $(e.target);
			if($target.hasClass('city-remove')) {
				var id = $target.attr('data-id');
				var name = $target.parent().find('.city-name').text();
				self.removeSelectedCity(id);
				$target.parent().remove();
				if(typeof self.option.remove === 'function') {
					self.option.remove({
						id: id,
						name: name
					})
				}
			} else {
				self.$container.show();
			}
			return false;
		});

		var $searchResult = $container.find('.search-result');
		var $tabs = $container.find('.region-tabs');
		var $tabContent = $container.find('.tab-content');
		var $clear = $container.find('.clear');
		var $searchInput = $container.find('.search-input');
		$searchInput.on('input', function() {
			var value = $.trim($(this).val());
			if(value) {
				$tabs.hide();
				$tabContent.hide();
				var matched = self.findCityByName(value);
				self.searchResultRender(matched);
				$searchResult.show();
				$clear.show();
			} else {
				$tabs.show();
				$tabContent.show();
				$searchResult.hide();
				$clear.hide();
				$container.find('.search-result').empty();
			}
		});

		$clear.on('click', function() {
			$searchInput.val('').trigger('input');
		})
	}

	$.fn.citySelector = function(option) {
		return $(this).data('instance', new CitySelector($(this), (option || {})));
	}

	$(document).on('click', function() {
		$('.e-city-selector').hide();
	})
}));
