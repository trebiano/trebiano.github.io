<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfUtils.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */


/**
 * Utilities for the bfFramework
 *
 */
class bfUtils {

	function ampReplace($text){
		$text = str_replace( '&&', '*--*', $text );
		$text = str_replace( '&#', '*-*', $text );
		$text = str_replace( '&amp;', '&', $text );
		$text = preg_replace( '|&(?![\w]+;)|', '&amp;', $text );
		$text = str_replace( '*-*', '&#', $text );
		$text = str_replace( '*--*', '&&', $text );
		return $text;
	}

	function renderCustomFields($customfields, $listing){
		$my =& bfUser::getInstance();

		$html = '';
		/* convert object to array - quickly! */
		//		$i = array();
		//		foreach ($listing['customfields'][0] as $k=>$v){
		//			$arr[$k] = $v;
		//		}

		$i = 0;
		foreach ($customfields as $customfield){
			/* check if we need to display this field */
			if ($customfield->published == 0) continue;
			if ($customfield->access > $my->get('gid')) continue;

			/* get tooltip */
			if ($customfield->tooltip){
				$title = bfHTML::mooToolTip($customfield->friendlyname,$customfield->tooltip);
			}else {
				$title = $customfield->friendlyname;
			}

			/* get HTML form field */
			switch ($customfield->type){
				case 'Textarea WITH WYSIWYG':
					$field = '<textarea mce_editable="true" cols="50" style="width: 100%; height: 200px;" name="'.$customfield->fieldname.'" id="'.$customfield->fieldname.'">'.@$listing['customfields'][$customfield->fieldname].'</textarea></td>';
					$html .= '<tr class="row'.$i.'"><td colspan="2">'.$title.'</td>';
					$html .= '<tr class="row'.$i.'"><td colspan="2">'.$field.'</td></tr>';
					break;
				case 'Textarea NO WYSIWYG':
					$field = '<textarea mce_editable="false" cols="50" style="width: 100%; height: 200px;" name="'.$customfield->fieldname.'" id="'.$customfield->fieldname.'">'.@$listing['customfields'][$customfield->fieldname].'</textarea></td>';
					$html .= '<tr class="row'.$i.'"><td colspan="2">'.$title.'</td>';
					$html .= '<tr class="row'.$i.'"><td colspan="2">'.$field.'</td></tr>';
					break;
				case 'Textbox upto 255 Chars':
				default:
					$field ='<input type="text" name="'.$customfield->fieldname.'" value="'.@$listing['customfields'][$customfield->fieldname].'" class="flatinputbox" maxlength="255" />';
					$html .= '<tr class="row'.$i.'"><td>'.$title.'</td><td>'.$field.'</td></tr>';
					break;
			}




			$i = 1 - $i;
		}

		return $html;
	}

	/**
 * builds a path to a category
 *
 */
	function buildPathToCategoryFromRoot($currentid, $currentname, $parentid, $link, $indent='/', $html=array(), $maxlevel=30, $returnType='html') {
		bfLoad('bfCache');
		/* @var $cache bfCache */
		$cache =& bfCache::getInstance();
		$cachekey = md5( 'com_directory_buildPathToCategoryFromRoot' . $currentid. $currentname. $parentid. $link. $indent. $html. $maxlevel . 'html');
		$cachekey2 = md5( 'com_directory_buildPathToCategoryFromRoot' . $currentid. $currentname. $parentid. $link. $indent. $html. $maxlevel . 'objArr');
		$arr = $cache->get($cachekey,'com_directory_objects');

		if (!$arr){
			$arr = bfUtils::_buildPathToCategoryFromRoot($parentid, $link, array(), $indent, $html, $maxlevel);
			$cache->add($cachekey,$arr[0],'com_directory_objects');
			$cache->add($cachekey2,$arr[1],'com_directory_objects');
			$cache->save();
		}

		$t = array_reverse($arr[0]);

		$html = sprintf($link, 0, 'Root') . ' / ';

		if (count($t)){
			$html .= implode( ' / ' , $t ) . ' / ';
		}
		if ($currentid > 0){
			$html .= ' <a href="index2.php?option=com_directory&task=selectparentcategory&tmpl=component&pop=1&no_html=1&id='.$currentid.'">'.$currentname.'</a> / ';
		}
		return array($html, $arr[1]);
	}

	/**
	 * Do not call - gets called by buildPathToCategoryFromRoot
	 */
	function _buildPathToCategoryFromRoot($parentid, $link, $objArr, $indent, $arr, $maxlevel=30, $level=0) {
		global $mainframe;
		$db =& bfCompat::getDBO();
		if ($parentid > 0  && $level <= $maxlevel) {
			$db->setQuery('Select id, title, parentid FROM #__'.$mainframe->get('component_shortname').'_categorys WHERE id ="'.$parentid.'"');
			$details = $db->loadObject();
			$arr[] =  sprintf($link, $details->id, $details->title);
			$objArr[] = array( 'id'=>$details->id, 'title'=>$details->title);
			return bfUtils::_buildPathToCategoryFromRoot( $details->parentid, $link, $objArr, $indent, $arr, $maxlevel, $level+1 );
		}
		return array($arr, serialize($objArr));
	}

	/**
	 * check if mambot file is installed.
	 *
	 * @param unknown_type $mambot
	 * @return unknown
	 */
	function isMambotInstalled($mambot){
		$parts = explode('.',$mambot);
		$mambotType = $parts[0];
		array_shift($parts);
		$mambotFileName = $mambot;
		//		echo JPATH_ROOT . DS . _PLUGIN_DIR_NAME . DS . $mambotType. DS .  $mambotFileName. '.php';
		if(file_exists(JPATH_ROOT . DS . _PLUGIN_DIR_NAME . DS . $mambotType . DS .  $mambotFileName . '.php')){
			return true;
		} else {
			return false;
		}
	}
	function isModuleInstalled($module){
		//		echo JPATH_ROOT . DS . 'modules' . DS .  $module . '.php';
		if(file_exists(JPATH_ROOT . DS . 'modules' . DS .  $module . '.php')){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Return the application itemid
	 * @deprecated
	 */
	function findItemid(){
		return '@depreciated'; //Joomla 1.5 does this different now ?
		/*
		$itemid = bfRequest::getVar( 'Itemid', 0, '', 'int' );
		$option = strtolower(bfRequest::getVar('option', null));

		if ( $itemid === 0 )
		{
		$menus =& bfMenu::getInstance();
		$item  =& $menus->getDefault();

		$itemid = $item->id;
		}

		bfRequest::setVar( 'Itemid', $itemid );
		return $itemid;
		*/
	}

	/**
	 * Return the application option string [main component]
	 */
	function findOption(){
		$option = strtolower(bfRequest::getVar('option', null));

		if(empty($option))
		{
			$menu =& bfMenu::getInstance();
			$item =& $menu->getItem(bfSiteHelper::findItemid());

			$component = bfTable::getInstance( 'component', bfFactory::getDBO() );
			$component->load($item->componentid);

			$option = $component->option;

			// Lets set any request variables from the menu item url
			$parts = parse_url($item->link);
			if ($parts['query']) {
				$vars = array();
				parse_str($parts['query'], $vars);
				foreach ($vars as $k => $v)
				{
					bfRequest::setVar($k, $v);
				}
			}
		}

		//provide backwards compatibility for frontpage component
		if($option == 'com_frontpage') {
			$option = 'com_content';
			bfRequest::setVar('task', 'frontpage');
		}

		return bfRequest::setVar('option', $option);
	}

	function linkToForm($id = null){
		global $mainframe;
		$session =& bfSession::getInstance();
		/* Set up our registry and namespace - we must use bfRegistry and not subclass it as config gets messed up */
		$registry = bfUtils::getbfRegistry();

		if ($id===null)	{
			$id = (int) $session->get('viewed_form_id',null,'default');
		}
		if (_BF_PLATFORM=='JOOMLA1.5'){
			$link = JRouter::_('index.php?option='.$mainframe->get('component').'&task=viewform&formid='.$id);
		} else {
			$link = sefRelToAbs('index.php?option='.$mainframe->get('component').'&task=viewform&formid='.$id.'&Itemid='.bfCompat::findItemid());
		}
		return $link;
	}

	/**
	 * returns Jregistry with name space for this component
	 *
	 * @return object bfRegistry
	 */
	function getbfRegistry(){
		global $mainframe;
		return bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));
	}

	/**
	 * Fix pathway/breadcrumbs removing unneeded links - Joomla Fudged!
	 * @deprecated
	 */
	function fixPathway(){
		return 'Warning: fixPathway depreciated';
		/*
		$pathway =& bfDocument::getPathWay();

		$arr = array();
		foreach ($pathway->_pathway as $link){
		if (substr($link->name,0,2) != 'bf') $arr[] = $link;
		}
		$pathway->_pathway =& $arr;
		*/
	}

	function jsSafe($str){
		return str_replace("'",'',$str);
	}


	function bfSortArray($x, $y){
		global $mainframe;

		$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));
		$key = $registry->getValue('sortby','weight');

		if ( $x->$key == $y->$key ) {
			return 0;
		} else if ( $x->$key < $y->$key ){
			return 1;
		} else {
			return -1;
		}
	}

}
?>