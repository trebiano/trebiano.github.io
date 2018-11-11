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

if (!bfCompat::isAdmin()){
	$controller->setPageTitle(bfText::_('Popular Tags on') . ' ' . bfCompat::getSiteName());
	bfCompat::setMeta('keywords',$tag['meta_keywords']);
	bfCompat::setMeta('description',$tag['meta_desc']);
}

/* Call in Smarty to display template */
bfLoad('bfSmarty');

$tmp = bfSmarty::getInstance('com_tag');
$tmp->caching = false;
$u8 = new bfUtf8();

/* Cloud */
if (@count($tag['items'])){
	$tag_items = array();
	foreach ($tag['items'] as $row){
		foreach ($row as $k=>$v){
			if ($k=='tagname'){
				$t[$k] = $v; //$u8->utf8ToHtmlEntities($v);
			} else {
				$t[$k] = $v;
			}
		}
		$tag_items[] = $t;
	}
	$tmp->assign('items', $tag_items );
} else {
	$tmp->assign('items', array() );
}


/* Cloud */
if (@count($tag['itemsbyhits'])){
	$tag_items = array();
	foreach ($tag['itemsbyhits'] as $row){

		foreach ($row as $k=>$v){
			if ($k=='tagname'){
				$t[$k] = $u8->utf8ToHtmlEntities($v);
			} else {
				$t[$k] = $v;
			}
		}
		$tag_items[] = $t;
	}
	$tmp->assign('itemsbyhits', $tag_items );
} else {
	$tmp->assign('itemsbyhits', array() );
}

/* popular */
if (@count($tag['popularitems'])){
	$tag_items = array();
	foreach ($tag['popularitems'] as $row){

		foreach ($row as $k=>$v){
			if ($k=='tagname'){
				$t[$k] = $u8->utf8ToHtmlEntities($v);
			} else {
				$t[$k] = $v;
			}
		}
		$tag_items[] = $t;
	}
	$tmp->assign('popularitems', $tag_items );
}else {
	$tmp->assign('popularitems', array() );
}

/* Latest */
if (@count($tag['latestitems'])){
	$tag_items = array();
	foreach ($tag['latestitems'] as $row){

		foreach ($row as $k=>$v){
			if ($k=='tagname'){
				$t[$k] = $u8->utf8ToHtmlEntities($v);
			} else {
				$t[$k] = $v;
			}
		}
		$tag_items[] = $t;
	}
	$tmp->assign('latestitems', $tag_items );
}else {
	$tmp->assign('latestitems', array() );
}

if ($registry->getValue('config.tagcloudintegers')==='1'){
	$tmp->assign('showqty', 1 );
}
$tmp->display(md5(8).'.php',true);
?>