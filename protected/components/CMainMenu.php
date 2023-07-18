<?php
Yii::import('zii.widgets.CMenu');

class CMainMenu extends CMenu {
	public $activeParentCssClass;
	/**
	 * 重载CMenu
	 * @param array $item
	 * @param string $route
	 */
	protected function isItemActive($item, $route) {
		if (isset($item['url'])&&is_array($item['url'])) {
			$_base_url = $item['url'][0]; //trim($item['url'][0], '/');
			//把url转换为实际的可访问url
			if ($module = $this->getController()->getModule()) {
				$module_id = $module->getId();
				if ($_base_url[0]!='/') {
					$_base_url = '/'.$module_id.'/'.$_base_url;
				}
			}
			
			$route = explode('/', $route);
			$url = explode('/', trim(ltrim($_base_url, '/'), '/'));
			
			$is_active = 0;
			for($i = 0; $i<count($url); $i++) {
				if (!strcasecmp($url[$i], $route[$i])) {
					$is_active += 1;
				}
			}
			if (count($url)===$is_active) {
				return true;
			}
		}
		return false;
	}
	
	protected function renderMenu($items) {
		if (count($items)) {
			foreach($items as $item) {
				echo CHtml::openTag('ul', $this->htmlOptions)."\n";
				$this->renderMenuRecursive(array (
					$item
				));
				echo CHtml::closeTag('ul');
				echo "\n\n".'<div class="nav-divider">&nbsp;</div>'."\n";
			}
		}

	}
	
	protected function renderMenuItem($item) {
		if (isset($item['url'])) {
			$label = !isset($item['linkLabelWrapper']) ? $item['label'] : '<'.$item['linkLabelWrapper'].'>'.$item['label'].'</'.$item['linkLabelWrapper'].'>';
			return CHtml::link($label, $item['url'], isset($item['linkOptions']) ? $item['linkOptions'] : array ());
		} else
			return CHtml::tag('span', isset($item['linkOptions']) ? $item['linkOptions'] : array (), $item['label']);
	}
	
	protected function renderMenuRecursive($items) {
		$count = 0;
		$n = count($items);
		foreach($items as $item) {
			$count++;
			$options = isset($item['itemOptions']) ? $item['itemOptions'] : array ();
			$class = array ();
			if ($item['active']&&$this->activeCssClass!=''){
				if (isset($item['items'])&&count($item['items'])) {
					$class[] = $this->activeParentCssClass;
				}else{
					$class[] = $this->activeCssClass;
				}
			}
				
			if ($count===1&&$this->firstItemCssClass!='')
				$class[] = $this->firstItemCssClass;
			if ($count===$n&&$this->lastItemCssClass!='')
				$class[] = $this->lastItemCssClass;
			if ($class!==array ()) {
				if (empty($options['class']))
					$options['class'] = implode(' ', $class);
				else {
					$options['class'] .= ' '.implode(' ', $class);
				}
			}
			
			echo CHtml::openTag('li', $options);
			$menu = $this->renderMenuItem($item);
			
			if (isset($this->itemTemplate)||isset($item['template'])) {
				$template = isset($item['template']) ? $item['template'] : $this->itemTemplate;
				echo strtr($template, array (
					'{menu}'=>$menu
				));
			} else {
				echo $menu;
			}
			
			if (isset($item['items'])&&count($item['items'])) {
				if($item['active']){
					echo "\n".'<div class="select_sub show">';
				}else{
					echo "\n".'<div class="select_sub">';
				}
				echo "\n\t".CHtml::openTag('ul', isset($item['submenuOptions']) ? $item['submenuOptions'] : $this->submenuHtmlOptions)."\n";
				$this->renderMenuRecursive($item['items']);
				echo CHtml::closeTag('ul')."\n";
				echo "\n".'</div>';
			
			}
			echo CHtml::closeTag('li')."\n";
		}
	}
}