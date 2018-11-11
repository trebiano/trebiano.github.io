<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: category_map.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class Category_map extends bfModel {

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
	function Category_map() {
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

	/* remove a map by its id */
	function removeMap($id){
		$this->load($id);
		$this->delete($id);
		/* update category totals */
		$cat =& $this->_getModel('category');
		$cat->updateListingTotal('-1', $this->category_id);
	}

	function addMap($id, $category_id){
		$this->listing_id = $id;
		$this->category_id = $category_id;
		$this->store();

		/* update category totals */
		$cat =& $this->_getModel('category');
		$cat->updateListingTotal('1', $this->category_id);

		return  $this->id;
	}

	/* remove all maps based on listing_id */
	function removeAllMapsToListing($listing_id){

		/* get all */
		$this->getAll('listing_id = ' .$listing_id);
		foreach ($this->rows as $map){

			/* remove the map */
			$mapping =& $this->_getModel('category_map');
			$mapping->delete($map->id);

			/* update category totals */
			$cat =& $this->_getModel('category');
			$cat->updateListingTotal('-1', $map->category_id);

		}
	}
}
?>