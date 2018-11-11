<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: footer.php 827 2007-06-12 18:03:41Z phil $
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
/* @var $tmp bfSmarty */
$tmp = bfSmarty::getInstance('com_tag');
$user =& bfUser::getInstance();

$templateFile = md5($registry->get('template')).'.php';

$tmp->caching = false;
$tmp->compile_id = md5('footer');

$tag_maps = array();
$u8 = new bfUtf8();

foreach ($tag_map['rows'] as $row){

	foreach ($row as $k=>$v){
//		$v = $u8->utf8ToHtmlEntities($v);
		// echo $k .' = ' .$v .'<br />';
		if ($k=='url'){
			$v = bfCompat::sefRelToAbs($v);
		}
		// echo $k .' = ' .$v .'<br />';
		$t[$k] = $v;
	}
	$tag_maps[] = $t;
}

$row = $registry->getValue('row');
$tmp->assign('ID',$row->id);
$tmp->assign('mmm',$tag_maps);
$tmp->assign('CONTENT_ITEM', $row->text );

$tmp->assign('taglinelocation',$registry->getValue('config.footerlocation'));
$tmp->assign('technorati',$registry->getValue('config.technorati'));

if ($registry->getValue('config.showsocialbookmarks')==='1'){
	bfLoad('bfSocialBookmark');
	$n = new bfSocialBookmarks();
	$n->setArticleDetail(bfCompat::getLiveSite() . '/index.php?' . $_SERVER['QUERY_STRING'],'My Title');
	$tmp->assign('SOCIALBOOKMARKS',$n->toHTML());
}

/* are we allowed to add tags ? */
if ($registry->getValue('config.allowfrontendsubmission',0)=='1'){
	$tmp->assign('allowfrontendsubmission',1);
}
if ($registry->getValue('config.frontendsubmissionaccesslevel',0) <= $user->get('gid')){
	$tmp->assign('frontendsubmissionaccesslevel',1);
}
$tmp->display($templateFile, true, md5($row->text));



?>