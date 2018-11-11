<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: frontpage.php 1100 2007-07-13 16:10:30Z phil $
 * @package bfFramework
 * @subpackage Joomla Tags
 * @copyright Copyright (C) 2007 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

$user =& bfUser::getInstance();
$limit = bfRequest::getVar('limit',$registry->getValue('config.limitperpage','ASC'),'request','int');
$limitstart = bfRequest::getVar('limitstart',0,'request','int');
/* This is the way in admin */
$controller->setPageTitle($tag['meta_title']);

bfCompat::setMeta('title',$tag['meta_title']);
bfCompat::setMeta('keywords',$tag['meta_keywords']);
bfCompat::setMeta('description',$tag['meta_desc']);

/* Check if this tag is published */
if ($tag['published']=='0'){
	bfError::raiseError('403','This tag is not published');
	return;
}

/* check if this tag is within our access level */
if ($tag['access'] > $user->get('gid')){
	bfError::raiseError('403','You do not have access rights to this tag');
	return;
}

//* Call in Smarty to display template */
bfLoad('bfSmarty');
/* select right user template to display */
if ($tag['template'] > 0){
	$templateFile = md5($tag['template']).'.php';
} else {
	/* c4ca4238a0b923820dcc509a6f75849b */
	$templateFile = md5($registry->getValue('config.defaultTemplate')).'.php';
}

//* @var $tmp bfSmarty */
$tmp = bfSmarty::getInstance('com_tag');
$tmp->caching = false;
$tmp->compile_id = md5($tag['tagname'] . $limit . $limitstart);
$tmp->assign('TAGID',$tag['id']);
$tmp->assign('TAGDESC',$tag['desc']);
$u8 = new bfUtf8();

$tmp->assign('tagtext', $u8->utf8ToHtmlEntities($tag['desc'] ));
if ($registry->getValue('tag.multiple') === true){
	$tmp->assign('TAGNAME', $u8->utf8ToHtmlEntities( implode(' &amp; ', $registry->getValue('tag.tagnames')) ));
} else {
	$tmp->assign('TAGNAME', $u8->utf8ToHtmlEntities($tag['tagname'] ));
}



$tag_contentitems = array();
foreach ($tag['rows'] as $row){

	/* fire content mambots */
	if (_BF_PLATFORM=='JOOMLA1.0'){
		global $_MAMBOTS, $mainframe;
		$_MAMBOTS->loadBotGroup( 'content' );
		$params = new mosParameters("image=1\nintrotext=1");
		$page = 0;
		$row->text = $row->introtext;
		$results = $_MAMBOTS->trigger( 'onPrepareContent', array( &$row, &$params, $page ), true );
	}
	foreach ($row as $k=>$v){
		$t[$k] = $v;
	}

	$t['url'] = bfCompat::sefRelToAbs('index.php?option=com_content&task=view&id='.$row->contentid); //.'&Itemid='.bfCompat::getItemID($row->id));
	//	$t['url'] = str_replace('&','&amp;',$t['url']);
	$tag_contentitems[] = $t;
}

$tmp->assign('content_items', $tag_contentitems );
$tmp->assign('COUNT', $tag['totalrowscount'] );
$tmp->assign('TAGCLOUD_LINK', bfCompat::sefRelToAbs('index.php?option=com_tag') );
$html = $tmp->render($templateFile, true, md5($tag['tagname']));

/* page navigation */
if (ereg('##PAGENAV_',$html)){
	bfLoad('bfPagination');
	$pageNav = new bfPageNav($tag['totalrowscount'], $limitstart, $limit);
	$link = 'index.php?option=com_tag&amp;tag_id='.$tag['id'].'&amp;tag='.$tag['tagname'].'&amp;Itemid='.bfCompat::getComponentItemID();
	$links = $pageNav->writePagesLinks($link);
	$limitbox = $pageNav->writeLimitBox($link);

	$html = str_replace('##PAGENAV_LINKS##',$links,$html);
	$html = str_replace('##PAGENAV_LIMITBOX##',$limitbox,$html);
	$html = str_replace('##PAGENAV_INFO##',$pageNav->writeLeafsCounter(),$html);
	echo $html;
} else {
	echo $html;
}
?>