<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
* @version $Id: bfControllerServices.php 953 2007-07-03 20:06:45Z phil $
* @package bfFramework
* @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
* @license Commercial
* @link http://www.blueflameit.ltd.uk
* @author Blue Flame IT Ltd.
*/

/**
 * the bfController class. Provides the controller services to the MVC.
 *
 */
class bfControllerServices  {

	/**
	 * The name of the controller
	 *
	 * @var		array
	 * @access protected
	 */
	var $_name = null;

	/**
	 * Array of class methods
	 *
	 * @var	array
	 * @access protected
	 */
	var $_methods 	= null;

	/**
	 * Array of class methods to call for a given task.
	 *
	 * @var	array
	 * @access protected
	 */
	var $_taskMap 	= null;

	/**
	 * Current or most recent task to be performed.
	 *
	 * @var	string
	 * @access protected
	 */
	var $_task 		= null;

	/**
	 * The mapped task that was performed.
	 *
	 * @var	string
	 * @access protected
	 */
	var $_doTask 	= null;

	/**
	 * The set of search directories for resources (views or models).
	 *
	 * @var array
	 * @access protected
	 */
	var $_path = array(
	'model'	=> array(),
	'view'	=> array()
	);

	/**
	 * URL for redirection.
	 *
	 * @var	string
	 * @access protected
	 */
	var $_redirect 	= null;

	/**
	 * Redirect message type.
	 *
	 * @var	string
	 * @access protected
	 */
	var $_messageType 	= null;

	/**
	 * ACO Section for the controller.
	 *
	 * @var	string
	 * @access protected
	 */
	var $_acoSection 		= null;

	/**
	 * Default ACO Section value for the controller.
	 *
	 * @var	string
	 * @access protected
	 */
	var $_acoSectionValue 	= null;

	/**
	 * An error message.
	 *
	 * @var string
	 * @access protected
	 */
	var $_error;

	/**
	 * Set to false if you don't want an adminform on an admin page
	 * @var boolean
	 */
	var $_adminform=true;
	/**
	 * The log object
	 * @var unknown_type
	 */
	var $_log;
	/**
	 * A list of the models I know about. Used for passing model data
	 * as arrays (not objects) to the view.
	 *
	 * @var unknown_type
	 */
	var $_modellist=array(); /* A hash of models used by this instance of the controller */
	/**
	 * I am called from AJAX. _arguments is an array of the passed
	 * arguments from xAJAX (xindex?)
	 *
	 * @var unknown_type
	 */
	var $_arguments;
	/**
	 * the layout for the view. Can be view (i.e. use the view)
	 * or message (just use the text that the controller passed back)
	 * or XML (rend an XML view).
	 *
	 * @var unknown_type
	 */
	var $_layout='view';
	/**
	 * The return html for layout=message mode
	 *
	 * @var unknown_type
	 */
	var $_message;
	/**
	 * the xajax target were the output goes
	 *
	 * @var unknown_type
	 */
	var $_xajaxTarget = '';
	/**
	 * the xajax action
	 *
	 * @var unknown_type
	 */
	var $_xajaxAction;
	/**
	 * the view (by default this is view/$task.php
	 *
	 * @var unknown_type
	 */
	var $_view=null;
	/**
	 * the name of this component e.g. com_bfforms
	 *
	 * @var unknown_type
	 */
	var $_component_name=null;
	/**
	 * the default logging state
	 *
	 * @var unknown_type
	 */
	var $_controller_log="off";

	/**
	 * I store a pointer to the bfSession instance
	 *
	 * @var bfSession instance
	 */
	var $_session = null;

	/**
	 * I store the page header
	 *
	 * @var unknown_type
	 */
	var $_pageHeader = null;

	/**
	 * I store the page title
	 *
	 * @var unknown_type
	 */
	var $_pageTitle = null;

	/**
	 * I store the toolbar html
	 *
	 * @var unknown_type
	 */
	var $_toolbar = '';

	/**
	 * I store any xAJAX alert message
	 *
	 * @var string
	 */
	var $_xajaxAlert = null;

	/**
	 * I know if the page is a popup or not
	 *
	 * @var bool
	 */
	var $_isPopup = false;

	/**
	 * I store the reegistry
	 *
	 * @var object bfRegistry
	 */
	var $_registry = null;

	/**
	 * I am a pointer to the xAJAX objResponse object
	 *
	 * @var object bfRegistry
	 */
	var $xajax = null;

	/**
	 * I store the array of textareas that have editors
	 */
	var $_editorAreas = array();

	/**
	 * the PHP4 constructor
	 *
	 */
	function bfController() {
		$this->__construct();
	}

	/**
	 * the constructor
	 * initialise the modellist and the session
	 * then call the JController constructor. The need for
	 * JController is dubious.
	 */

	function __construct() {
		global $mainframe;

		$this->_xajaxTarget = $mainframe->get('component');

		//Initialize private variables
		$this->_redirect	= null;
		$this->_message		= null;
		$this->_messageType = 'message';
		$this->_taskMap		= array();
		$this->_methods		= array();
		$this->_data		= array();

		// Get the methods only for the final controller class
		$thisMethods	= get_class_methods( get_class( $this ) );
		$baseMethods	= get_class_methods( 'bfController' );

		$methods		= array_merge( $thisMethods, $baseMethods );
		//		$methods		= array_diff( $thisMethods, $baseMethods );

		//		print_R($methods);
		// Add default display method
		$methods[] = 'display';
		// Iterate through methods and map tasks
		foreach ( $methods as $method ) {
			if ( substr( $method, 0, 1 ) != '_' ) {
				$this->_methods[] = strtolower( $method );
				// auto register public methods as tasks
				$this->_taskMap[strtolower( $method )] = $method;
			}
		}

		//Set the controller name
		if ( empty( $this->_name ) )
		{
			if ( isset( $config['name'] ) )
			{
				$this->_name = $config['name'];
			}
			else
			{
				$r = null;
				if ( !preg_match( '/(.*)Controller/i', get_class( $this ), $r ) ) {
					JError::raiseError(
					500, JText::_(
					'JController::__construct() :'
					.' Can\'t get or parse class name.'
					)
					);
				}
				$this->_name = strtolower( $r[1] );
			}
		}

		// If the default task is set, register it as such
		if ( isset( $config['default_task'] ) ) {
			$this->registerDefaultTask( $config['default_task'] );
		} else {
			$this->registerDefaultTask( 'display' );
		}

		//		 set the default model search path
		//		if ( isset( $config['model_path'] ) ) {
		//			// user-defined dirs
		//			$this->_setPath( 'model', $config['model_path'] );
		//		} else {
		//			$this->_setPath( 'model', null );
		//		}
		//
		//		// set the default view search path
		//		if ( isset( $config['view_path'] ) ) {
		//			// user-defined dirs
		//			$this->_setPath( 'view', $config['view_path'] );
		//		} else {
		//			$this->_setPath( 'view', null );
		//		}

		/* initialise the modellist */
		$this->_modellist = array();

		/* initialise the session and log*/
		$this->_session =& bfSession::getInstance();
		$this->_log =& bfLog::getInstance();
		global $mainframe;

		$this->_registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));
	}


	/**
	 * Execute a task by triggering a method in the derived class
	 * If no task found try to set the view
	 */
	function execute( $task ) {

		/** Are we in admin or not */
		$isAdmin = bfCompat::isAdmin() ? 'Admin' : 'Front';

		/* Check we have access to this controller method */
		bfSecurity::acl($isAdmin. 'Controller.'.$task, ' the method ' . $task ,true);

		/* store our task for later processing */
		$this->_task = $task;


		/* all our controller methods should be lower case */
		$task = bfString::strtolower( $task );

		//		print_r($this->_taskMap);
		//		die;


		/* Set the default view to a viewfile minus the x */
		$defaultView = (bfString::substr($task,0,1)=='x') ? (substr($task,1, strlen($task))) : $task;
		$this->setView($defaultView);

		if (isset( $this->_taskMap[$task] )){
			/* We have a method in the map to this task */
			$doTask = $this->_taskMap[$task];
			/* call the task method */
			//
			// Reminder: call_user_func(), the array is the class then the method
			// then you pass in any arguments in the parameter list, not the array
			//
			return call_user_func( array( &$this, $doTask ),$this->_arguments );
		} else {
			if (bfString::substr($task,0,1)=='x'){

				/* remove x prefix */
				$task = bfString::substr($task, 1 , strlen($task));

				/* store our task for later processing */
				$this->_task = $task;

			}
		}
	}

	/**
	 * Normally the view has the same name as the task,
	 * but this function overrides it.
	 *
	 * @param unknown_type $view
	 */
	function setView( $view ) {
		$this->_view = $view;
		$this->setPageHeader(bfString::ucwords($view));
	}

	/**
	 * Returns the current set view name...
	 *
	 * @return string The view Name
	 */
	function getView() {
		return($this->_view);
	}

	/**
	 * The component name is used to put output into a div
	 * with an id which is the same as the "ComponentName". This convention
	 * ensures we can always send xajax output which replaces this div
	 * back to the right target.
	 *
	 * @param string $name
	 */
	function setComponentName( $name ) {
		$this->_component_name = $name;
	}

	/**
	 * Returns the current set component_name...
	 *
	 * @return string
	 */
	function getComponentName() {
		return($this->_component_name);
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function renderView($justComponent=false){
		global $mainframe;

		/* Get the Registry object onto this scope */
		$registry =& $this->_registry;

		/* Get the session vars into this scope */
		$session = array();
		foreach($this->session->getSessionArray() as $name => $value ) {
			$session[$name]=$value;
		}

		/* Get the models in this scope */
		foreach( $this->getModelList() as $modelname => $modelObject) {
			$$modelname = $modelObject->getPublicVars();
		}

		/* Set $controller so we can use it in the view */
		$controller =& $this;

		$document = new bfDocument();

		/* Set the session */
		$bfsession =& $session;

		/* get the view */
		$view = (string) $this->getView();

		/* Start of our output, buffer it */
		ob_start();

		/* if popup */
		if ($this->_registry->getValue('isPopup') == true){
			echo '<html><head>';
			echo '<script type="text/javascript" language="javascript" src="'
			.bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME
			.'/system/blueflame/bfCombine.php?type=js&c='
			.$mainframe->get('component_shortname').'&f=mootools,jquery,jquery.tabs,jquery.greybox,bfadmin_js,admin_js'.'"></script>';

			echo '<link rel="stylesheet" type="text/css" href="'
			. bfCompat::getLiveSite()
			. '/'
			._PLUGIN_DIR_NAME
			.'/system/blueflame/bfCombine.php?type=css&c='
			.$mainframe->get('component_shortname')
			.'&f=bfadmin_css,admin_css" />';
			echo '</head><body>';
		}

		/* display the preview   */
		if (bfCompat::isAdmin() && $this->_adminform && $justComponent ===false ){
			echo bfHTML::preView();
		}

		$_BF_ADMIN_VIEW_DIR = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'view' . DS . 'admin';
		$_BF_FRONT_VIEW_DIR = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'view' . DS . 'front';
		if (file_exists($_BF_ADMIN_VIEW_DIR . DS . $view . '.php') && bfCompat::isAdmin() ) {
			/* Look in the components view folder */
			require($_BF_ADMIN_VIEW_DIR . DS . $view . '.php');

		} elseif (file_exists($_BF_FRONT_VIEW_DIR . DS . $view . '.php')) {
			/* Look in the components view folder */
			require($_BF_FRONT_VIEW_DIR . DS . $view . '.php');

		} elseif (file_exists(_BF_FRONT_LIB_VIEW_DIR . DS . $view . '.php')){
			/* Look in the framework to see if its a  framework view */
			require(_BF_FRONT_LIB_VIEW_DIR . DS . $view . '.php');

		} else {
			bfError::raiseError('404','The view file could not be located: ' . $view
			. ' I looked in these places:'
			. $_BF_ADMIN_VIEW_DIR . DS . $view . '.php AND'
			. _BF_FRONT_LIB_VIEW_DIR . DS . $view . '.php');
		}
		if (bfCompat::isAdmin() && $this->_adminform && $justComponent ===false ){
			echo bfHTML::postView();
		}
		if ($this->_registry->getValue('isPopup') == true){
			echo '</body></html>';
		}
		$HTML .= ob_get_contents();
		ob_end_clean();

		if (!bfCompat::isAdmin()){
			$HTML .= bfDocument::initTips();
		}


		if ($this->_registry->getValue('isMambot') != true)
		bfCompat::setPageTitle($controller->getPageTitle());


		if ($this->_registry->getValue('isPopup') == false){
			/* xajax */
			return $HTML;
		} else {
			echo $HTML;
		}
	}

	/**
	 * Set arguments for handling calls from xajax.
	 * Split arguments of the type name=value into a hash
	 * Arguments can be retreived by getArgument using either
	 * an id or a hash key.
	 * Squirrel name=value pairs away so we can use the same view
	 * for xajax and non xajax
	 * @param array $args
	 */
	function setArguments( $args = array() , $xAJAX=true) {

		if ($xAJAX===false){
			foreach ($args as $k=>$v){
				/* Clean through bfSecurity */
				$k = bfSecurity::cleanVar($k);
				$v = bfSecurity::cleanVar($v);

				/**
				 * @author Phil Taylor
				 * @todo explain to Phil the reason for this please
				 * Why are we doing this - this may make problems for us?
				 * if we are setting id in the session ... make cause problems?
				 * Dont know - just doesnt seem right adding the incoming args
				 * into the session every page load.
				 */
				$this->_session->set($k,$v);

				$this->_arguments[$k]=$v;
			}
		} else {
			//		$this->_arguments = $args;


			$split=array();
			$this->log("==== bfController setArguments === ");
			$i=1;
			if (count($args)){
				foreach($args as $argument) {
					/* handle strings with = in them */
					if (is_string($argument)){

						$argument=bfString::trim($argument);
						if (preg_match('/^(\w+)\W*=(.*)/',$argument,$split)) { // Split around the first =
							if (!isset($split[1])) $split[2]='';
							/* Clean through bfSecurity */
							$k = bfSecurity::cleanVar($split[1]);
							$v = bfSecurity::cleanVar($split[2], 4);

							$this->_arguments[$k]=$v;
							$this->_session->set($k,$v);
							$this->log("setArguments: Setting ".$k." to ".$v);
						} else {
							/* handle non pairs */
							/* Clean through bfSecurity */
							$i 		  = bfSecurity::cleanVar($i);
							$argument = bfSecurity::cleanVar($argument, 4);
							$this->_arguments[$i] = $argument;
							$i++;
						}
					} else {
						// the arguments are passed in from xAJAX
						// as arg1,arg2 etc or as
						// key1=val1,key2=val2,... or a mixture of
						// both. this parses them into the
						// _arguments hash.
						foreach($argument as $k=>$v) {

							/* @todo reapply security here */
							$k = $k;
							$v = $v;
							$this->_arguments[$k] = $v;
							$i++;
						}
					}
				}
			} else {
				$this->_arguments = bfSecurity::cleanVar($args, 4);
			}
		}
	}

	/**
	 * Retreive an input xajax function argument
	 *
	 * @param mixed $id
	 * @param unknown_type $default
	 * @return unknown
	 */
	function getArgument( $id , $default='' ) {
		if (isset( $this->_arguments[ $id ] )) {
			return( $this->_arguments[ $id ] );
		}
		return($default);
	}

	/**
	 * Return a hash of all arguments
	 *
	 * @return unknown
	 */
	function getAllArguments(){
		return $this->_arguments;
	}

	/**
	 * Get this object's public vars
	 *
	 * @return unknown
	 */
	function getPublicVars() {
		$allvars=get_object_vars($this);
		$publicvars=array();
		foreach( $allvars as $name => $value ) {
			if (bfString::substr( $name, 0, 1 ) != '_') {
				$publicvars[$name]=$value;
			}
		}
		return( $publicvars );
	}

	/**
	 * Get the required model and keep track of the models
	 *
	 * @param unknown_type $name
	 * @return unknown
	 */
	function &getModel( $name , $dontRecord=false) {
		/* Save the last accessed model */

		$this->_log->log ('setting last model to ' . $name);

		if ($dontRecord===true) {

		} else {
			if (@!defined('_POPUP')){
				$this->session->set('lastModel',$name,'default');
			} else{
				$this->session->set('lastModel',$name,'popup');
			}
		}

		if ( isset($this->_modellist[$name]) ) {
			return $this->_modellist[$name];
		} else {
			$model =&  $this->_getModel( $name );
			$log =& bfLog::getInstance();
			$this->_modellist[$name] =& $model;
			return $model;
		}
	}

	/**
     * Get a hash of all of the models that the controller knows about
     *
     * @return unknown
     */
	function &getModelList() {
		return $this->_modellist;
	}

	/**
	 * set up the redirect to a task in this controller
	 *
	 * @param unknown_type $task
	 * @param unknown_type $msg
	 * @param unknown_type $msgType
	 */
	function setRedirect( $task, $msg='', $msgType='' ) {
		$component=get_class($this);
		$component=ereg_replace("Controller","",$component);
		$this->setRedirect("index2.php?option=$component&task=$task",$msg,$msgType);
		$this->log("================================== Redirect to $task ================================");
	}

	/**
	 *  Set the layout to text/view/xml
	 * 		text => the view comes from $controller->getMessage();
	 * 		view => the view comes from the view i.e. view/$task.php
	 * 		xml  => the view comes from view/$task.xml.php (not	implemented yet)
	 *
	 * @param unknown_type $type
	 */
	function setLayout( $type ) {
		$this->_layout=$type;
	}

	/**
	 * Get the layout type (text/view/xml etc)
	 *
	 * @return unknown
	 */
	function getLayout() {
		// Check for an xAJAX / Testing error

		$error=$this->_registry->getValue('error');
		$errno=$this->_registry->getValue('errno');
		if ($error != '' || $errno != '') return('error');

		return $this->_layout ;
	}

	/**
	 * Set the message for layout=text mode
	 *
	 * @param unknown_type $msg
	 */
	function setMessage($msg) {
		$this->_message = $msg;
	}
	/**
	 * Get the message for layout/text mode
	 *
	 * @return unknown
	 */
	function getMessage() {
		return( $this->_message );
	}

	/**
	 * setXajaxAction e.g. addAssign
	 *
	 * @param unknown_type $action
	 */
	function setXajaxAction( $action ) {
		$this->_xajaxAction=$action;
	}

	/**
	 * Get the xAJAX action
	 *
	 * @return unknown
	 */
	function getXajaxAction() {
		return($this->_xajaxAction);
	}

	/**
	 * Set the page header. (the blue text at the top of the page
	  under the main menu.)
	 *
	 * @param unknown_type $str
	 */
	function setPageHeader($str){
		$this->_pageHeader = $str;
	}

	/**
	 * Return the page header
	 *
	 * @return unknown
	 */
	function getPageHeader(){
		return $this->_pageHeader ? $this->_pageHeader : $this->_registry->getValue('Component.Title');
	}

	/**
	 * Set the page title
	 *
	 * @param unknown_type $str
	 */
	function setPageTitle($str){
		if (bfCompat::isAdmin()){
			$this->_pageTitle = $str;
		} else {
			$this->_pageTitle = $str;
			bfDocument::setTitle( $str );
		}
	}

	/**
	 * Get the page title
	 *
	 * @return unknown
	 */
	function getPageTitle(){
		if (bfCompat::isAdmin()){
			return bfText::_($this->_registry->getValue('Component.Title')) . ($this->_pageTitle ? ' :: ' . $this->_pageTitle : '');
		} else {
			return $this->_pageTitle;
		}
	}

	/**
	 * Set the toolbar HTML
	 *
	 * @param unknown_type $html
	 */
	function setToolbar($html){
		$this->_toolbar = $html;
	}

	/**
	 * Get the toolbar HTML
	 *
	 * @return unknown
	 */
	function getToolbar(){
		return $this->_toolbar;
	}

	/**
	 * sets the div (or whatever) that the action will update
	 *
	 * @param unknown_type $action
	 */
	function setXajaxTarget( $action ) {
		$this->_xajaxTarget=$action;
	}

	/**
	 * Get the xAJAX target
	 *
	 * @return unknown
	 */
	function getXajaxTarget() {
		return($this->_xajaxTarget);
	}

	/**
	 * Sets a string for an xAJAX Alert
	 *
	 * @param string $str
	 */
	function setxAJAXAlert($type, $str){
		$this->_xajaxAlert = array($type, $str);
	}

	/**
	 * Returns the string for use in the xAJAX Alert
	 *
	 * @return string The Alert Mesage
	 */
	function getxAJAXAlert(){
		if ($this->_xajaxAlert){
			return $this->_xajaxAlert;
		} else {
			return false;
		}
	}

	/**
     * Outputs a message to the bfLog with bfController prepended.
     *
     * @param unknown_type $msg
     */
	function log( $msg ) {
		if (is_string($msg))
		$this->log->log(__CLASS__.": $msg");
		else
		$this->log->log($msg);
	}

	/**
	 * Create the toolbar.
	 *
	 * @param unknown_type $task
	 * @param unknown_type $isXAJAX
	 */
	function createToolbar($task, $isXAJAX=false) {
		if (!class_exists('bfToolbar'))	bfLoad('bfToolbar');
		$bfToolbar =& bfToolbar::getInstance($this);
		if ($isXAJAX===true){
			$bfToolbar->createXAJAXToolbar($task);
		} else {
			$bfToolbar->create($task);
		}
	}

	/**
	 * Return the pathway from the mainframe.
	 *
	 */
	function &getPathway() {
		$pathway =& bfDocument::getPathWay();
		return $pathway;
	}

	/**
	 * I set which textareas convert to TinyMCE editors
	 *
	 * @param string $textarea
	 */
	function viewHasEditor($textarea){
		$this->_editorAreas[] = $textarea;
	}

	/**
	 * I return an array of textareas that need TinyMCE activation
	 *
	 * @return unknown
	 */
	function getEditorAreas(){
		return $this->_editorAreas;
	}

	/**
	 * Use this instead of JController::getModel because
	 * a) it stops Joomla Developers from pulling the rug fromunder our feet (again)
	 * b) We can call bfError instead of JError to get better control of error handling
	 */
	function &_getModel($modelClass) {
		global $mainframe;

		// $modelClass   = preg_replace( '#\W#', '', $name );

		if (!class_exists( $modelClass )) {
			// If the model file exists include it and try to instantiate the object
			// The bfString::strtolower bit just follows the sad Joomla convention.

			$path = _BF_JPATH_BASE . DS . 'components' . DS . $mainframe->get('component') . DS . 'model' . DS . bfString::strtolower($modelClass).'.php';
			$this->log("bfCS Pulling in $path");
			if (!file_exists($path)) {
				bfError::raiseError( 0, 'Model ' . $modelClass . ' not supported. File not found.: ' . $path );
				die(bfText::_('Could not include a model as the model name is not set'));
			}
			require( $path );
			if (!class_exists( $modelClass ))
			bfError::raiseError( 0, bfText::_('Model class not found in model file: ').$modelClass );
		}

		$model =& new $modelClass();
		return $model;
	}

	/**
	 * I mimic the Ex-joomla function of the same name
 	*/
	function setModelPath( $path ) {
		//				$this->_setPath( 'model', $path );
	}

	/**
	 * I set the adminform to true or false
	 */
	function setAdminForm( $value ) {
		$this->_adminform=$value;
	}

	/**
	 * Register the default task to perform if a mapping is not found.
	 *
	 * @access	public
	 * @param	string The name of the method in the derived class to perform if
	 * a named task is not found.
	 * @return	void
	 * @since	1.5
	 */
	function registerDefaultTask( $method )
	{
		$this->registerTask( '__default', $method );
	}

	/**
	 * Register (map) a task to a method in the class.
	 *
	 * @access	public
	 * @param	string	The task.
	 * @param	string	The name of the method in the derived class to perform
	 * for this task.
	 * @return	void
	 * @since	1.5
	 */
	function registerTask( $task, $method )
	{
		if ( in_array( strtolower( $method ), $this->_methods ) ) {
			$this->_taskMap[strtolower( $task )] = $method;
		} else {
			bfError::raiseError( 404, JText::_( 'Method not found:' ) . $method );
		}
	}

}
?>
