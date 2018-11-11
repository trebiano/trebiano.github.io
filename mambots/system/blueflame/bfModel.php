<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfModel.php 1017 2007-07-09 17:24:27Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

bfLoad('bfTable');

// Set _BF_UNLIMITED to false so hasMany calls to getAllWhere in the ORM
// return all rows
define('_BF_UNLIMITED',false);

/**
 * A JModel adapted for our use.
 * Our model classes subclass this.
 * I'm going to assume that every table has "id" as its primary key,
 * this is easily changed if more flexibility is required.
 *
 * NOTE: Make sure you choose STATE or PUBLISHED and make sure you go through
 * the code and change code depending on your choice of field name.
 *
 * See the develloper wiki for definitions of the ORM
 *
 */
class bfModel extends bfTable {

	/** @var Object The database DBO **/
	var $_db = null;

	/** @var The table name including the #__ **/
	var $_table_name = null;

	/** @var The subclass name excluding the #__ **/
	var $_child_name = "data";

	/** @var The model name singular lower case **/
	var $_model_name = null;

	/**
	 * Fields that should be searched when the user
	 *	filters the search results
	 *  @var array
	 **/
	var $_search_fields=array();

	var $_hasRating = false;
	/**
	 * The models for which this model hasMany
	 *
	 * @var array
	 */
	var $_hasMany=array();
	/**
	 * The models for which this model hasOne
	 *
	 * @var array
	 */
	var $_hasOne=array();
	/**
	 * The models which this model belongs to (ie a list of its parents)
	 *
	 * @var array
	 */
	var $_belongsTo=array();
	/**
	 * The models with which this model has a
	 * "has and belongs to many, through" relationship
	 *
	 * @var array
	 */
	var $_habtm=array(); // Has and belong to many through

	/**
	 * A local reference to the log
	 *
	 * @var array
	 */
	var $_log;
	/**
	 * A local reference to the bfSession
	 *
	 * @var array
	 */
	var $_session;

	/**
	 * The search filter order
	 *
	 * @var string
	 */
	var $_orderby = array();

	/**
	 * Incoming xajax args
	 *
	 * @var unknown_type
	 */
	var $_args;

	/**
	 * I store the registry object
	 *
	 * @var object The bfRegistry Object
	 */
	var $_tablefields = array();

	/**
	 * I store the registry object
	 *
	 * @var object The bfRegistry Object
	 */
	var $_registry = null;

	/**
	 * I hold a bool if this model supports ordering
	 *
	 * @var bool
	 */
	var $_noOrdering = false;

	/**
	 * List of compulsory fields
	 */
	var $_validates_presence_of = array();

	/**
	 * List of fields that must be unique
	 */
	var $_validates_uniqueness_of = array();

	/**
	 * Call to the constructor for PHP4
	 *
	 * @param string $table_name
	 * @return bfModel
	 */
	function bfModel($table_name){
		$this->__construct($table_name);
	}

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a
	 * constructor because func_get_args ( void ) returns a copy of all
	 * passed arguments NOT references. This causes problems with
	 * cross-referencing.
	 *
	 * @param string $table_name
	 */
	function __construct($table_name) {
		global $mainframe;

		$this->_session =& bfSession::getInstance();
		$this->_log =& bfLog::getInstance();


		/* Get our registry */
		$component = $mainframe->get('component');
		$this->_registry =& bfRegistry::getInstance($component, $component);

		$this->_table_name=$table_name;
		$this->_child_name = eregi_replace("^[^_]*_","",$table_name);
		$this->_model_name = eregi_replace("s$","",$this->_child_name);
		$this->_model_name = eregi_replace("^bf","",$this->_model_name);
		$this->_model_name = eregi_replace("^_bf","",$this->_model_name);



		if (_BF_TEST_MODE == 'true') {
			$this->_table_name=$table_name.'_test';
		}

		$this->_db =& bfCompat::getDBO();
		parent::__construct($this->_table_name,'id', $this->_db);

		/* set vars that setDefaults will modify */
		$this->published = null;
		$this->access = null;

		/* enable support for checkout/in */
		$this->checked_out = null;

		/* Set the database fields for this table */
		$this->log("About to get db columns for ".$this->_table_name);
		$tables=array($this->_table_name);

		$this->_tablefields=$this->_db->getTableFields( $tables );

		if ($this->_db->_errorNum > 0){
			return false;
		}

		foreach( $this->_tablefields as $fields ) {
			foreach( $fields as $fieldname => $type ) {
				$this->$fieldname = null;
			}
		}

		$this->_checkForDBErrors();
	}

	/**
	 * saves the incoming post with details to the db
	 *
	 * @return bool the save result
	 */
	function saveDetails($args=null){
		$new = false;

		/* Set the defaults for our row */
		$this->setDefaults();

		if (!count($this->_args)){
			$this->setArgs($args);
		}

		/* Bind the args to the class properties */
		if (!count($this->_args)){
			$this->setArgs(bfRequest::get('REQUEST'));
		}



		$this->bind($this->_args);

		/* Support for credit card numbers */
		if (@$this->card_number){
			$this->card_number =  'xxxxxxxxxxxx'.substr($this->card_number,strlen($this->card_number)-4, strlen($this->card_number));
		}

		if ($this->id ==''){
			$new = true;
		}

		/* created */
		if (!@$this->_nometa){
			if ($this->created_by == ''){
				$user =& bfUser::getInstance();
				$this->created_by = $user->get('id');
				if ($this->created && strlen(trim( $this->created )) <= 10) {
					$this->created 	.= ' 00:00:00';
				}
				global $mosConfig_offset;
				$this->created 		= $this->created ? mosFormatDate( $this->created, '%Y-%m-%d %H:%M:%S', -$mosConfig_offset ) : date( 'Y-m-d H:i:s' );

			}
		}

		/* nometa =1 :  This forces a model not to have a published, checkedout etc columns */
		//		if (isset($this->_nometa)) unset($this->_nometa);

		/* Do the save! */
		if (!$this->store()){
			bfError::raiseError('500','Could not save '.$this->_child_name . '<br />Reason given: '.$this->_db->getErrorMsg());
			die('Could not save '.$this->_child_name . '<br />Reason given: '.$this->_db->getErrorMsg());
		} else {

			/* check if parentid is same as id */
			/* Not allowed! */
			if (isset($this->parentid)){
				if ($this->parentid == $this->id){
					$this->_registry->set('errormsg','Item cant be parent of itself');
					$this->parentid = 0;
					$this->category_path = 'Root';
					$this->store();
				}
			}



			$this->checkIn();
			return true;
		}

	}

	function resetHits($id){
		$this->load($id);
		$this->hits = '0';
		$this->store();
	}

	function resetAllHits(){
		$db =& bfCompat::getDBO();
		$db->setQuery('UPDATE ' . $this->_tbl . ' SET hits = 0');
		$db->query();
	}

	function getRating(){

		$query = "SELECT ROUND(v.rating_sum/v.rating_count) AS user_rating, v.rating_count"
		. "\n FROM ".$this->_RatingTable." AS v"
		. "\n WHERE v.id = ".$this->id
		;

		$this->_db->setQuery( $query );
		$rateings = $this->_db->loadObjectList();

		$path = bfCompat::getLiveSite() .'/' . bfCompat::mambotsfoldername() . '/system/blueflame/view/images';
		$starImageOn = '<img src="' .$path . '/star_on.png'.'" />';
		$starImageOff = '<img src="' .$path . '/star_off.png'.'" />';
		if ($rateings){
			$rateings = @$rateings[0];
			$img = "";
			for ($i=0; $i < $rateings->user_rating; $i++) {
				$img .= $starImageOn;
			}
			for ($i=$rateings->user_rating; $i < 5; $i++) {
				$img .= $starImageOff;
			}
			$html = '<span class="content_rating">';
			$html .=  $img;
			$html .= "</span>";
			return array($html,$rateings->rating_count . ' '.bfText::_('Votes'));
		} else {
			$img = $starImageOff . $starImageOff . $starImageOff . $starImageOff .$starImageOff;
			$html = '<span class="content_rating">';
			$html .=  $img;
			$html .= "</span>";
			return array($html,' 0 '.bfText::_('Votes'));
		}
	}

	/**
	 * Sets the default options before saving new item
	 */
	function setDefaults(){
		return;

		if (!isset($this->_nometa)){
			/* set default state to unpublished */
			if ($this->published==null){
				$this->published = $this->_registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.bfModel.defaults.published');
			}

			/* set default access to public */
			if ($this->access==null){
				$this->access = $this->_registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.bfModel.defaults.access');;
			}

			/* set default checked out */
			if ($this->checked_out==null){
				$this->checked_out = $this->_registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.bfModel.defaults.checked_out');;
			}
		}

	}

	/**
	 * Sets the incoming xAJAX args so we can use them to bind to
	 *
	 * @param unknown_type $args
	 */
	function setArgs($args){
		$this->_args = $args;
	}

	/**
	 * Add a sort order pair to the local _orderby array
	 * Check that the column exists in the db before sorting on it.
	 *
	 * @param string $order
	 * @param string $dir
	 */
	function setOrderBy($order, $dir='ASC'){
		if (!isset($this->_tablefields[$this->_table_name][$order])) {
			bfError::raiseError('500','Access Denied to invalid model column '.get_class($this).' '.$order);
		}
		// If we are already sorting on this column then just
		// update the direction
		foreach($this->_orderby as $Eorder => $Edesc) {
			if ($order == $Eorder) {
				$this->_orderby[$order]=$dir;
				return;
			}
		}
		// Otherwise add a new sort column/direction pair
		$this->_orderby[$order] = $dir;
	}

	/**
	 * Enter description here...
	 * @Deprecated. Orderby moving to an array
	 *
	 * @param unknown_type $dir
	 */
	// function setOrderdir($dir='DESC'){
	// 	$this->_orderdir = $dir;
	// }

	/**
	 * I simply run SQL with minimal response and error checking
	 * Useful for things like CREATE TEMPORARY TABLE
	 * Use getBySQL wherever possible, unless the model does the query
	 * for you, because nothing except error statused comes back from
	 * runSQL.
	 */

	function runSQL( $sql ) {
		$this->log("runSQL: $sql \n");
		$this->_db->setQuery($sql);
		$this->_db->query($sql);
		$this->_checkForDBErrors();
	}

	/**
	 * Find all the rows from the give SQL statement.
	 * @return rowset array
	 */
	function getBySQL( $sql ) {
		/** set query to collect rows **/

		$this->_db->setQuery($sql);
		/** Load rows into array */
		/* return in $this->rows */
		$this->rows = $this->_db->LoadObjectList();
		$this->_checkForDBErrors();
		$this->rows=$this->rows;
	}


	/**
	 * Find all the rows from the table subject only to the
	 * where clause.
	 *
	 * @param SQL $where
	 * @param boolean $apply_limit
	 * @return rowset array
	 */
	function getAllWhere( $where = '', $apply_limit = true , $orderby=null) {
		return $this->getAll( $where , $apply_limit, $orderby );
	}


	/**
	 * Return the id of the table's first element
	 */

	function last( $where='') {
		$sql = "SELECT MAX(id) as max FROM ". $this->_table_name." $where";
		$this->getBySQL($sql);
		$this->_checkForDBErrors();
		return($this->rows[0]->max);
	}
	/**
	 * Return the id of the table's first element
	 */

	function first( $where='') {
		$sql = "SELECT MIN(id) as min FROM ". $this->_table_name." $where";
		$this->getBySQL($sql);
		$this->_checkForDBErrors();
		return($this->rows[0]->min);
	}

	/**
	 * Return the number of rows in the table
	 */

	function count( $where='') {
		$sql = "SELECT COUNT(*) as count FROM ". $this->_table_name." $where";
		$this->getBySQL($sql);
		$this->_checkForDBErrors();
		return($this->rows[0]->count);
	}

	/**
	 * Find all the rows from the table subject to the
	 * SQL LIMIT $start, $end
	 * @return object
	 *
	 * @param SQL $where
	 * @param boolean $apply_limit
	 * @return rowset array
	 */
	function getAll( $where = '', $apply_limit=true, $orderby=null) {
		global $mainframe;

		$tableJOIN = '';
		$whereJOIN = '';
		$filter 	= $this->_session->get('filter');
		$cat	 	= $this->_session->get('cat');
		$page 		= $this->_session->get('page');
		$limit 		= $this->_session->get('limit');

		if ($cat > 0 && ($mainframe->get('component')=='com_kb') && $this->_table_name = '#__kb_articles'){
			$tableJOIN = ' LEFT JOIN #__kb_category_map as m ON m.article_id = a.id ';
			$whereJOIN = ' m.category_id = ' . $cat;
		}

		$start = ($page - 1) * $limit;
		$end = $start + $limit;

		$search_string = "";

		if ($filter != "") {
			$search_string = $this->_getSearchString($filter);
		}

		// Get the number of rows in the table / matching the
		// whole unlimited query including anysearch criteria.
		$sql = "SELECT COUNT(*) as count FROM ". $this->_table_name .' as a '. $tableJOIN ;
		if ($search_string != "") $sql.=" $search_string";

		/* make sure we append the WHERE correctly based on if we have already filtered with WHERE */
		if ($where != '' && $search_string != "") $sql.=" AND $where" . $whereJOIN;
		if ($where != '' && $search_string == "") $sql.=" WhERE $where" . $whereJOIN;
		if ($where == '' && $search_string == "" && $whereJOIN != '') $sql.=" WhERE " . $whereJOIN;

		$this->_db->setQuery($sql);
		$row = $this->_db->loadResult();
		$this->_checkForDBErrors();
		$this->_session->set("rowcount",$row);

		/** set query to collect rows **/
		$sql = "SELECT a.* FROM ". $this->_table_name .' as a ' . $tableJOIN;

		if ($search_string != "") $sql.=" $search_string";

		/* make sure we append the WHERE correctly based on if we have already filtered with WHERE */
		if ($where != '' && $search_string != "") $sql.=" AND $where" . $whereJOIN;
		if ($where != '' && $search_string == "") $sql.=" WhERE $where" . $whereJOIN;
		if ($where == '' && $search_string == ""&& $whereJOIN != '') $sql.=" WhERE ". $whereJOIN;

		if ($orderby === null){
			$sql .= $this->getOrderBySQL();
		} else {
			$sql .= ' ORDER BY '.$orderby;
		}

		if ($start < 0) $start = 0;
		if ($apply_limit) $sql .= " LIMIT $start, $limit";

		//		echo ("getAll sql: $sql \n");

		$this->_db->setQuery($sql);

		/** Load rows into array */
		$this->rows = $this->_db->LoadObjectList();

		$log =& bfLog::getInstance();
		$log->log($this->rows);

		if (!isset($this->rows)) $this->rows=array();

		$this->_checkForDBErrors();

		/* return rows */
		$this->_registry->setValue('sql', $this->_db->_log);
		return $this->rows;
	}

	/**
	 * returns the current dbo object in $this->_jtable
	 * appends ORM variables as needed.
	 *
	 * @return unknown
	 */
	function get( $id ){

		$this->log("get $id");
		$ret = $this->load( $id );

		if (!$ret) $this->log("No db row for id $id");

		if ($this->_hasRating===true){
			$arr = $this->getRating();
			$this->rating = $arr[0];
			$this->rating_count = $arr[1];
		}

		return($ret);

		/* The ORM */

		//		/* Has many */
		//		$apply_limit=false;
		//		foreach($this->_hasMany as $modelname => $value) {
		//			$this->log("Handling hasMany ( $modelname , $value ) for ".__CLASS__);
		//			/* set some where clauses for our actions so the ORM only gets useable ones
		//			$registry =& bfRegistry::getInstance(, );
		//			echo $registry->getValue('bfORM.'.$modelname.'.where');
		//			echo 'bfORM.'.$modelname.'.where'."\n";
		//			*/
		//
		//			$modelfile=preg_replace("/s$/","",$modelname);
		//			$modelclass=ucfirst($modelfile);
		//			$modelsingular=$modelfile;
		//			$modelplural=$modelfile."s";
		//
		//			/* get the models definition file */
		//			require_once(_BF_FRONT_MODEL_DIR . DS . $modelfile . '.php');
		//
		//			/* Generate a model object */
		//			$model=& new $modelclass();
		//
		//			/* create empty array where our where parts will go */
		//			$where = array();
		//
		//			/* ORM in the id field */
		//			$where[] = $this->_model_name."_id = " . $id;
		//
		//			/* allow appending of where from registry */
		//			if ($this->_registry->getValue('bfORM.'.$modelsingular.'.where')){
		//				$where[] = $this->_registry->getValue('bfORM.'.$modelsingular.'.where');
		//			}
		//
		//			/* create where sql */
		//			$whereSQL = implode (' AND ', $where);
		//
		//			$model->getAllWhere($whereSQL, _BF_UNLIMITED);
		//
		//			$this->$modelplural=&$model->rows;
		//			$this->$modelname=&$model->rows;
		//		} // foreach has many
		//
		//		/* Has one*/
		//		foreach($this->_hasOne as $modelname => $value) {
		//			$this->log("Handling hasOne for ".__CLASS__);
		//			$modelfile=preg_replace("/s$/","",$modelname);
		//			$modelsingular=$modelfile;
		//			$modelclass=ucfirst($modelfile);
		//			require_once(_BF_FRONT_MODEL_DIR.DS.$modelfile.".php");
		//			// Generate a model object
		//			$model=&new $modelclass();
		//			$model->getAllWhere($this->_model_name."_id = " . $id);
		//			$this->$modelsingular=$model->rows;
		//			$this->$modelname=$model->rows;
		//		} // foreach has one
		//
		//		/* belongs to */
		//		foreach($this->_belongsTo as $modelname => $value) {
		//			$this->log("Handling belongsTo for ".__CLASS__);
		//			$modelfile=preg_replace("/s$/","",$modelname);
		//			$modelsingular=$modelfile;
		//			$modelclass=ucfirst($modelfile);
		//			require_once(_BF_FRONT_MODEL_DIR.DS.$modelfile.".php");
		//			// Generate a model object
		//			$model=&new $modelclass();
		//			$id = eval("return \$this->$modelsingular"."_id;");
		//			$model->get($id);
		//			$this->$modelsingular=$model;
		//			$this->$modelsingular=$model;
		//		} // foreach belongs to
		//return($ret);
		//		foreach($this->_habtm as $modelname => $throughtable) {
		//			$this->log("Handling habtm for ".__CLASS__);
		//			# Get the through table model
		//			# Get the through table rows giving a list of my
		//			# associated items
		//			# For each item get an instance of the target model
		//			# and add it to $this->$modelname
		//			#
		//			# OR generate the SQL to just do it!
		//			#
		//			$targetlower=strtolower($modelname);
		//			$targetlower=preg_replace("/^bf/","",$targetlower);
		//			$targetsingular=preg_replace("/s$/","",$targetlower);
		//			$source=get_class($this);
		//			$sourcelower=strtolower($source);
		//			$sourcelower=preg_replace("/^bf/","",$sourcelower);
		//			$sourcesingular=preg_replace("/s$/","",$sourcelower);
		//
		//			$sql=" SELECT #__$targetlower.* FROM #__$targetlower JOIN
		//  #__$throughtable ON #__$throughtable.$targetsingular"."_id = #__$targetlower".".id
		//  WHeRE #__$throughtable.$sourcesingular"."_id = $id ";
		//			$this->getBySQL($sql);
		//			$this->$targetlower=$this->rows;
		//		} // HABTM through
		//


	}


	/**
	 * book this ite out by me for update
	 *
	 */
	function checkOut(){
		/* set default checked out else bfTable thinks we dont support checkouts*/
		if ($this->checked_out==null){
			$this->checked_out = '0';
		}

		/* Get our user id */
		/* @var $user JUser */
		$user =& bfUser::getInstance();
		$userid = $user->get('id');

		/* do the checkout */
		parent::checkout($userid);

		/* Log it */
		$this->log('bfModel::Checkout ' . $this->getTableName() . ' Row: ' .$this->id );
	}

	/**
	 * Book this item back in
	 *
	 */
	function checkIn ($oid = null){
		if (isset($this->_nometa)) return true;
		/* Looks like the JTAble version of this function is broken so we are using our own */
		$k = $this->_tbl_key;

		if ($oid !== null)
		{
			$this->$k = $oid;
		}
		if ($this->$k == NULL)
		{
			return false;
		}

		$query = "UPDATE ".$this->_table_name
		. "\n SET checked_out = 0, checked_out_time = '".$this->_db->_nullDate."'"
		. "\n WHErE ".$this->_tbl_key." = '". $this->$k ."'"
		;
		$this->_db->setQuery( $query );

		$this->checked_out = 0;
		$this->checked_out_time = '';



		/* cant have a & for PHP4 */
		/* $ret =& $this->_db->query(); */
		$ret = $this->_db->query();

		$this->_checkForDBErrors();

		return($ret);
	}


	/**
     * This probably wont work as we use a different naming convention.
     *
     * @param unknown_type $msg
     */
	function clear(){
		bfTable::getInstance($this->_child_name, $this->_db);
	}

	/**
	 * Unlike publish and unpublish and New and Edit, archive is not in JModel/bfTable!
	 * It is in JContentController in Joomla so we need to call our own model
	 * to archive and unarchive
	 * @param array $cid An Array of ID's to toggle
	 * @param int $archive 0 or 1
	 * @return bool
	 */
	function toggleArchive($cid,$archive){

		/* make sure we have an array of items */
		if (!is_array($cid)) $id=array($cid);
		$cids	= implode(',', $cid);

		/*Get our table name and database object */
		$table = $this->getTableName();

		/* Construct SQL, here we are checking for state or published field name */
		$vars = $this->getPublicVars();
		array_key_exists ('state',$vars) ?	$key = 'state' : $key = 'published';
		$query = 'UPDATE ' . $table . ' SET '. $key .' = '.$archive.' WHERe id IN ( '.$cids.' ) ';
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			bfError::raiseError( 500, $db->getErrorMsg() );
			return false;
		} else {
			return true;
		}
	}

	/**
     * Toggle the access state for this item
     *
     * @param unknown_type $msg
     */
	function toggleAccess($id,$next){
		$this->load($id);
		$this->access = $next;
		$this->store();
	}

	/**
	 * Returns the current models table name
	 *
	 * @author Phil Taylor
	 * @since 0.1
	 * @return string
	 */
	function getTableName(){
		$tbl = $this->_tbl;
		return $tbl;
	}

	/**
	 * Returns the Database Object
	 *
	 * @author Phil Taylor
	 * @since 0.1
	 * @return object
	 */
	function getDBO(){
		$db = &$this->_db;
		return $db;
	}

	/**
	 * update ordering
	 */
	function updateOrder($where = null){
		$k = $this->_tbl_key;

		/* If this table doesnt support ordering */
		if (!array_key_exists( 'ordering', $this->getPublicVars( $this ) ) ) return;

		$order2 = "";

		$query = "SELECT " . $this->_tbl_key . ", ordering"
		. "\n FROM ".$this->_tbl
		. ( $where ? "\n WHERE $where" : '' )
		. "\n ORDER BY ordering$order2 "
		;
		$this->_db->setQuery( $query );

		$this->log($query);

		if (!($orders = $this->_db->loadObjectList()))
		{
			$this->_checkForDBErrors();
			return false;
		}

		/* first pass, compact the ordering numbers */
		for ($i=0, $n=count( $orders ); $i < $n; $i++)
		{
			if ($orders[$i]->ordering >= 0)
			{
				$orders[$i]->ordering = $i+1;
			}
		}

		$shift = 0;
		$n=count( $orders );
		for ($i=0; $i < $n; $i++)
		{
			if ($orders[$i]->$k == $this->$k)
			{
				/* place 'this' record in the desired location */
				$orders[$i]->ordering = min( $this->ordering, $n );
				$shift = 1;
			}
			else if ($orders[$i]->ordering >= $this->ordering && $this->ordering > 0)
			{
				$orders[$i]->ordering++;
			}
		}

		/* compact once more until I can find a better algorithm */
		for ($i=0, $n=count( $orders ); $i < $n; $i++)
		{
			if ($orders[$i]->ordering >= 0)
			{
				$orders[$i]->ordering = $i+1;
				$query = "UPDATE " . $this->_tbl
				. "\n SET ordering = '". $orders[$i]->ordering ."'"
				. "\n WHERE $k = '". $orders[$i]->$k ."'"
				;
				$this->_db->setQuery( $query);
				$this->_db->query();
				$this->_checkForDBErrors();
				//echo '<br />'.$this->_db->getQuery();
			}
		}

		/* if we didn't reorder the current record, make it last */
		if ($shift == 0)
		{
			$order = $n+1;
			$query = "UPDATE " . $this->_tbl
			. "\n SET ordering = '$order'"
			. "\n WHERE $k = '". $this->$k ."'"
			;
			$this->_db->setQuery( $query );
			$this->_db->query();
		}
		$this->_checkForDBErrors();
		return true;
	}

	/**
     * Unarchive this item
     *
     * @param unknown_type $msg
     */
	function unarchive(){
		if (!is_array($id)) $id=array($id);
		$this->publish($id,0);
	}

	/**
	 * Gets the current item id
	 * @author Phil Taylor
	 * @since 0.1
	 * @return int
	 */
	function getCurrentId(){
		return $this->id;
	}

	/**
     * Convert the class name to a string.
     *
     * @param unknown_type $msg
     */
	function __toString (){
		echo "<pre>";
		print_r($this->_child_name);
		echo "</pre>";
	}

	/**
     * Get this object's public vars
     */
	function getPublicVars() {

		/* Get all the object vars */
		$allvars = get_object_vars($this);

		/* start a blank array */
		$publicvars = array();

		/* populate the array with the name=>value pairs */
		foreach( $allvars as $name => $value ) {
			/* only use public vars, ie. the ones not prefixed with _ */
			if (substr( $name, 0, 1 ) != '_') {

				/* 17/10 PT The & was causing PHP4 Compat Issues. */
				$publicvars[$name] = $this->$name; //$value;
				//				$publicvars[$name] = &$value;
			}
		}
		return $publicvars;
	}

	/**
     * Mark this item as published.
     *
     * @param integer $msg
     */
	function publish($id) {
		if (!is_array($id)) $id = array($id);
		parent::publish($id,1);
	}

	/**
     * Mark this item as unpublished.
     *
     * @param integer $msg
     */
	function unpublish($id) {
		if (!is_array($id)) $id=array($id);
		parent::publish($id,0);
	}

	/**
     * Add an array of fields (column headings) in the database
     * to be searched when getAll() is called (i.e. the table column
     * headings for the search filter.
     *
     * @param array $fields
     */

	function addSearchField( $fields ) {
		if (is_array( $fields )) {
			foreach( $fields as $field ) {
				$this->_search_fields[] = $field;
			}
		} else {
			$this->_search_fields[] = $fields;
		}
	}

	/**
     * Get the filter search fields.
     */
	function _getSearchFields() {
		return $this->_search_fields;
	}

	/**
     * Get the SQL for the search over the _search_fields
     * Returns a string contqinnig the search WHERE clause
     *
     * @param unknown_type $msg
     */
	function _getSearchString($filter) {
		$this->_getSearchFields();
		if ( sizeof( $this->_search_fields ) == 0 ) {
			return "";
		}
		$LOGIC="";
		$search_string = "WHERE ";
		foreach( $this->_search_fields as $field ) {

			/* field name must be in back ticks else desc, introtext and other reserved words wil fail */
			$search_string.="$LOGIC `$field` like '%$filter%' ";
			$LOGIC='OR';
		}
		return $search_string;
	}

	/**
     * Used to say that this model hasMany $things in the ORM
     * for example a Hand has many Fingers. In the Hand constructor
     * call $this->HasMany('Fingers');
     *
     * @param model $things
     */
	function hasMany( $things ) {
		$this->_hasMany[$things]=true;
	}

	/**
     * I define a hasOne ORM relationship
     *
     * @param unknown_type $thing
     */
	function hasOne( $thing ) {
		$this->_hasOne[$thing]=true;
	}


	/**
     * I define a Has and Belongs to Many ORM relationship
     * $model_name is he model to which I am related.
     * $through_table holds the relationship between the two models.
     * See the BF WIKI for more details.
     *
     * @param model $model_name
     * @param db_table $through_table
     */
	function HABTMthrough( $model_name, $through_table ) {
		$this->_habtm[$model_name]=$through_table;
	}

	/**
     * I define a belongsTo ORM relationship
     * relating to my parent's table.
     *
     * @param model $owner
     */
	function belongsTo( $owner ) {
		$this->_belongsTo[$owner]=true;
	}
	/**
     * I return the last insert id for this model
     *
     */
	function getInsertId() {
		return($this->_db->insertid());
	}

	/**
     * I handle logging for the controller
     *
     * @param unknown_type $msg
     */
	function log( $msg ) {
		$this->log =& bfLog::getInstance();
		$this->_log->log(__CLASS__);
		$this->_log->log($msg);
	}


	/**
	 * Use this instead of JController::getModel because
	 * a) it stops Joomla Developers from pulling the rug fromunder our feet (again)
	 * b) We can call bfError instead of JError to get better control of error handling
	 */
	function &_getModel($modelClass) {


		// $modelClass   = preg_replace( '#\W#', '', $name );

		if (!class_exists( $modelClass )) {
			// If the model file exists include it and try to instantiate the object
			// The bfString::strtolower bit just follows the sad Joomla convention.
			global $mainframe;
			$_BF_FRONT_MODEL_DIR = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'model';
			$path = $_BF_FRONT_MODEL_DIR . DS . bfString::strtolower($modelClass).'.php';
			$this->log("bfCS Pulling in $path");
			if (!file_exists($path)) {
				bfError::raiseError( 0, 'Model ' . $modelClass . ' not supported. File not found.' );
				die('Could Not include a model as the model name not set: '.$path);
			}
			require( $path );
			if (!class_exists( $modelClass ))
			bfError::raiseError( 0, 'Model class ' . $modelClass . ' not found in model file.' );
		}

		$model = new $modelClass();
		return $model;
	}

	/**
	* Moves the order of a record
	* @param integer The increment to reorder by
	* @param integer The id of the row in the model
	* @param string Where
	*/
	function order($direction, $id, $where){
		$this->load( (int) $id );
		$this->move($direction, $where );

	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $where
	 */
	function reOrder($where=null){
		$this->updateOrder($where);
	}

	/**
     * Reload this object from the DB
     *
     * @param unknown_type $msg
     */
	function reload() {
		$this->get( $this->id );
	}

	/**
 	 * I set a jtable attribute
	 */
	function setAttribute( $attribute, $value ) {
		$this->$attribute=value;
	}

	/**
 	 * I get a jtable attribute
	 */
	function getAttribute( $attribute ) {
		return($this->$attribute);
	}

	/**
 	 * I return the ORDER BY SQL query string which is composed
	 * from the _orderby and _orderdir arrays
	 *
	 * @return string
	 */
	function getOrderBySQL() {
		$filter_order = $this->_session->get('filter_order','');
		if ($filter_order != '') {
			$dir = $this->_session->get('filter_order_Dir','ASC');
			$this->_orderby = array($filter_order => $dir);
		}

		$sql="";
		foreach($this->_orderby as $order => $dir) {
			if ($sql != '') $sql .= ", ";
			$sql.='`'.$order.'`'." ".$dir;
		}
		if ($sql != '') return " ORDER BY $sql";

		$try = $this->_registry->getValue('ORDERBY',null);
		if ($try) return " ORDER BY $try";
		return('');
	}

	/**
	 *
	 * I check for database errors
	 */

	function _checkForDBErrors() {
		if ($this->_db->_errorMsg != '') {
			echo "Found DB Error ".$this->_db->_errorNum ."\n";
			echo $this->_db->_errorMsg ."\n";
			bfError::raiseError($this->_db->_errorNum, $this->_db->_errorMsg );
			return(false);
		}
		return(true);
	}

	/**
	 * I check any necessary validation then call the bfTable store()
	 */

	function store() {
		$required_fields=array();
		if (sizeof($this->_validates_presence_of) > 0) {
			foreach($this->_validates_presence_of as $required_field) {
				if (!isset($this->$required_field))
				$required_fields[]=$required_field;
			}
		}
		if (sizeof($required_fields) > 0) {
			$errmsg="The following are required fields: ".join(', ',$required_fields);
			bfError::raiseError(403,$errmsg);
		}


		if (sizeof($this->_validates_uniqueness_of) > 0) {
			foreach( $this->_validates_uniqueness_of as $unique_field) {
				$WHERE="WHERE $unique_field = '".$this->$unique_field ."'";
				if (isset($this->id) && ($this->id > 0))
				$WHERE.=" and id != '".$this->id ."'";
				$count=$this->count($WHERE);
				if ($count > 0) bfError::raiseError(403,"$unique_field must be unique. Another row already has this value '".$this->$unique_field."'");
			}
		}
		$ret=parent::store();
		return($ret);
	}

	/**
	 * I add the name of a field that must exist when the model is stored.
	 * stored. If the field does not have a value then $this->store()
	 * raises a 403 error.
	 */

	function validates_presence_of( $column ) {
		$this->_validates_presence_of[]=$column;
	}

	/**
	 * I add the name of a field that must be unique when the model is
	 * stored. If the field is not unique then $this->store() raises a
	 * 403 error.
	 */

	function validates_uniqueness_of( $column ) {
		$this->_validates_uniqueness_of[]=$column;
	}

	/**
	* Set the value of the class variable
	* @param string The name of the class variable
	* @param mixed The value to assign to the variable
	*/
	function set( $_property, $_value ) {
		$this->$_property = $_value;
	}
}
?>
