<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfSubmenu.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

/**
 * Helper class that displays the left side menu in framework apps
 * */
class bfSubmenu {

	/**
	 * I draw the unordered list for the menu.
	 *
	 */
	function render(){
		global $mainframe;
		/* Do I want to hide the menu? */
		$noSubmenu = bfRequest::getVar('no_html');
		if ($noSubmenu) return ;

		/* Tell the view renderer that I have used popups */
		$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));
		$registry->setValue('hasPopups',1);

		/* Get the submenu items frm the framework config */
		$items = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.submenus');

		/* build the ul */
		echo '<ul class="bfsubmenu">';
		foreach ($items as $item=>$link){
			$item = bfText::_($item);
			$parts = explode('|', $link);
			if ($parts[1]=='preview'){
				$href = '" href="'.$parts[0].'">';
			} else {
				$href = '" href="javascript:void(0);" onclick="killTinyMCE();jQuery(\'div#bf-main-content\').fadeOut(\'fast\');bfHandler(\''.$parts[0].'\');">';
			}
			if (@!$parts[1]) $parts[1] = $item;
				echo '<li class="submenuicon-'. strtolower($parts[1]) .'">';
				echo '<a class="submenuicon-'
				. strtolower($parts[1]) . (@$parts[2] ? ' hasTip"' : '"')
				.' title="'
				.(@$parts[2] ?  $item
				.' :: '
				.@$parts[2] : $item
				.' :: '.$parts[0])
				.$href

				.$item
				.'</a></li>';
		}
		echo '</ul>';
	}
}
?>
