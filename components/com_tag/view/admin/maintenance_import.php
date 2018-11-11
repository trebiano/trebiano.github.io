<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: maintenance_import.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
$controller->setPageTitle(bfText::_('Import & Export Options'));
$controller->setPageHeader(bfText::_('Import & Export Options'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('help','xhelp',				bfText::_('Click here to view Help and Support Information'));
$toolbar->render(true);
echo 'edit  this file' . __FILE__;
?>