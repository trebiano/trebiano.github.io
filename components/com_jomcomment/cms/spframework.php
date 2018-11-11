<?php

/**
 * (c) Azrul.com
 * Single Page MVC application framework
 * + database object access (MySql Only)
 * + build-in superfast template system
 * + library loadin
 */ 

class CMSController {
	
	function CMSController(){
	}
	
	# load the required library
	# The library should reside in "lib' folder and has 1 file with the same name as the library
	# name itself so that we can include it, eg: ->load('template') will include /lib/template/template.php				 	
	function load($libName){
		
	}
}


class CMSCore {

	function getInstance(){
        static $instance;
        if(!isset($instance)){
            //$object= __CLASS__;
            //$instance=new $object;
            $instance=new CMSCore();
        }
        return $instance;
    }
    
	function CMSCore(){
	}
	
	# Similar to PHP mail, but loaded with predefineds vars and preferably uses
	# the CMS default mailing system	
	function mail($to, $subject, $message, $header="null"){
		mail($to, $subject, $message);
	}
}




/**
 * CMSDB is a simple abstraction layer for CMSes database, it should provide
 * + simple way to perform typical query such as 
 * 	+ getting array of objects
 * 	+ get single value result
 * 	+ insert/update/delete object easily       
 */ 
class CMSDb {
	var $result; 		// Query result
	var $db;			// MySQL resource
	var $rec_pointer;	// record pointer
	var $prefix;		//
	var $prefix_mask;	//
	
	function getInstance(){
        static $instance;
        if(!isset($instance)){
            //$object= __CLASS__;
            //$instance=new $object;
            $instance=new CMSDb();
        }
        return $instance;
    }
    
	function CMSDb(){
		# This should be the only area that we will find Joomla Code
		global $database, $mosConfig_dbprefix;
		
		$this->db			= $database->_resource;
		$this->prefix		= $mosConfig_dbprefix;
		$this->prefix_mask	= "#__";
	}
	
	
	
	# Execute the given query
	function query($sql){
		$sql = str_replace($this->prefix_mask, $this->prefix, $sql);
		$result = mysql_query($sql, $this->db) or die($sql . mysql_error());
		$this->result = $result;
		return $result;
	}
	
	# Return number of rows of last query result
	function num_rows(){
		return mysql_num_rows($this->result);
	}
	
	# Return a single row of result Object from current #_result pointer
	# Assume $_result is not null	
	function row(){
		mysql_fetch_object($this->result);
	}
	
	# Result first row/first colum result
	function get_value($query =""){
	
		if(!empty($query)){
			$this->query($query);
		}
		
		if($this->result)
			return mysql_result($this->result, 0);
		else
			return 0;
	}
	
	# If a query is given
	function get_count($table, $cond){
		$sql = "SELECT COUNT(*) FROM `$table` WHERE ";
		$sql .= $this->implode_data($cond, "AND");
		
		return $this->get_value($sql);
	}
	
	# Return an array of objects
	function get_object_list(){
		$objectlist = array();
		while ($row = mysql_fetch_object($this->result)) {
		   $objectlist[] = $row;
		}
		
		return $objectlist;
	} 
	
	function get_where($table, $cond){
	}
	
	function set_where($table, $new, $cond){
	}
	
	
	# Insert the given array into the specified table
	# Data can either be an array of object
	function insert($table, $data){
		$sql = "INSERT INTO `$table` SET ";
		
		if(is_object($data)){
			$data = $this->object_to_array($data);
		}
		
		foreach( $data as $key => $val){
			if(is_numeric($data[$key]))
				$data[$key] = "`$key`=" . $this->_escape($val);
			else
				$data[$key] = "`$key`='" . $this->_escape($val) . "'";
		}

		$sql .= implode(",", $data);
		return $this->query($sql);
	}

	# Insert the given array into the specified table
	# Data can either be an array of object
	function update($table, $data, $cond){
		$sql = "UPDATE `$table` SET ";
		
		if(is_object($data)){
			$data = $this->object_to_array($data);
		}
		
		# Only build the sting if it is an array, otherwise assume it is a complete
		# query string		
		if(is_array($data)){
			$sql .= $this->implode_data($data);
		} else if(is_string($data)){
			$sql .= " $data "; 
		}
		
		$sql .= " WHERE ";
		
		if(is_array($cond)){
			$sql .= $this->implode_data($cond, "AND");
		} else if(is_string($cond)){
			$sql .= " $cond ";
		}
		
		return $this->query($sql);
	}
	
	
	# Return the last insert id
	function get_insert_id(){
		return mysql_insert_id($this->db);
	}
	
	function _escape($str){
		return mysql_escape_string($str);
	}
	
	/* Utilities */
	
	function object_to_array($obj) {
       $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
       $arr = array();
       foreach ($_arr as $key => $val) {
               $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
               $arr[$key] = $val;
       }
       return $arr;
	}
	
	# Implode the given data and return a string
	function implode_data($data, $tag=" , "){
		$sql = "";
		
		if(is_object($data)){
			$data = $this->object_to_array($data);
		}
		
		# where condition
		if(is_array($data)){		
			foreach( $data as $key => $val){
				if(is_numeric($data[$key]))
					$data[$key] = "`$key`=" . $this->_escape($val);
				else
					$data[$key] = "`$key`='" . $this->_escape($val) . "'";
			}
	
			$sql .= implode(" $tag ", $data);
		} else {
			$sql = $data;
		}
		return $sql;
	}
	
}

class CMSDbTable{
	function CMSDbTable(){
	}
	
}
