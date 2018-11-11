<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfSmarty.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

require_once(_BF_FRAMEWORK_LIB_DIR . DS . 'libs' . DS . 'smarty' . DS . 'Smarty.class.php');

class bfSmarty extends Smarty {

	var $_component = '';

	function bfSmarty($component){
		$this->_component = $component;
		$this->__construct($component);

	}

	function __construct($component){
		parent::smarty();
		$this->_component = $component;
		$this->_configurePaths();
		$this->_loadLangFile();

	}

	/**
	 * I implmenet static class
	 *
	 * @return unknown
	 */
	function &getInstance($component=null){
		global $mainframe;
		if ($component===null){
			$component = $mainframe->get('component');
		}
		static $instance;

		if (!isset ($instances)) {
			$instances = array ();
		}

		if (empty ($instances[$component])) {
			$c = __CLASS__;
			$instances[$component] = new $c($component);
			$instances[$component]->_component = $component;
		}
		//		echo $instances[$component]->compile_dir;
		return $instances[$component];
	}

	function _configurePaths(){
		global $mainframe;
		@chmod(_BF_SMARTY_LIB_DIR, 0777 );
		$this->_setTemplatePath();
		$this->templates_c = _BF_SMARTY_LIB_DIR . DS .'templates_c';
		@chmod($this->templates_c, 0777 );
		$this->compile_dir = 	_BF_SMARTY_LIB_DIR . DS .'templates_c' . DS . $this->_component;
		@chmod($this->compile_dir, 0777 );
		$this->cache_dir = 	_BF_SMARTY_LIB_DIR . DS .'cache';
		@chmod($this->cache_dir, 0777 );
		$this->config_dir = 	_BF_SMARTY_LIB_DIR . DS .'configs';
		$this->_checkPathsExist();
	}

	function _checkPathsExist(){
		$this->_check_dir($this->compile_dir);
		$this->_check_dir($this->cache_dir);
	}

	function _check_dir($dirpath) {
		$make_dir = '';
		foreach (split('/', $dirpath) as $subdir) {
			$make_dir .= "$subdir/";

			/* dont create anything higher than the absolute path to joomla */
			$root = bfCompat::getAbsolutePath();
			if (!ereg($root.DS.bfCompat::mambotsfoldername().DS.'system',$make_dir)) {
				continue;
			}

			/* create folder if not exists */
			if (!file_exists($make_dir)) {
				if (@!mkdir($make_dir)){
					die( bfText::_('Could not create smarty template cache folder') . ' : '.$make_dir  );
				} else {
					chmod($make_dir, 0755 );
					if (!file_exists($make_dir."index.php") && file_exists($make_dir)) {
						@touch($dirpath.DS."index.php");
						@chmod($dirpath.DS."index.php", 0655);
					}
				}
			}
		}
	}

	function cache($level=0){
		$this->caching = $level;
	}

	function _setTemplatePath(){
		global $mainframe;
		$this->template_dir = bfCompat::getCfg('absolute_path') . DS .
		'components'
		. DS
		. $this->_component
		. DS
		. 'view'
		. DS
		. (bfCompat::isAdmin() ? 'admin' : 'front')
		. DS
		. 'templates';
	}

	function assignFromArray($arr){
		foreach ($arr as $k=>$v){
			$this->assign(strtoupper($k), $v);
		}
	}

	function arrayFromArrayOfObjects($arr){
		$newArr = array();
		foreach ($arr as $obj){
			$r = array();
			foreach ($obj as $k=>$v){
				$r[$k] = $v;
			}
			$newArr[] =$r;
		}
		//		echo "<pre>";
		//		print_R($newArr);
		//		echo "</pre>";
		return $newArr;
	}

	function _loadLangFile(){
		$arr = array();
		$this->assignFromArray($arr);
	}

	function _setDefaultGlobals(){
		$this->assign('LIVE_SITE', bfCompat::getLiveSite());
		$this->assign('LIVESITE', bfCompat::getLiveSite());
		$this->assign('ABSOULTE_PATH', bfCompat::getAbsolutePath());
		$this->assign('ABSOLUTE_PATH', bfCompat::getAbsolutePath());
		$this->assign('PLUGIN_DIR', bfCompat::getPluginDir() );
		$this->assign('PLUGINS_DIR', bfCompat::getPluginDir() );
		$this->assign('TAGCLOUD_LINK', bfCompat::sefRelToAbs('index.php?option=com_tag&tag=cloud') );
		$this->assign('TAGCLOUD', bfCompat::sefRelToAbs('index.php?option=com_tag&tag=cloud') );
		$this->assign('BLUEFLAMESITE', 'http://www.blueflameit.ltd.uk' );
		if (_BF_PLATFORM=='JOOMLA1.0'){
			global $mainframe;
			$this->assign('LIVETEMPLATE', $mainframe->getTemplate() );
		}

	}

	function display($tmpl_name, $useUserTemplate=false, $usertemplate_id=null, $cache_id=null){
		$this->_setDefaultGlobals();

		if ($useUserTemplate===true ){
			$this->_setTemplatePathToUserDir();
		} else {
			if (!file_exists($this->template_dir . DS . $tmpl_name)){
				$this->_setTemplatePathToFramework();
			}
		}
		parent::display($tmpl_name,$cache_id);
	}

	function render($tmpl_name, $useUserTemplate=false, $compile_id=null, $cache_id=null){
		$this->_setDefaultGlobals();

		if ($useUserTemplate===true ){
			$this->_setTemplatePathToUserDir();
		} else {
			if (!file_exists($this->template_dir . DS . $tmpl_name)){
				$this->_setTemplatePathToFramework();
			}
		}
		return parent::fetch($tmpl_name, $cache_id, $compile_id);
	}


	function _setTemplatePathToUserDir(){
		global $mainframe;
		$this->template_dir = bfCompat::getCfg('absolute_path') . DS .
		'components'
		. DS
		. $this->_component
		. DS
		. 'view'
		. DS
		. 'user_templates';
	}

	function _setTemplatePathToFramework(){
		$this->template_dir = bfCompat::getCfg('absolute_path') . DS
		. _PLUGIN_DIR_NAME
		. DS
		. 'system'
		. DS
		. 'blueflame'
		. DS
		. 'view'
		. DS
		. 'templates';
	}

	function getLayouts( $fieldname, $selected=0, $pertainsto = null){
		global $mainframe;
		$db =& bfCompat::getDBO();

		if ($pertainsto!==null){
			$where = ' WHERE appliesto =\''.$pertainsto.'\'';
		} else {
			$where = '';
		}

		$mysql_table = '#__' . $mainframe->get('component_shortname') . '_layouts';

		/* get the layouts */
		$db->setQuery('SELECT id as value, title as `text` FROM ' . $mysql_table . $where . ' ORDER BY title ASC');
		$layouts = $db->LoadObjectList();

		$temp = array();
		$temp[] = bfHTML::makeOption( '0', '--' .bfText::_('not set').'--' );

		foreach ($layouts as $layout){
			$temp[] = bfHTML::makeOption( $layout->value, $layout->text );
		}

		/* create an html select list */
		$html = bfHTML::selectList2($temp, $fieldname, ' class="flatinputbox"', 'value', 'text', $selected);

		/* return it */
		return $html;
	}

	function getPertainsto($selected){
		global $mainframe;
		$registry =& bfRegistry::getInstance($mainframe->get('component'));

		$pertainsto = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.layout.pertainsto');

		$temp = array();
		$temp[] = bfHTML::makeOption( '','-- Not Set --');

		if (!is_array($pertainsto)){
			echo 'bfFramework_'.$mainframe->get('component_shortname').'.layout.pertainsto '.bfText::_('NOT SET IN REGISTRY');
		} else {
			foreach ($pertainsto as $layout){
				$temp[] = bfHTML::makeOption( $layout, ucwords($layout) );
			}
		}
		/* create an html select list */
		$html = bfHTML::selectList2($temp, 'appliesto', ' class="flatinputbox"', 'value', 'text', $selected);

		/* return it */
		return $html;
	}
}
?>
