<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: submenu.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

/**
 * I return the submenu as defined in the framework.config.php
 *
 * @return string HTML
 */
function getSubmenu() {
	global $mainframe;
	$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));
	$submenus = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.submenus');

	$html  = '<div class="submenu-pad">';
	$html .= '	<div class="submenu-box">';
	$html .= '		<div class="submenu-pad">';
	$html .= '			<ul id="submenu">';

	$i=1;
	foreach ($submenus as $menutext => $submenu) {
		if ($submenu=='xconfiguration'){
			if ( bfSecurity::checkPermissions('Admin.EditConfiguration','',false) === false){
				continue;
			}
		}
		$i == 1 ? $class = ' class="active"' :	$class = 'class =""';

		if (ereg('index.php',$submenu)){
			$url = bfURI::resolve($submenu);
			$html .= "<li class=\"item-smenu\"><a href=\"$url\" id=\"$submenu\"$class target=\"_blank\">".bfText::_($menutext)."</a></li>\n";
		} else {
			$html .= "<li class=\"item-smenu\"><a onClick=\"selectSubmenuItem(this, '$submenu');\" id=\"$submenu\"$class>".bfText::_($menutext)."</a></li>\n";
		}
		$i++;
	}
	$html .= '			</ul>';
	$html .= '			<div class="clr"></div>';
	$html .= '		</div>';
	$html .= '  </div>';
	$html .= '</div>';
	$html .= '<div class="clr"></div>';
	return $html;
}
?>
