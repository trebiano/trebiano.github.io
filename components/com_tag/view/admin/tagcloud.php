<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: tagcloud.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

$controller->setPageTitle(bfText::_('Tag Cloud Preview'));
$controller->setPageHeader(bfText::_('Tag Cloud Preview'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);

$toolbar->addButton('help','xhelp',				bfText::_('Click here to view Help and Support Information'));
$toolbar->render(true);

include(bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'view' . DS . 'front' . DS . 'tagcloud.php');
?>