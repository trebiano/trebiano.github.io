<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: layouts.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

/**
 * The following objects are available to the view:
 *
 * $registry 	- The components registry object
 * $document	- The JDocument Object
 * $controller 	- The current controller
 * $session		- The components current session (bfSession)
 * $toolbar		- The toolbar object
 * $log			- The log object
 * $[modelname] - Any models accessed in the controller can be accesed in the view by their name
 * 						Where $[modelname] is the non-plural name of the model. Eg. $fortune
 *
 * In the view you should:
 * 1. Set a page title: 					$controller->setPageTitle(bfText::_('Welcome'));
 * 2. Set a page Header: 					$controller->setPageHeader(bfText::_('Welcome'));
 * 3. Create a toolbar:
 * 		bfMenuBar::title( '<div id="bfHeader">'.$controller->getPageHeader() .'</div>', 'bflogo' );
 * 	    bfMenuBar::help('@todo.test');
 *      $toolbar->render(true);
 * 4. Display any HTML you need for the view, the rows loaded into the module can be accessed by
 * 		$modelname['rows']  // Where $modelname is the non-plural name of the model. Eg. $fortune
 *
 */

$controller->setPageTitle(bfText::_('Template layouts'));
$controller->setPageHeader(bfText::_('Template layouts'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('new','add', 				bfText::_('Click here to add new item'));
$toolbar->addButton('edit','edit', 				bfText::_('Click here to edit selected item'));
$toolbar->addButton('delete','remove', 			bfText::_('Click here to delete selected items'));
$toolbar->addButton('help','xhelp',				bfText::_('Click here to view Help and Support Information'));
$toolbar->render(true);

bfHTML::drawIndexTable($layout,false,$controller->getView());
?>