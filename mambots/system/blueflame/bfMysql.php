<?php
/**
* @version		$Id: bfMysql.php 827 2007-06-12 18:03:41Z phil $
* @package		Joomla.Framework
* @subpackage	Database
* @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/**
 * MySQL database driver
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.0
 */
class bfDatabaseMySQL extends bfDatabase
{
	/** @var string The database driver name */
	var $name			= 'mysql';
	/** @var string The null/zero date string */
	var $_nullDate		= '0000-00-00 00:00:00';
	/** @var string Quote for named objects */
	var $_nameQuote		= '`';

	/**
	* Database object constructor
	* @param string Database host
	* @param string Database user name
	* @param string Database user password
	* @param string Database name
	* @param string Common prefix for all tables
	*/
	function __construct( $host='localhost', $user, $pass, $db='', $table_prefix='')
	{
		// perform a number of fatality checks, then die gracefully
		if (!function_exists( 'mysql_connect' )) {
			$this->_errorNum = 1;
			$this->_errorMsg = 'The MySQL adapter "mysql" is not available.';
			return;
		}

		if (!($this->_resource = @mysql_connect( $host, $user, $pass, true ))) {
			$this->_errorNum = 2;
			$this->_errorMsg = 'Could not connect to MySQL';
			return;
		}

		if ($db != '' && !mysql_select_db( $db, $this->_resource )) {
			$this->_errorNum = 3;
			$this->_errorMsg = 'Could not connect to database';
			return;
		}

		// if running mysql 5, set sql-mode to mysql40 - thereby circumventing strict mode problems
		if ( strpos( $this->getVersion(), '5' ) === 0 ) {
			$this->setQuery( "SET sql_mode = 'MYSQL40'" );
			$this->query();
		}


		parent::__construct($host, $user, $pass, $db, $table_prefix);

	}

	/**
	 * Database object destructor
	 *
	 * @return boolean
	 * @since 1.5
	 */
	function __destruct()
	{
		$return = false;
		if (is_resource($this->_resource)) {
			$return = mysql_close($this->_resource);
		}
		return $return;
	}

	/**
	 * Determines UTF support
	 */
	function hasUTF() {
		$verParts = explode( '.', $this->getVersion() );
		return ($verParts[0] == 5 || ($verParts[0] == 4 && $verParts[1] == 1 && (int)$verParts[2] >= 2));
	}

	/**
	 * Custom settings for UTF support
	 */
	function setUTF() {
		if (@_BF_UTF8_MYSQL===true){
			//	mysql_query("SET CHARACTER SET utf8",$this->_resource);
			mysql_query( "SET NAMES 'utf8'", $this->_resource );
		}
	}

	/**
	* Get a database escaped string
	* @return string
	*/
	function getEscaped( $text ) {
		return mysql_real_escape_string( $text );
	}

	/**
	* Execute the query
	* @return mixed A database resource if successful, FALSE if not.
	*/
	function query()
	{


		if (!is_resource($this->_resource)) {
			return false;
		}

		if ($this->_limit > 0 || $this->_offset > 0) {
			$this->_sql .= "\nLIMIT $this->_offset, $this->_limit";
		}
		if ($this->_debug) {
			$this->_ticker++;
			$this->_log[] = $this->_sql;
		}
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		$this->_cursor = mysql_query( $this->_sql, $this->_resource );

		if (!$this->_cursor)
		{
			$this->_errorNum = mysql_errno( $this->_resource );
			$this->_errorMsg = mysql_error( $this->_resource )." SQL=$this->_sql";

			if ($this->_debug) {
				JError::raiseError('joomla.database:'.$this->_errorNum, 'JDatabaseMySQL::query: '.$this->_errorMsg );
			}
			return false;
		}

		if ($this->_fish_enabled == true){
			$this->setRefTables();
		}

		return $this->_cursor;
	}

	/**
	 * @return int The number of affected rows in the previous operation
	 * @since 1.0.5
	 */
	function getAffectedRows() {
		return mysql_affected_rows( $this->_resource );
	}

	/**
	* Execute a batch query
	* @return mixed A database resource if successful, FALSE if not.
	*/
	function queryBatch( $abort_on_error=true, $p_transaction_safe = false)
	{
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($p_transaction_safe) {
			$si = mysql_get_server_info( $this->_resource );
			preg_match_all( "/(\d+)\.(\d+)\.(\d+)/i", $si, $m );
			if ($m[1] >= 4) {
				$this->_sql = 'START TRANSACTION;' . $this->_sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 19) {
				$this->_sql = 'BEGIN WORK;' . $this->_sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 17) {
				$this->_sql = 'BEGIN;' . $this->_sql . '; COMMIT;';
			}
		}
		$query_split = preg_split ("/[;]+/", $this->_sql);
		$error = 0;
		foreach ($query_split as $command_line) {
			$command_line = trim( $command_line );
			if ($command_line != '') {
				$this->_cursor = mysql_query( $command_line, $this->_resource );
				if (!$this->_cursor) {
					$error = 1;
					$this->_errorNum .= mysql_errno( $this->_resource ) . ' ';
					$this->_errorMsg .= mysql_error( $this->_resource )." SQL=$command_line <br />";
					if ($abort_on_error) {
						return $this->_cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	* Diagnostic function
	*/
	function explain()
	{
		$temp = $this->_sql;
		$this->_sql = "EXPLAIN $this->_sql";
		$this->query();

		if (!($cur = $this->query())) {
			return null;
		}
		$first = true;

		$buf = "<table cellspacing=\"1\" cellpadding=\"2\" border=\"0\" bgcolor=\"#000000\" align=\"center\">";
		$buf .= $this->getQuery();
		while ($row = mysql_fetch_assoc( $cur )) {
			if ($first) {
				$buf .= "<tr>";
				foreach ($row as $k=>$v) {
					$buf .= "<th bgcolor=\"#ffffff\">$k</th>";
				}
				$buf .= "</tr>";
				$first = false;
			}
			$buf .= "<tr>";
			foreach ($row as $k=>$v) {
				$buf .= "<td bgcolor=\"#ffffff\">$v</td>";
			}
			$buf .= "</tr>";
		}
		$buf .= "</table><br />&nbsp;";
		mysql_free_result( $cur );

		$this->_sql = $temp;

		return "<div style=\"background-color:#FFFFCC\" align=\"left\">$buf</div>";
	}
	/**
	* @return int The number of rows returned from the most recent query.
	*/
	function getNumRows( $cur=null ) {
		return mysql_num_rows( $cur ? $cur : $this->_cursor );
	}

	/**
	* This method loads the first field of the first row returned by the query.
	*
	* @return The value returned in the query or null if the query failed.
	*/
	function loadResult($translate=true, $language=null)
	{
		//		if ($this->_fish_enabled===false){
		//			$result=null;
		//			$ret=null;
		//			$this->loadObject( $result, $translate, $language );
		//			if( $result != null ) {
		//				$fields = get_object_vars( $result );
		//				$field = each($fields);
		//				$ret = $field[1];
		//			}
		//			return $ret;
		//		} else {
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = mysql_fetch_row( $cur )) {
			$ret = $row[0];
		}
		mysql_free_result( $cur );
		return $ret;
		//		}
	}
	/**
	* Load an array of single field results into an array
	*/
	function loadResultArray($numinarray = 0)
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysql_fetch_row( $cur )) {
			$array[] = $row[$numinarray];
		}
		mysql_free_result( $cur );
		return $array;
	}

	/**
	* Fetch a result row as an associative array
	*
	* return array
	*/
	function loadAssoc()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($array = mysql_fetch_array( $cur, MYSQL_ASSOC )) {
			$ret = $array;
		}
		mysql_free_result( $cur );
		return $ret;
	}

	/**
	* Load a assoc list of database rows
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	*/
	function loadAssocList( $key='' )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysql_fetch_assoc( $cur )) {
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysql_free_result( $cur );
		return $array;
	}
	/**
	* This global function loads the first row of a query into an object
	*
	* return object
	*/
	function _loadObject( )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($object = mysql_fetch_object( $cur )) {
			$ret = $object;
		}
		mysql_free_result( $cur );
		return $ret;
	}

	function loadObject( $object=null, $translate=true, $language=null ) {

		if ($this->_fish_enabled===true){
			global $mosConfig_lang, $_JOOMFISH_MANAGER;
			if( $language===null) $language = $mosConfig_lang;

			$result = $this->_loadObject(  );
			if( $translate && isset($_JOOMFISH_MANAGER)) {
				$doTranslate=false;
				$tables =$this->_getRefTables();
				foreach ($tables["fieldTablePairs"] as $i=>$table) {
					if (in_array( $table, $this->_mlTableList )) {
						$doTranslate=true;
						break;
					}
				}
				if ( $doTranslate) {
					JoomFish::translate( $object, $this->_getTableName(), $language, $this->_getRefTables());
				}
			}
			return $result;
		} else {
			return $this->_loadObject(  );
		}
	}


	/**
	* Load a list of database objects
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*/
	function _loadObjectList(  $key='' ){
		/* bfCache */
		/* Object caching */
		if (!bfCompat::isAdmin()){
			if ($this->_isincache()){
				return $this->_isincache();
			}
		}

		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysql_fetch_object( $cur )) {
			if ($key) {
				$array[$row->$key] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysql_free_result( $cur );

		/* bfCache */
		if (!bfCompat::isAdmin()){
			$this->_savetocache($array);
		}
		return $array;
	}

	/* fish compatibility */
	function loadObjectList(  $key='', $translate=true, $language=null )
	{

		if ($this->_fish_enabled===true){
			global $mosConfig_lang, $_JOOMFISH_MANAGER;
			if( $language===null) $language = $mosConfig_lang;

			$result = $this->_loadObjectList( $key );
			if( $translate && isset($_JOOMFISH_MANAGER)) {
				$doTranslate=false;
				$tables = $this->_getRefTables();

				foreach ($tables["fieldTablePairs"] as $i=>$table) {
					if (in_array( $table, $this->_mlTableList )) {
						$doTranslate=true;
						break;
					}
				}
				if ($doTranslate ) {
					JoomFish::translateList( $result, $this->_getTableName(), $language, $this->_getRefTables() );
				}
			}
			return $result;

		} else {
			return $this->_loadObjectList( $key );
		}


	}
	/**
	* @return The first row of the query.
	*/
	function loadRow()
	{

		/* Object caching */
		if ($this->_isincache()){
			return $this->_isincache();
		}


		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = mysql_fetch_row( $cur )) {
			$ret = $row;
		}
		mysql_free_result( $cur );

		$this->_savetocache($ret);
		return $ret;
	}
	/**
	* Load a list of database rows (numeric column indexing)
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*/
	function loadRowList( $key=null )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysql_fetch_row( $cur )) {
			if ($key !== null) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysql_free_result( $cur );
		return $array;
	}
	/**
	* Document::db_insertObject()
	*
	* { Description }
	*
	* @param [type] $keyName
	* @param [type] $verbose
	*/
	function _insertObject( $table, &$object, $keyName = NULL, $verbose=false )
	{
		$fmtsql = "INSERT INTO $table ( %s ) VALUES ( %s ) ";
		$fields = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->nameQuote( $k );;
			$values[] = $this->isQuoted( $v ) ? $this->Quote( $v ) : $v;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
		($verbose) && print "$sql<br />\n";
		if (!$this->query()) {
			return false;
		}
		$id = $this->insertid();
		($verbose) && print "id=[$id]<br />\n";
		if ($keyName && $id) {
			$object->$keyName = $id;
		}
		return $object;
	}

	function insertObject( $table, &$object, $keyName = NULL, $verbose=false , $passthru=false) {

		if ($this->_fish_enabled===true){
			global $mosConfig_lang, $mosConfig_defaultLang, $_JOOMFISH_MANAGER;
			$passthru = $mosConfig_lang == $mosConfig_defaultLang;

			if( !$passthru && isset($_JOOMFISH_MANAGER)) {
				//Must insert parent first to get reference id !
				$parentInsertReturn = $this->_insertObject( $table, $object, $keyName, $verbose);

				/* Phil: slight change as we are using models and not mosDBTable */
				$object = $object->_db;
				$actContentObject=null;
				if( isset($table) && $table!="" ) {
					$tableName = ereg_replace( '^#__', '', $table);
					if (in_array( $tableName, $this->_mlTableList )) {
						$contentElement = $_JOOMFISH_MANAGER->getContentElement( $tableName );
						if( isset( $contentElement ) ) {
							$actContentObject = new ContentObject( $_JOOMFISH_MANAGER->getLanguageID($mosConfig_lang), $contentElement );
							if( isset( $object->$keyName ) ) {
								$actContentObject->loadFromContentID( $object->$keyName );
								//								print_R($object);
								$actContentObject->updateMLContent( $object );
								if( isset( $object->state ) ) {
									$actContentObject->published = ($object->state == 1) ? true : false;
								} else if ( isset( $object->published ) ) {
									$actContentObject->published = ($object->published == 1) ? true : false;
								}
								if ($actContentObject->published){
									if ( $_JOOMFISH_MANAGER->getCfg("frontEndPublish")){
										global $acl, $my;
										$access = new stdClass();
										$access->canPublish = $acl->acl_check( 'action', 'publish', 'users', $my->usertype, 'content', 'all' );
										if ($access->canPublish) $actContentObject->setPublished($actContentObject->published);
									}
								}
								$actContentObject->store();
							}
						}
					}
				}
				return $parentInsertReturn;
			}
			else {
				return $this->_insertObject( $table, $object, $keyName, $verbose);
			}
		} else {
			return $this->_insertObject( $table, $object, $keyName, $verbose);
		}
	}

	/**
	 * Document::db_updateObject()
	 * @param [type] $updateNulls
	 */
	function _updateObject( $table, &$object, $keyName, $updateNulls=true )
	{
		$fmtsql = "UPDATE $table SET %s WHERE %s";
		$tmp = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
				continue;
			}
			if( $k == $keyName ) { // PK not to be updated
				$where = $keyName . '=' . $this->Quote( $v );
				continue;
			}
			if ($v === NULL && !$updateNulls) {
				continue;
			}
			if( $v == '0' ) {
				$val = $this->isQuoted( $v ) ? $this->Quote( '0' ) : 0;
			} else if( $v == '' ) {
				$val = $this->isQuoted( $v ) ? $this->Quote( '' ) : 0;
			} else {
				$val = $this->isQuoted( $v ) ? $this->Quote( $v ) : $v;
			}
			$tmp[] = $this->nameQuote( $k ) . '=' . $val;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
		return $this->query();
	}

	/**
	* Overwritten update function to enable storage of translated information.
	* Based on the configuration in the content elements the automatic storage of
	* information is activated or not. It is important that this upgraded method will ensure
	* that all information will be written into the translation table. Only in the case that the
	* default language is choosen the information will be updated directly within the original tables.
	* To make sure that all other information will be written into the tables as expected the
	* statements will be manipulated as needed.
	* 
	* @param	string	table name
	* @param	object	instance with information to store
	* @param	string	primary key name of table
	* @param	boolean	update fields with null or not
	* @param	boolean	passthru without storing information in a translation table
	*/
	function updateObject( $table, &$object, $keyName, $updateNulls=true, $passthru=false ) {

		if ($this->_fish_enabled===true){
			global $mosConfig_lang, $mosConfig_defaultLang, $_JOOMFISH_MANAGER;

			// if the currect language is the site default language the translations will not be updated!
			$passthru = $mosConfig_lang == $mosConfig_defaultLang;

			if( !$passthru && isset($_JOOMFISH_MANAGER)) {
				$actContentObject=null;
				if( isset($table) && $table!="" ) {
					$tableName = ereg_replace( '^#__', '', $table);
					if (in_array( $tableName, $this->_mlTableList )) {
						$contentElement = $_JOOMFISH_MANAGER->getContentElement( $tableName );
						if( isset( $contentElement ) ) {
							$actContentObject = new ContentObject( $_JOOMFISH_MANAGER->getLanguageID($mosConfig_lang), $contentElement );
							if( isset( $object->$keyName ) ) {
								$actContentObject->loadFromContentID( $object->$keyName );
								$actContentObject->updateMLContent( $object );
								if( isset( $object->state ) ) {
									$actContentObject->published = ($object->state == 1) ? true : false;
								} else if ( isset( $object->published ) ) {
									$actContentObject->published = ($object->published == 1) ? true : false;
								}
								if ( $_JOOMFISH_MANAGER->getCfg("frontEndPublish")){
									global $acl, $my;
									$access = new stdClass();
									$access->canPublish = $acl->acl_check( 'action', 'publish', 'users', $my->usertype, 'content', 'all' );
									if ($access->canPublish) $actContentObject->setPublished($actContentObject->published);
								}

								$actContentObject->store();
							}
						}
					}
				}
				return $this->_updateObject( $table, $object, $keyName, $updateNulls );

			} else {
				return $this->_updateObject( $table, $object, $keyName, $updateNulls );
			}
		} else {
			/* no fish */
			return $this->_updateObject( $table, $object, $keyName, $updateNulls );
		}
	}

	function insertid() {
		return mysql_insert_id( $this->_resource );
	}

	function getVersion() {
		return mysql_get_server_info( $this->_resource );
	}
	/**
	 * Assumes database collation in use by sampling one text field in one table
	 * @return string Collation in use
	 */
	function getCollation ()
	{
		if ( $this->hasUTF() ) {
			$this->setQuery( 'SHOW FULL COLUMNS FROM #__content' );
			$array = $this->loadAssocList();
			return $array['4']['Collation'];
		} else {
			return "N/A (mySQL < 4.1.2)";
		}
	}

	/**
	 * @return array A list of all the tables in the database
	 */
	function getTableList()
	{
		$this->setQuery( 'SHOW TABLES' );
		return $this->loadResultArray();
	}
	/**
	 * @param array A list of table names
	 * @return array A list the create SQL for the tables
	 */
	function getTableCreate( $tables )
	{
		$result = array();

		foreach ($tables as $tblval) {
			$this->setQuery( 'SHOW CREATE table ' . $this->getEscaped( $tblval ) );
			$rows = $this->loadRowList();
			foreach ($rows as $row) {
				$result[$tblval] = $row[1];
			}
		}

		return $result;
	}

	/**
	 * @param array A list of table names
	 * @return array An array of fields by table
	 */
	function getTableFields( $tables )
	{
		$result = array();

		foreach ($tables as $tblval) {

			$log =& bfLog::getInstance();
			$log->log('SHOW FIELDS FROM ' . $tblval);
			$this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
			$fields = $this->loadObjectList();

			foreach ($fields as $field) {
				$result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
			}
		}

		return $result;
	}

	/**
	 * fish compatibility
	 *
	 */
	function setRefTables(){

		// This could be speeded up by the use of a cache
		$tempsql = $this->_sql;
		// only needed for selects at present - possibly add for inserts/updates later
		if (strpos(strtoupper($tempsql),"SELECT")===false) {
			return;
		}
		if($this->_cursor===true || $this->_cursor===false) return;
		// get column metadata
		$i = 0;
		//		echo " $this->_sql $this->_cursor ".var_export( $this->_cursor, true )."<br>";
		$cursorType = get_resource_type($this->_cursor);
		if ($cursorType == "mysql result") {
			$dbIsMySQL = true;
		}
		else $dbIsMySQL = false;


		if (!$dbIsMySQL && function_exists("mysqli_num_fields")){
			$fields = mysqli_num_fields($this->_cursor);
		}	else {
			$fields = mysql_num_fields($this->_cursor);
		}
		//		print "<br> $tempsql $this->_cursor $fields";
		if ($fields<=0) return;

		$this->_refTables=array();
		$this->_refTables["fieldTablePairs"]=array();
		$this->_refTables["tableAliases"]=array();
		$this->_refTables["fieldAliases"]=array();
		$this->_refTables["fieldTableAliasData"]=array();
		$this->_refTables["fieldCount"]=$fields;
		$this->_refTables["sql"]=$tempsql;
		// local variable to keep track of aliases that have already been analysed
		$tableAliases = array();

		for ($i = 0; $i < $fields; ++$i) {
			if (!$dbIsMySQL && function_exists("mysqli_fetch_field"))  $meta = mysqli_fetch_field($this->_cursor);
			else  $meta = mysql_fetch_field($this->_cursor, $i);
			if (!$meta) {
				echo "No information available<br />\n";
			}
			else {
				$tempTable =  $meta->table;
				// if I have already found the table alias no need to do it again!
				if (array_key_exists($tempTable,$tableAliases)){
					$value = $tableAliases[$tempTable];
				}
				else {
					if (!isset($tempTable) || strlen($tempTable)==0) {
						continue;
					}
					//echo "<br>Information for column $i of ".($fields-1)." ".$meta->name." : $tempTable=";
					$tempArray=array();
					$prefix = $this->_table_prefix;
					preg_match_all("/$prefix(\w*)\s+AS\s+".$tempTable."[,\s]/i",$this->_sql, $tempArray, PREG_PATTERN_ORDER);
					if (count($tempArray)>1 && count($tempArray[1])>0) $value = $tempArray[1][0];
					else $value = null;
					if (isset($this->_table_prefix) && strlen($this->_table_prefix)>0 && strpos($tempTable,$this->_table_prefix)===0) $tempTable = substr($tempTable, strlen( $this->_table_prefix));
					$value = $value?$value:$tempTable;
					$tableAliases[$tempTable]=$value;
				}

				if ((!($value=="session" || strpos($value,"jf_")===0)) && $this->translatedContentAvailable($value)){
					/// ARGH !!! I must also look for aliases for fieldname !!
					$tempName = $meta->name;
					$tempArray=array();
					preg_match_all("/(\w*)\s+AS\s+".$tempName."[,\s]/i",$this->_sql, $tempArray, PREG_PATTERN_ORDER);
					if (count($tempArray)>1 && count($tempArray[1])>0) {
						//echo "$meta->name is an alias for ".$tempArray[1][0]."<br>";
						$nameValue = $tempArray[1][0];
					}
					else $nameValue = $meta->name;

					if (!array_key_exists($value,$this->_refTables["tableAliases"])) $this->_refTables["tableAliases"][$value]=$meta->table;
					// I can't use the field name as the key since it may not be unique!
					if (!in_array($value,$this->_refTables["fieldTablePairs"])) $this->_refTables["fieldTablePairs"][]=$value;
					if (!array_key_exists($nameValue,$this->_refTables["fieldAliases"])) $this->_refTables["fieldAliases"][$meta->name]=$nameValue;

					// Put all the mapping data together so that everything is in sync and I can check fields vs aliases vs tables in one place
					$this->_refTables["fieldTableAliasData"][$i]=array("fieldNameAlias"=>$meta->name, "fieldName"=>$nameValue,"tableNameAlias"=>$meta->table,"tableName"=>$value);
				}

			}
		}
	}

	/** Internal function to return reference table names from an sql query
	 *
	 * fish compatibility
	 * 
	 * @return	string	table name
	 */
	function _getRefTables(){
		return $this->_refTables;
	}

	/**
	 * Internal function to determit the table name from an sql query
	 *
	 * fish compatibility
	 * 
	 * @return	string	table name
	 */
	function _getTableName() {
		global $mosConfig_dbprefix;

		$posFrom = strpos( strtoupper($this->_sql), 'FROM ') + 5; // after 'FROM '
		$posWhere = strpos( strtoupper($this->_sql), 'WHERE ');
		$table = substr( $this->_sql, $posFrom, $posWhere - $posFrom);
		if( strpos( $table, ' ' ) !== false ) {
			$table = substr( $table, 0, strpos( $table, ' ' ) );
		}
		if (isset($mosConfig_dbprefix) && strlen($mosConfig_dbprefix)>0) $table = ereg_replace( $mosConfig_dbprefix, '', $table);
		$table = ereg_replace( "\n", "", $table) ;

		return $table;
	}

	/**
	 * Public function to test if table has translated content available
	 *
	 * fish compatibility
	 * 
	 * @param string $table : tablename to test
	 */
	function translatedContentAvailable($table){
		return in_array( $table, $this->_mlTableList);
	}
}
?>
