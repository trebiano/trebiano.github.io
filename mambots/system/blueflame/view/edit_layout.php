<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: edit_layout.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
$controller->setPageTitle(bfText::_('Edit Layout'));
$controller->setPageHeader(bfText::_('Edit Layout'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('save','save', bfText::_('Click here to save the category'));
$toolbar->addButton('apply','apply', bfText::_('Click here to save changes and return to this category'));
$toolbar->addButton('cancel','cancel', bfText::_('Click here to loose changes and return to the list of categories'));
$toolbar->render(true);

/* Tell the controller to display editors */
$controller->viewHasEditor('html');

/* Call in Smarty to display template */
bfLoad('bfSmarty');

$tmp = bfSmarty::getInstance('framework');
$tmp->assignFromArray($layout);

if ($layout['appliesto']==='framework'){
	$appliesto = bfHTML::mooToolTip(bfText::_('Used by the framework'),bfText::_('This template is a system template and cannot be deleted, it is required for the correct operation of the component'));
} else {
	$appliesto =  $tmp->getPertainsto($layout['appliesto']);
}
$tmp->assign('APPLIESTO',$appliesto);
$tmp->assign('FILENAME',$layout['filename']);
$tmp->display('edit_layout.php');
?>