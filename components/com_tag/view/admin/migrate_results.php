<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: migrate_results.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
$controller->setPageTitle(bfText::_('Migration Results'));
$controller->setPageHeader(bfText::_('Migration Results'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('help','xhelp',				bfText::_('Click here to view Help and Support Information'));
$toolbar->addButton('refresh','maintenance_migratefromtagscomponent',				bfText::_('Click here to view Help and Support Information'));
$toolbar->render(true);

?>
<div style="text-align: left;">
 <?php echo implode('<br />',$registry->get('migrate.results')); ?>
</div>
