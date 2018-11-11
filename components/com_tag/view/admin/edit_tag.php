<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: edit_tag.php 902 2007-06-22 16:06:12Z chris $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */
/* Call in Smarty to display template */
bfLoad('bfSmarty');

$controller->setPageTitle(bfText::_('Edit Tag'));
$controller->setPageHeader(bfText::_('Edit Tag'));

/* Create a toolbar, or use a deafult index type toolbar */
$toolbar =& bfToolbar::getInstance($controller);
$toolbar->addButton('save','save', bfText::_('Click here to save the Tag'));
if ($tag['id']){
	$toolbar->addButton('apply','apply', bfText::_('Click here to save changes and return to this Tag'));
}
$toolbar->addButton('cancel','cancel', bfText::_('Click here to loose changes and return to the list of Tags'));
$toolbar->render(true);

$tmp = bfSmarty::getInstance('com_tag');
$tmp->caching = false;
$tmp->assignFromArray($tag);

/* set defaults for blank strings */
if ($tag['meta_keywords']==''){
	$tmp->assign('META_KEYWORDS',$tag['tagname']) ;
}
if ($tag['meta_desc']==''){
	$tmp->assign('META_DESC',bfText::_('All items tagged with') . ' ' . $tag['tagname']) ;
}
if ($tag['meta_title']==''){
	$tmp->assign('META_TITLE',bfText::_('All items tagged with') . ' ' . $tag['tagname']) ;
}

/* Tell the controller to display editors */
$controller->viewHasEditor('desc');

$layouts =& $controller->getModel('layout');
$layouts = $layouts->getLayoutOptions($tag['template'],'template','tag');
$fool =& $controller->getModel('tag');

$tmp->assign('TEMPLATES', 		$layouts ) ;

$content_items = array();
if (isset($tag['rows'])){
	foreach ($tag['rows'] as $ci){
		$content_items[] = array(
		'title'=>$ci->title,
		'onclick'=>'jQuery(this).hide(\'slow\');removeTagFromContent(\''.$ci->id.'\', \''.$tag['id'].'\');'
		);
	}
}
$tmp->assign('content_items', 		$content_items ) ;

if ($tag['id']){
	$tmp->assign('ACCESS', 			bfHTML::drawAccessLinks( $tag['id'], $tag['access'], true, 'tag') );
}else{
	$tmp->assign('ACCESS', bfText::_('Please save tag first'));
}

$tmp->assign('HITSANDRESET', 	bfHTML::drawHitsLinks( 'listing', $tag['id'], $tag['hits']) );

if ($tag['id']){
	if ($tag['published']==1){
		$tmp->assign('PUBLISHED', 	bfHTML::publishInformationDiv($tag['id'],'published') );
	} else {
		$tmp->assign('PUBLISHED', 	bfHTML::unpublishInformationDiv($tag['id'],'published') );
	}
}else{
	$tmp->assign('PUBLISHED', bfText::_('Please save tag first'));
}

$options = array();
$options[] = bfHTML::makeOption('','-- Use Global Preference --');
$options[] = bfHTML::makeOption('ASC','Ascending');
$options[] = bfHTML::makeOption('DESC','Descending');
$layout_dir = bfHTML::selectList2($options,'layout_dir',' class="inputbox"','value','text',$tag['layout_dir']);
$tmp->assign('LAYOUT_DIR',$layout_dir);

$options = array();
$options[] = bfHTML::makeOption('','-- Use Global Preference --');
$options[] = bfHTML::makeOption('hits','Hits');
$options[] = bfHTML::makeOption('title','Title');
$options[] = bfHTML::makeOption('title_alias','Title Alias');
$options[] = bfHTML::makeOption('created','Date Created');
$layout_orderby = bfHTML::selectList2($options,'layout_orderby',' class="inputbox"','value','text',$tag['layout_orderby']);
$tmp->assign('LAYOUT_ORDERBY',$layout_orderby);

$tmp->display('edit_tag.php');

$registry->setValue('script', "new Accordion($$('div#outer table thead'), $$('div#outer table tbody'));");
?>