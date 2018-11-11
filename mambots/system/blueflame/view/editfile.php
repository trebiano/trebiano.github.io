<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: editfile.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
$controller->setPageTitle(bfText::_('Edit File'));
$controller->setPageHeader(bfText::_('Edit File'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('save','savefile', bfText::_('Click here to save the file'));
$toolbar->addButton('cancel','css', bfText::_('Click here to loose changes and return to the list of files'));
$toolbar->render(true);


$fd = $registry->getValue('fileDetails');
$fileContents = $fd['fileContents'];
$fileName = $fd['fileName'];

?>
<table class="bfadminlist">
		<thead>
	    	<tr>
				<th><span class="indent bullet-config"><?php echo bfText::_("Editing File"); ?>: <?php echo $fileName; ?></a></th>
			</tr>

		</thead>
		<tbody>
		<tr><td>
<input type="hidden" name="fileName" id="fileName" value="<?php echo $fileName; ?>" />


<textarea
 name="fileContents" id="fileContents"
  style="width:100%; height: 100%; min-height: 400px;" class="inputbox bfflatinputbox"><?php echo $fileContents; ?></textarea>

</td></tr>
</tbody>
</table>