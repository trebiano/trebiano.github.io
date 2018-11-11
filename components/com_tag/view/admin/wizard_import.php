<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: wizard_import.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

if ($registry->getValue('done',false)===1){
	$controller->setPageTitle(bfText::_('Import Complete'));
	$controller->setPageHeader(bfText::_('Import Complete'));
	echo '<h1>'.bfText::_('Import Complete').'</h1>';
	return;
}

$controller->setPageTitle(bfText::_('Import from Content Meta Tags'));
$controller->setPageHeader(bfText::_('Import from Content Meta Tags'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('refresh','maintenance_importmetatags', 				bfText::_('Refresh'));
$toolbar->render(true);

$options = array();
$options[] = bfHTML::makeOption('comma', ('Comma Separated'));
$options[] = bfHTML::makeOption('space', ('Space Separated'));
$separator = bfHTML::selectList2($options, 'separator',' class="inputbox"', 'value', 'text', 'comma');



$db =& bfCompat::getDBO();
$db->setQuery("SELECT metakey FROM #__content");
$contentItems = $db->loadObjectList();
$keys = '';
foreach ($contentItems as $item){
	if (strlen($item->metakey)>=1){
		$keys .= $item->metakey .', ';
	}
}
$keys = explode(',',$keys);
$keywords='';
foreach ($keys as $key){
	if (strlen($key)>=1)
	$keywords .= trim($key) . "\n";
}
$keywords = trim($keywords);
?>

<div style="text-align: left">

<p><?php echo bfText::_('This option allows you to import your keywords from the content meta tags.'); ?></p>
<br />
<p><?php echo bfText::_('It is a simple process that looks at every content items keywords, adds those keywords as tags (if they dont exist) and then applies the newly created tag to the same content item.'); ?></p>
<br />
<p><?php echo bfText::_('Your current list of keywords are as follows, all of these will be imported and tagged to content:'); ?></p>
<br /><br />
<p>
<strong><?php echo $keywords; ?></strong>
</p>
<br /><br />
<input type="button" class="button bfbutton flatbutton bfflatbutton" onclick="bfHandler('ximportkeywords');" value="<?php echo bfText::_('Click Here To Run Import'); ?>" />
</div>
