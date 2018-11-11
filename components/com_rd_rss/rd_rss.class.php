<?php
/**
 * 
 * @version $Id: rd_rss.class.php,v 1.1.1.1 2005/12/20 15:49:40 deutz Exp $
 * @package RdRss
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * This is free software
 * 
 * changed to show all sections and categories as rss feed by Robert Deutz 
 *         joomla at run-digital dot com
 **/

// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

//
// Classdefinition  
//

class rdRssData extends mosDBTable {
	var $id 					= null; 	// int(11) unsigned NOT NULL auto_increment
	var $name					= "";		// varchar(255) not null
	var $catids					= "";		// text
	var $published				= "0";  	// tinyint(1)
	var $created				= "";  		// datetime NOT NULL default '0000-00-00 00:00:00' 
	var $created_by      		= ""; 		// int(11) unsigned
	var $modified				= "";  		// datetime NOT NULL default '0000-00-00 00:00:00' 
	var $modified_by      		= ""; 		// int(11) unsigned
	var $checked_out_time		= "";  		// datetime NOT NULL default '0000-00-00 00:00:00' 
	var $checked_out      		= ""; 		// int(11) unsigned
	var $state  	     		= "0"; 		// tinyint(3) NOT NULL default '0'
	var $params					= "";		// text
	
	function rdRssData( &$_db ) {
		$this->mosDBTable( '#__rd_rss', 'id', $_db );
	}
	
	function delete($oid=null) {
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		//$this->_db->setQuery( "UPDATE $this->_tbl set status = '1' WHERE $this->_tbl_key = '".$this->$k."'" );
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE $this->_tbl_key = '".$this->$k."'" );
		if ($this->_db->query()) {
			return true;
		} else {
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}			
	}
}

/** EOF **/?>