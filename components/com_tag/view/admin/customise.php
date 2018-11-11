<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: customise.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
$controller->setPageTitle(bfText::_('Customise'));
$controller->setPageHeader(bfText::_('Customise'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('help','help', bfText::_('Click here to get help'));
$toolbar->render(true);

/* read the addons out of the framework config file */
$tasks = $registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.Customise.Tasks');

bfLoad('bfButtons');

$buttons = new bfButtons();

?>
<table class="bfadminlist">
	<thead>
		<tr>
			<th><?php echo bfText::_('Customise Task'); ?></th>
			<th width="120">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$row = 0;
	foreach ($tasks as $task){
		$buttons = new bfButtons('left',false);
		$buttons->addButton('ok', 	'\''.$task[0].'\', \''.$task[0].'\'', 'Go', $task[1]);

		/* display the row */
		echo sprintf('<tr class="row%s">
		<td><span class="bullet-%s biggerblue indent"><span class="bold">%s</span><br /><small>%s</small></span></td>
		<td valign="top" id="toggle-%s">%s</td>
		</tr>',
		$row,
		$task[3],
		$task[1],
		$task[2],
		$task[0],
		$buttons->display(true)
		);

		/* row zebra colors */
		$row = 1 - $row;
	}
?>
	</tbody>
</table>
