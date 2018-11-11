<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: patch.php 1008 2007-07-09 12:26:21Z chris $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

$controller->setPageTitle(bfText::_('Patch Joomla Files'));
$controller->setPageHeader(bfText::_('Patch Joomla Files'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('refresh','patch', 				bfText::_('Click here to add new tag'));
$toolbar->addButton('help','xhelp',				bfText::_('Click here to view Help and Support Information'));
$toolbar->render(true);

bfLoad('bfButtons');
$button = new bfButtons('left', true);

function isPatched($file, $return=false){
	if (!file_exists($file)){
		echo '<span class="red">'.bfText::_('File not writeable').'</span>';
		return;
	}
	$contents = file_get_contents($file);

	if (ereg('Joomla Tags',$contents)){
		if ($return===true) return true;
		echo '<span class="green">'.bfText::_('Patched').'</span>';
	} else {
		if ($return===true) return false;
		echo '<span class="red">'.bfText::_('NOT PATCHED').'</span>';
	}
}

function patchButton($file, $filename){
	if (!file_exists($file)){
		echo '<span class="red">'.bfText::_('File not writeable').'</span>';
		return;
	}
	if (isPatched($file, true) && isWriteable($file)){
		echo '<div class="commonButton hasTip" id="bid-cancel" onclick="bfHandler(\'xdounpatch\', \''.$filename.'\');">
		<button name="bname_cancel" onclick="return false;">Unpatch</button><span>Unpatch</span></div>';
	} elseif ( (false===isPatched($file, true)) && isWriteable($file)){ 
		echo '<div class="commonButton hasTip" id="bid-ok" onclick="bfHandler(\'xdopatch\', \''.$filename.'\');">
		<button name="bname_ok" onclick="return false;">Patch</button><span>Patch</span></div>';
	} else{
		echo '<span class="red">'.bfText::_('File not writeable').'</span>';
	}
}

function isWriteable($file){
	@chmod($file,0777);
	if (is_writable($file)){
		return true;
	} else {
		return false;
	}
}

?>
<table class="bfadminlist">
	<thead>
		<tr>
			<th>Patches Required</th>
			<th width="120">Status</th>
			<th width="120"></th>
		</tr>
	</thead>
	<tbody>
	<tr class="row0">
		<td><span class="bullet-config biggerblue indent">
		<span class="bold"><?php echo bfText::_('Allows adding of tags on the admin edit content screen') ?></span>
		<small><?php echo  DS . 'administrator' . DS . 'components' . DS . 'com_content'. DS; ?>admin.content.html.php</small></span><br/></td>
		
		<td align="center">
		<?php
		isPatched(bfCompat::getAbsolutePath() . DS .  'administrator' . DS . 'components' . DS . 'com_content'. DS. 'admin.content.html.php');
		?>
		</td>
		<td valign="top" id="toggle-admincontenthtmlphp">
		<?php patchButton(
		bfCompat::getAbsolutePath() . DS .  'administrator' . DS . 'components' . DS . 'com_content'. DS. 'admin.content.html.php',
		'admin.content.html.php');
		
		 ?>
		</td>
	</tr>
	<tr class="row1">
		<td><span class="bullet-config biggerblue indent"><span class="bold">
		<?php echo bfText::_('Allows adding of tags on the admin edit static content screen') ?></span>
		<small><?php echo  DS . 'administrator' . DS . 'components' . DS . 'com_typedcontent'. DS; ?>admin.typedcontent.html.php</span><br/></small></td>
		<td align="center">
		<?php
		isPatched(bfCompat::getAbsolutePath() . DS .  'administrator' . DS . 'components' . DS . 'com_typedcontent'. DS. 'admin.typedcontent.html.php');
		?></td>
		<td valign="top" id="toggle-admintypedcontenthtmlphp">
		<?php
patchButton(
		bfCompat::getAbsolutePath() . DS .  'administrator' . DS . 'components' . DS . 'com_typedcontent'. DS. 'admin.typedcontent.html.php',
		'admin.typedcontent.html.php');
		
		?>
		</td>
	</tr>
	<tr class="row0">
		<td><span class="bullet-config biggerblue indent"><span class="bold">
		<?php echo bfText::_('Allows adding of tags on the frontend edit content screen') ?><br /></span>
		<small>
		<?php echo  DS . 'components' . DS . 'com_content'. DS; ?>content.html.php
		</small></span></td>
		
		<td align="center">
		<?php
		isPatched(bfCompat::getAbsolutePath() . DS .  'components' . DS . 'com_content'. DS. 'content.html.php');
		?>
		</td>
		<td valign="top" id="toggle-contenthtmlphp">
		<?php
		patchButton(bfCompat::getAbsolutePath() . DS .  'components' . DS . 'com_content'. DS. 'content.html.php', 'content.html.php');
		
		?>
		</td>
	</tr>
	
	</tbody>
</table>
