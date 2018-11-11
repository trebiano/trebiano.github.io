<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: rsslinks.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
global $mainframe;
$controller->setPageTitle(bfText::_('RSS Feeds'));
$controller->setPageHeader(bfText::_('RSS Feeds'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('help','help', bfText::_('Click here to get help'));
$toolbar->render(true);

/* read the addons out of the framework config file */
$tasks = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.RSS Feeds.Links');
?>
<table class="bfadminlist">
	<thead>
		<tr>
			<th><?php echo bfText::_('Feed Description'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	$row = 0;
	foreach ($tasks as $task){

		if (isset($task[3])){
			$link = bfCompat::sefRelToAbs($task[3]);
		} else {
			$link = bfCompat::sefRelToAbs(bfCompat::getLiveSite() . '/index2.php?option='.$mainframe->get('component').'&task=rss&format=RSS2.0&no_html=1&pop=1&type='.$task[0]);
		}

		/* display the row */
		echo sprintf('<tr class="row%s">
		<td><span class="bullet-rss biggerblue indent">%s<br /><small>%s</small>
		<br />
		<input type="text" class="flatinputbox rsslinkbox" value="%s" />
		</span></td>
		</tr>',
		$row,
		$task[1],
		$task[2],
		$link
		);

		/* row zebra colors */
		$row = 1 - $row;
	}
?>
	</tbody>
</table>