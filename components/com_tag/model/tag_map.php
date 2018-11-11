<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: tag_map.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class Tag_map extends bfModel {

	/**
     * @var
     * I do lots of stuff with direct access to tables so need to
     * know when I'm in test mode.
     */
	var $_mode;
	/**
     * I call the PHP5 Constructor
     *
     */
	function Tag_map() {
		$this->__construct();
	}

	/**
	 * PHP5 Constructor
	 * I set up the table name, the ORM and the search fields
	 */
	function __construct() {
		global $mainframe;
		parent::__construct('#__'.$mainframe->get('component_shortname').'_category_map');

		/* search_fields is an array of db column names on which the model filters when */
		$this->_search_fields = array('id');

		// Am I in test mode?
		$session=& bfSession::getInstance();
		$this->_mode = $session->get('mode');
	}
	
	function clear(){
		return $this->__construct();
	}

	function getTagsForContentId($id, $andUnpublished=false, $scope="default"){
		
		if ($scope !== "default"){
			$and = "AND scope = '".$scope."'";
		} else {
			$and = "";
		}
		
		$pub = '';
		if ($andUnpublished === false) {
			$pub = "  AND d.published='1'";
		}
		$db =& bfCompat::getDBO();
		$db->setQuery("SELECT m.tagid as id, d.tagname as tagname
		FROM #__tag_category_map as m
		LEFT JOIN #__tag_tags as d ON m.tagid = d.id
		WHERE m.contentid='".$id."' ".$pub
		
		. $and
		
		." ORDER BY tagname");
		$rows = $db->loadObjectList();

		foreach ($rows as $row){
			$row->url = 'index.php?option=com_tag&amp;tag='.$row->tagname.'&amp;tag_id='.$row->id;
			$this->rows[] = $row;
		}
		if (!isset($this->rows)){
			$this->rows = array();
		}
		return $this->rows;
	}

	/* remove a map by its id */
	function removeMap($id){
		$this->load($id);
		$this->delete($id);
		/* update category totals */
		$cat =& $this->_getModel('tag');
		$cat->updateListingTotal('-1', $this->category_id);
	}

	function AddTagToContent($id, $content_id, $scope){
		$db =& bfCompat::getDBO();
		$db->setQuery('SELECT count(*) FROM #__tag_category_map WHERE tagid=\''.$id.'\' AND contentid=\''.$content_id.'\' ');
		$count = $db->LoadResult();
		if (!$count){
			/* add map */
			$this->tagid = $id;
			$this->contentid = $content_id;
			$this->scope = $scope;
			$this->store();
			$this->_checkForDBErrors();

			/* tell tags we have added new map for tag */
			$db->setQuery('UPDATE #__tag_tags SET countpublished = countpublished+1, count = count+1 WHERE id = \''.$id.'\' ');
			$db->query();

			return  $this->id;
		}
		return null;
	}

	function removeSingleMap($tagid, $contentid, $scope='com_content'){
		$db =& bfCompat::getDBO();
		$db->setQuery('DELETE FROM '.$this->_tbl.' WHERE tagid=\''.$tagid.'\' AND scope=\''.$scope.'\' AND contentid=\''.$contentid.'\'');
		$db->query();
		$this->_checkForDBErrors();
	}

	function removeTag($tagid){
		$db =& bfCompat::getDBO();
		$db->setQuery('DELETE FROM '.$this->_tbl.' WHERE tagid=\''.$tagid.'\'');
		$db->query();
		$this->_checkForDBErrors();
	}

	/* remove all maps based on listing_id */
	function removeAllMapsToListing($listing_id){

		/* get all */
		$this->getAll('contentid = ' .$listing_id);
		foreach ($this->rows as $map){

			/* remove the map */
			$mapping =& $this->_getModel('category_map');
			$mapping->delete($map->id);


			/* update category totals */
			$cat =& $this->_getModel('tag');
			$cat->updateListingTotal('-1', $map->tagid);

		}
	}
}
?>