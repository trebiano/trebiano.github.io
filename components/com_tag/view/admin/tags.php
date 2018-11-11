<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: tags.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

if ($registry->getValue('modertaionView') == 1){
	$controller->setPageTitle(bfText::_('View All Unpublished Tags'));
	$controller->setPageHeader(bfText::_('View All Unpublished Tags'));

} else {
	$controller->setPageTitle(bfText::_('View All Tags'));
	$controller->setPageHeader(bfText::_('View All Tags'));
}

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('new','add', 				bfText::_('Click here to add new tag'));
$toolbar->addButton('edit','edit', 				bfText::_('Click here to edit selected tags'));
$toolbar->addButton('delete','remove', 			bfText::_('Click here to delete selected tags'));
$toolbar->addButton('publish','publish', 			bfText::_('Click here to publish selected tags'));
$toolbar->addButton('unpublish','unpublish', 			bfText::_('Click here to unpublish selected tags'));
$toolbar->addButton('help','xhelp',				bfText::_('Click here to view Help and Support Information'));
$toolbar->render(true);

bfHTML::drawIndexTable($tag,true,$controller->getView());
?>