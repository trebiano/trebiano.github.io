<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: tag.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @subpackage Joomla Tags
 * @copyright Copyright (C) 2007 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class Tag extends bfModel {

	/**
     * @var
     * I do lots of stuff with direct access to tables so need to
     * know when I'm in test mode.
     */
	var $_mode;

	var $_controller;
	/**
     * I call the PHP5 Constructor
     *
     */
	function Tag() {
		$this->__construct();
	}

	/**
	 * PHP5 Constructor
	 * I set up the table name, the ORM and the search fields
	 */
	function __construct() {
		parent::__construct('#__tag_tags');

		$this->_hasRating = false;
		/* search_fields is an array of db column names on which the model filters when */
		$this->_search_fields = array('id','tagname');
		// Am I in test mode?
		$session=& bfSession::getInstance();
		$this->_mode=$session->get('mode');
	}

	function clear(){
		return $this->__construct();
	}

	function AddTagIfNotExists( $tagname ){

		$tagname = (string) bfSecurity::cleanVar($tagname,0,'string');
		$tagname = trim($tagname);

		if (function_exists('sef_decode')){
			$tagname = sefdecode('tagname');
		}

		$notAllowed = array('_','-','&','(',')','*',';',':','@','#',"'",'\'');
		foreach ($notAllowed as $not){
			$tagname = str_replace($not,'', $tagname);
		}
		$tagname = str_replace('%20',' ', $tagname);


		/* Prevent blank tags */
		if (trim($tagname)=="") die(bfText::_('Cannot add blank tags'));

		$this->_tbl_key = 'tagname';
		$this->get( $tagname );

		/* this must be a new tag so add it to the database */
		if ($this->id == 0){
			$this->_tbl_key = 'id';
			$this->tagname = $tagname;
			$this->setTagDefaults($tagname);
			$this->store();
			$this->_checkForDBErrors();
		}

		/* returns the tag id */
		return $this->id;
	}

	function getCount(){
		$this->_db->setQuery('SELECT count(id) FROM '.$this->_tbl);
		return $this->_db->LoadResult();
	}

	function saveDetails(){
		$id = $this->_args['id'];
		if ($id){
			parent::saveDetails();
		} else {
			parent::saveDetails();
			$this->setTagDefaults($this->tagname);
			$this->store();
		}

	}

	/**
	 * Sets the defaults for new tags
	 *
	 */
	function setTagDefaults($tagname=null){
		if ($tagname===null){
			$tagname = $this->tagname;
		}
		$this->access =  $this->_registry->getValue('config.newacceslevel')  ;
		$this->count = 0;
		$this->countpublished = 0;
		$this->published = ( $this->_registry->getValue('config.holdformoderation') ? '0' : '1') ;
		$this->parenttagid = '-1';
		$this->meta_title = (bfText::_('All items tagged with').' '.$tagname);
		$this->meta_desc = (bfText::_('A list of all items tagged with'). ' ' .$tagname);
		$this->sef = md5('/tag/'.$tagname);
	}

	/**
	 * I delete a tag and then calla  fnction to delete all 
	 * maps to avoid dust
	 *
	 * @param string $id The tag id
	 */
	function delete($id){
		parent::delete($id);

		/* remove all maps */
		$map =& $this->_getModel('tag_map');
		$map->removeTag($id);
	}

	function fix_removeBlanks(){
		$db =& bfCompat::getDBO();
		$db->setQuery('DELETE FROM ' . $this->_tbl .' WHERE tagname=""');
		$db->query();
	}


	/* resets hits on one row */
	function resetHits($id){
		$db =& bfCompat::getDBO();
		$db->setQuery('UPDATE ' . $this->_tbl .' SET hits = 0 WHERE ' . $this->_tbl_key . ' = ' . (int) $id);
		$db->query();
	}

	/* resets hits on one row */
	function resetAllHits(){
		$db =& bfCompat::getDBO();
		$db->setQuery('UPDATE ' . $this->_tbl .' SET hits = 0 ');
		$db->query();
	}

	function getLatestTags(){
		$limit = $this->_registry->getValue('config.latestlimit',5);

		bfLoad('bfCache');
		$cache =& bfCache::getInstance();
		$user =& bfUser::getInstance();
		$db =& bfCompat::getDBO();

		$sql = "SELECT t.id as tagid, tagname as tag, t.hits as hits
			FROM #__tag_tags as t
			WHERE t.published = '1'
			AND t.access <='".$user->get('gid')."'
			AND t.parenttagid = '-1'
			ORDER BY t.id DESC
			LIMIT ".$limit;

		$cached = $cache->get(md5($sql),'sql');
		if ($cached){
			$this->latestitems = $cached;
		} else {
			$db->setQuery($sql);
			$latestitemsTags = $db->LoadObjectList();
			$this->latestitems = array();
			foreach ($latestitemsTags as $t){
				$link = bfCompat::sefRelToAbs('index.php?option=com_tag&tag='.$t->tag.'&tag_id='.$t->tagid);
				$this->latestitems[] = array('link'=>$link, 'hits'=>$t->hits, 'tagname'=>$t->tag);
			}
			$cache->api_add(md5($sql),$this->latestitems,'sql');
			$cache->save();
		}
		return $this->latestitems;
	}

	/**
	 * used for the module.
	 *
	 */
	function getPopularTags(){
		$limit = $this->_registry->getValue('config.popularlimit',5);

		bfLoad('bfCache');
		$cache =& bfCache::getInstance();
		$user =& bfUser::getInstance();
		$db =& bfCompat::getDBO();

		$sql = "SELECT t.id as tagid, tagname as tag, t.hits as hits
			FROM #__tag_tags as t
			WHERE t.published = '1'
			AND t.access <='".$user->get('gid')."'
			AND t.parenttagid = '-1'
			AND t.hits > 0
			ORDER BY t.hits DESC
			LIMIT ".$limit;

		$cached = $cache->get(md5($sql),'sql');
		if ($cached){
			$this->popularitems = $cached;
		} else {
			$db->setQuery($sql);
			$popularTags = $db->LoadObjectList();
			$this->popularitems = array();
			foreach ($popularTags as $t){
				$link = bfCompat::sefRelToAbs('index.php?option=com_tag&amp;tag='.$t->tag.'&amp;tag_id='.$t->tagid);
				$this->popularitems[] = array('link'=>$link, 'hits'=>$t->hits, 'tagname'=>$t->tag);
			}
			$cache->api_add(md5($sql),$this->popularitems,'sql');
			$cache->save();
		}
		return $this->popularitems;
	}

	function get($id){

		parent::get($id);


		$task = $this->_session->getMode();
		if ($task=='xedit' OR $task == 'xapply'){
			/* get content items this tag uses! */
			$this->getContentItems(true, true);
		}
	}

	function generateCloud($cloudOnly=false){
		set_time_limit(898789);
		$user =& bfUser::getInstance();

		bfLoad('bfCache');
		$cache =& bfCache::getInstance();
		$db =& bfCompat::getDBO();

		//		$db->SetQuery('SET names utf8');
		//		$db->query();

		/* by useage */
		$sql = "SELECT t.id as tagid, tagname as tag, t.countpublished AS quantity
			FROM #__tag_tags as t
			WHERE t.published = '1'
			AND t.access <='".$user->get('gid')."'
			AND t.countpublished > 0
			AND t.count > 0
			AND t.parenttagid = '-1'
			ORDER BY tag ASC";
		$cached = $cache->get(md5($sql),'sql');
		if ($cached){
			$this->items = $cached;
		} else {

			$db->setQuery($sql);

			$allTags = $db->loadObjectList();
			$this->_checkForDBErrors();

			if (!count($allTags)){
				return  array();
			}
			$tags = array();
			foreach ($allTags as $row){
				$tags[$row->tag] = $row->quantity;
			}

			// change these font sizes if you will
			$max_size = 250; // max font size in %
			$min_size = 100; // min font size in %

			// get the largest and smallest array values
			$max_qty = max(array_values($tags));
			$min_qty = min(array_values($tags));

			// find the range of values
			$spread = $max_qty - $min_qty;
			if (0 == $spread) { // we don't want to divide by zero
				$spread = 1;
			}

			// determine the font-size increment
			// this is the increase per tag quantity (times used)
			$step = ($max_size - $min_size)/($spread);

			$this->items = array();
			// loop through our tag array
			foreach ($allTags as $t) {

				// calculate CSS font-size
				// find the $value in excess of $min_qty
				// multiply by the font-size increment ($size)
				// and add the $min_size set above
				$size = $min_size + (($t->quantity - $min_qty) * $step);
				// uncomment if you want sizes in whole %:
				$size = ceil($size);
				// you'll need to put the link destination in place of the #
				// (assuming your tag links to some sort of details page)
				$link = bfCompat::sefRelToAbs('index.php?option=com_tag&tag='.$t->tag.'&tag_id='.$t->tagid);
				$this->items[] = array('link'=>$link, 'size'=>$size, 'qty'=>$t->quantity, 'tagname'=>$t->tag);
			}
			$cache->api_add(md5($sql),$this->items,'sql');
			$cache->save();

		}

		/* by hits */
		$sql = "SELECT t.id as tagid, tagname as tag, t.countpublished AS quantity, t.hits as hits
			FROM #__tag_tags as t
			WHERE t.published = '1'
			AND t.access <='".$user->get('gid')."'
			AND t.countpublished > 0
			AND t.count > 0
			AND t.parenttagid = '-1'
			ORDER BY tagname ASC";
		$cached = $cache->get(md5($sql),'sql');
		if ($cached){
			$this->itemsbyhits = $cached;
		} else {

			$db->setQuery($sql);

			$allTags = $db->loadObjectList();
			$this->_checkForDBErrors();

			if (!count($allTags)){
				return  array();
			}
			$tags = array();
			foreach ($allTags as $row){
				$tags[$row->tag] = $row->hits;
			}

			// change these font sizes if you will
			$max_size = 250; // max font size in %
			$min_size = 100; // min font size in %

			// get the largest and smallest array values
			$sql2 = "SELECT MAX(hits)
			FROM #__tag_tags as t
			WHERE t.published = '1'
			AND t.access <='".$user->get('gid')."'
			AND t.countpublished > 0
			AND t.count > 0
			AND t.parenttagid = '-1'";
			$db->setQuery($sql2);
			$max_qty = $db->LoadResult();

			$sql3 = "SELECT MIN(hits)
			FROM #__tag_tags as t
			WHERE t.published = '1'
			AND t.access <='".$user->get('gid')."'
			AND t.countpublished > 0
			AND t.count > 0
			AND t.parenttagid = '-1'";
			$db->setQuery($sql3);
			$min_qty = $db->LoadResult();

			// find the range of values
			$spread = $max_qty - $min_qty;
			if (0 == $spread) { // we don't want to divide by zero
				$spread = 1;
			}

			// determine the font-size increment
			// this is the increase per tag quantity (times used)
			$step = ($max_size - $min_size)/($spread);

			$this->itemsbyhits = array();
			// loop through our tag array
			foreach ($allTags as $t) {

				// calculate CSS font-size
				// find the $value in excess of $min_qty
				// multiply by the font-size increment ($size)
				// and add the $min_size set above
				$size = $min_size + (($t->hits - $min_qty) * $step);
				// uncomment if you want sizes in whole %:
				//				 $size = ceil($size);
				// you'll need to put the link destination in place of the #
				// (assuming your tag links to some sort of details page)
				$link = bfCompat::sefRelToAbs('index.php?option=com_tag&tag='.$t->tag.'&tag_id='.$t->tagid);
				$this->itemsbyhits[] = array('link'=>$link, 'size'=>$size, 'qty'=>$t->hits, 'tagname'=>$t->tag);
			}
			$cache->api_add(md5($sql),$this->itemsbyhits,'sql');
			$cache->save();

		}

		if ($cloudOnly !=true){
			/* popular*/
			$this->getPopularTags();
			/* latest */
			$this->getLatestTags();
		}

		return $this->items;
	}

	function getContentItems($bypassCache=false, $getAll = false){

		if ( $this->_registry->getValue('tag.multiple') === true ){
			$ids = $this->_registry->getValue('tag.tagids');
			$ids = implode(',', $ids);
			$where = "WHERE m.tagid IN ( {$ids} )";
		} else {
			$where = 'WHERE m.tagid = \''.$this->id.'\'';
		}

		if ($getAll===true){
			$limit = 500;
		} else {
			$limit = bfRequest::getVar('limit',$this->_registry->getValue('config.limitperpage','ASC'),'request','int');
		}
		$limitstart = bfRequest::getVar('limitstart',0,'request','int');
		$order = bfRequest::getVar('order',	(@$this->layout_orderby ? $this->layout_orderby : $this->_registry->getValue('config.order','created')),'request','string');
		$dir = bfRequest::getVar('dir',	(@$this->layout_dir ? $this->layout_dir : $this->_registry->getValue('config.orderdir','ASC')),'request','string');


		/* XSS */
		$order = bfSecurity::cleanVar($order,0,'string');
		if ($dir!='ASC'){
			if ($dir !='DESC'){
				$dir = 'ASC';
			}
		}

		bfLoad('bfCache');
		$cache =& bfCache::getInstance();

		$sql = 'SELECT DISTINCT c.*, c.id as contentid, u.name as author FROM #__content as c
		LEFT JOIN #__tag_category_map AS m ON m.contentid = c.id
		LEFT JOIN #__users AS u ON u.id = c.created_by
		'.$where.' 
		ORDER BY c.'.$order. ' ' . $dir .'
		LIMIT '.$limitstart.', '.$limit;

		$cached = $cache->get(md5($sql),'sql');
		if ($cached && $bypassCache == false && 1==2){
			$this->rows = $cached;
		} else {
			$db =& bfCompat::getDBO();
			$db->setQuery($sql);
			$this->rows = $db->LoadObjectList();
			$this->_checkForDBErrors();
			$cache->api_add(md5($sql),$this->rows,'sql');
			$cache->save();
		}

		/* for page nav */
		$sql = 'SELECT count(DISTINCT c.id) FROM #__content as c
		LEFT JOIN #__tag_category_map AS m ON m.contentid = c.id
		'.$where.' 
		ORDER BY c.'.$order. ' ' . $dir;
		$cached = $cache->get(md5($sql),'sql');
		if ($cached && $bypassCache == false){
			$this->totalrowscount = $cached;
		} else {
			$db =& bfCompat::getDBO();
			$db->setQuery($sql);
			$this->totalrowscount = $db->LoadResult();
			$this->_checkForDBErrors();
			$cache->api_add(md5($sql),$this->totalrowscount,'sql');
			$cache->save();
		}

		$this->rowscount = count($this->rows);
		return $this->rows;
	}

	function updateListingTotal($dir, $id){
		$db =& bfCompat::getDBO();
		if ($dir=='1') $dir = '+1';
		$db->setQuery(
		'UPDATE #__tag_tags SET
			count = count ' . $dir . '
			, countpublished = countpublished ' . $dir . '
			WHERE id = \''.$id.'\' '
			);
			$db->query();
	}

}
?>
