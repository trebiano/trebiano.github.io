<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfController.php 1093 2007-07-13 15:27:55Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2007 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 * On tab index controllers call
 *     $this->session->returnto( $this->getView() );
 * so that after editing etc you come back to the right tab.
 *
 */

bfLoad('bfControllerServices');

/**
 * I am the main controller that bf components extend to get
 * their functionality. I provide defaults for just about every task.
 *
 */
class bfController extends bfControllerServices {

	/**
	 * The session
	 *
	 * @var unknown_type
	 */
	var $session;

	/**
	 * The log
	 *
	 * @var unknown_type
	 */
	var $log;

	/**
	 * Where I would redirect to if I wasn't in test mode
	 * @var string
	 */
	var $redirect;

	/**
	 * Constructor just calls the parent bfController
	 *
	 */
	function bfController() {
		$this->__construct();
	}

	/**
	 * Constructor. Set up the session and log then
	 * call the parent constructor.
	 *
	 */
	function __construct() {
		global $mainframe;
		$this->session =& bfSession::getInstance($mainframe->get('component'));
		$this->log =& bfLog::getInstance($mainframe->get('component'));
		parent::__construct();
	}

	function xaddmappedcategory(){
		$args = $this->getAllArguments();
		$maps =& $this->getModel('category_map');
		$mapid = $maps->addMap($args[2],$args[1]);

		$catmaps =& $this->getModel('category');
		$catmaps->get($args[1]);

		$path = $catmaps->category_path . ' / ' . $catmaps->title;

		$html = "<li id=\"catid".$mapid."\" class=\"submenuicon-categories\"><span><a onclick=\"xajax.call(getbfHandler(),Array(\'xremovemappedcategory\',\'".$mapid."\'));jQuery(\'#catid".$mapid."\').hide(\'slow\');\" href=\"javascript:void(0);\" class=\"hasTip\">".$path."</a></span></li>";
		$this->xajax->addscript("jQuery('#categorylistul').append('".$html."');");
		$this->setLayout('none');
	}

	function xaddcomponentlink(){
		$args = $this->getAllArguments();
		$db = bfCompat::getDBO();
		$db->setQuery("INSERT INTO `#__components`
		VALUES
		('', '".$args[0]."', 'option=".$args[1]."', 0, 0, 'option=".$args[1]."', '".$args[0]."', '".$args[1]."', 0,
		'../mambots/system/blueflame/view/images/menulogo.gif', 0, '');");
		$db->query();
		$this->xajax->addalert('Added Component Link');
	}
	
	function xremovemappedcategory(){
		$args = $this->getAllArguments();

		$map =& $this->getModel('category_map');
		$map->removeMap((int)$args[1]);

		$this->setxAJAXAlert('confirm',bfText::_('Category Removed').'!');
		$this->setLayout('none');

		/* fool */
		$fool =& $this->getModel('article');
	}

	function xremovemappedfile(){
		$args = $this->getAllArguments();

		$map =& $this->getModel('file_map');
		$map->removeMap((int)$args[1]);

		$this->setxAJAXAlert('confirm',bfText::_('File Mapping Removed').'!');
		$this->setLayout('none');

		/* fool */
		$fool =& $this->getModel('article');
	}

	function xremoveallcategoriesonlisting(){
		$args = $this->getAllArguments();

		/* reset maps */
		$map =& $this->getModel('category_map');
		$map->removeAllMapsToListing($args[1]);

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',bfText::_('All Categories Removed').'!');
		$this->setLayout('none');

		/* fool */
		$fool =& $this->getModel('article');
	}

	function xremoveallfilesonlisting(){
		$args = $this->getAllArguments();

		/* reset maps */
		$map =& $this->getModel('file_map');
		$map->removeAllMapsToListing($args[1]);

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',bfText::_('All File Maps Removed').'!');
		$this->setLayout('none');

		/* fool */
		$fool =& $this->getModel('article');
	}


	/**
	 * Mimic a page redirect for xajax. Just
	 * setup the session mode and call the required
	 * controller method. Blindingly fast!
	 * Do not call the redirect method if in test mode
	 * so testing can verify the redirect
	 */
	function _redirect( $controller_method ) {
		$this->log->log("redirecting to ".$controller_method);
		$view=$controller_method;
		$view=preg_replace('/^x/','',$view);
		$this->setView($view);
		$this->session->setMode($controller_method);
		$this->redirect=$controller_method;
		if (_BF_TEST_MODE != true ) $this->$controller_method();
	}

	/**
	 * I handle the cancel toolbar button
	 *
	 */
	function xcancel(){
		/* build our view from the returnto session var */
		$view = 'x'.$this->session->get('returnto' , 'controlpanel' );
		/* Set the task/view to redirect to */
		$this->_redirect($view);
	}

	/**
	 * I flush all object/template caches known to the framework
	 *
	 */
	function xmaintenance_clearobjectcache(){

		/* Clear object cache */
		bfLoad('bfCache');
		$cache =& bfCache::getInstance();
		if ($cache->flush()){
			/* Set an Alert for feedback */
			$this->setxAJAXAlert('confirm',bfText::_('Object Cache Flushed Successfully').'!');
		} else {
			/* Set an Alert for feedback */
			$this->setxAJAXAlert('error',bfText::_('Check permissions on cache folder!').'!');

		}

		/* clear smarty templates cache */
		bfLoad('bfSmarty');
		/* @var $smarty smarty */
		$smarty =& bfSmarty::getInstance();
		/* clear smarty cache */
		$smarty->clear_all_cache();
		/* clear smarty compiled templates for this component */
		$smarty->clear_compiled_tpl();
		/* dont return any text */
		$this->setLayout('none');
	}



	/**
	 * I display all the customfields in an index view
	 *
	 */
	function xcustomfields(){
		/* & is important for PHP4 */
		/* Load Model */
		$customfields =& $this->getModel('customfield');

		/* Get all rows */
		$customfields->getAll();

		/* set last view into session */
		$this->session->returnto( $this->getView() );

		/* set the view file (optional) */
		$this->setView('customfields');

	}

	function xmaintenance_findorphans(){
		set_time_limit(60 * 5);

		/* flush object cache */
		bfLoad('bfCache');
		$cache =& bfCache::getInstance();
		$cache->flush();

		/* reset all counts */
		$directory_category =& $this->getModel('category');
		$num = $directory_category->findorphans();

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',$num . bfText::_(' orphans found and assigned to Root Category').'!');
		$this->setLayout('none');
	}

	function xmaintenance_recalccatpaths_withrefresh(){
		$this->xmaintenance_recalccatpaths();
		$this->_redirect('xcategories');
		$this->setLayout('html');
	}

	function xmaintenance_recalccatpaths(){
		set_time_limit(60 * 5);

		/* flush object cache */
		bfLoad('bfCache');
		$cache =& bfCache::getInstance();
		$cache->flush();

		/* reset all counts */
		$directory_category =& $this->getModel('category');
		$directory_category->recalcPaths();

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',bfText::_('All Category paths calculated...').'!');
		$this->setLayout('none');
	}

	/**
	 * Display index view of layout templates
	 *
	 */
	function xlayouts(){
		$templates =& $this->getModel('layout');
		$templates->getAll('');
		/* set last view into session */
		$this->session->returnto( $this->getView() );

		$this->setView('layouts');
	}

	/**
	 * I clear any Joomla Caches
	 * Useful for purging content items with wrong mambots fired in them
	 */
	function xmaintenance_clearcontentcache(){
		$cache =& mosCache::getCache();

		if (_BF_PLATFORM=='JOOMLA1.0'){
			if ($cache->clean()){
				/* Set an Alert for feedback */
				$this->setxAJAXAlert('confirm',bfText::_('Joomla Cache Flushed Successfully').'!');
			} else {
				/* Set an Alert for feedback */
				$this->setxAJAXAlert('error',bfText::_('Check permissions on Joomla Cache folder!').'!');

			}
		}
		$this->setLayout('none');
	}

	function xcustomise(){
		$this->setView('customise');
	}

	function xhelp(){
		$this->setView('help');
	}
	function xxhelp(){
		$this->setView('help');
	}

	/**
	 * Just set the lastmodel and the view to be home.php
	 *
	 */
	function xhome($modelname=''){
		// Just set the lastModel
		$this->session->set('lastModel',$modelname,'default');
		$this->setView('home');
	}

	/**
	 * Get a table row to view
	 *
	 */
	function view() {
		$modelname = $this->session->get('lastModel','','default');
		$cid = bfRequest::getVar('cid');
		$model=$this->getModel($modelname);
		$model->get($cid[0]);
	}

	/**
	 * Used in modal popup to select the category
	 * and return it by javascript to the parentid hidden field in
	 * the parent document.
	 *
	 */
	function selectparentcategory(){
		$cats =& $this->getModel('category');

		$current_id = bfRequest::getVar('id',0,'request','int');
		if ($current_id === 0){
			$this->_registry->set('current_id',"0");
			$this->_registry->set('current_name','Root');
			$cats->getRootCategorys();

		} else {
			$cats->get((int) $current_id );
			$this->_registry->set('current_id',$current_id);
			$this->_registry->set('current_name',$cats->title);
			$this->_registry->set('current_parentid',$cats->parentid);
			$cats->getAll('parentid = "'.$current_id.'"', false);
		}
	}

	/**
	 * Used in modal popup to select the category
	 * and return it by javascript to the parentid hidden field in
	 * the parent document.
	 *
	 */
	function selectcategory(){
		define('_POPUP',1 );
		$cats =& $this->getModel('category');

		$current_id = bfRequest::getVar('id',0,'request','int');
		if ($current_id === 0){
			$this->_registry->set('current_id',"0");
			$this->_registry->set('current_name','Root');
			$cats->getRootCategorys();

		} else {
			$cats->get((int) $current_id );
			$this->_registry->set('current_id',$current_id);
			$this->_registry->set('current_name',$cats->title);
			$this->_registry->set('current_parentid',$cats->parentid);
			$cats->getAll('parentid = "'.$current_id.'"', false);
		}
	}

	/**
	 * Generate a brand new table row.
	 * View it in the edit_<modelname>.php view.
	 * This method shoud be called new() but that's illegal :-((
	 *
	 */
	function xadd() { // new but you can't have a method called new()
		/* @var $fortune Fortune */
		$this->log->log("xadd");
		$modelname = $this->session->get('lastModel','','default');
		$model=$this->getModel($modelname);

		/* this is important but I cant remember why - nuked until I work out why */
//		$model->get(0);

		$this->setView('edit_'.$modelname);
	}

	/**
	 * Save an updated row and redirect to the edit page
	 *
	 */
	function xapply($args) {
		/* @var $fortune Fortune */
		$this->log->log("xapply");
		$modelname = $this->session->get('lastModel','','default');

		$id = $this->getArgument('id');
		$row =& $this->getModel($modelname);

		/* Get the incoming args from xajax and pass to bfModel */
		$row->setArgs($args);

		/* Save the details */
		$row->saveDetails();

		$row->get($id);

		/* Neat trick. Don't redirect just specify the right view */
		/* Set the view to display */
		$this->setView('edit_'.$modelname);

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',' '.bfText::_('item successfully updated').'!');
		
	}

	function xsavefile(){
		$args = $this->getAllArguments();
		@chmod($args['fileName'], 0777);
		if (is_writeable($args['fileName'])){
			if ($fp = fopen($args['fileName'], 'wb')) {
				fwrite($fp, $args['fileContents']);
				fclose($fp);
			}
			/* Set an Alert for feedback */
			$this->setxAJAXAlert('confirm',' '.bfText::_('file saved').'!');
		} else {
			/* Set an Alert for feedback */
			$this->setxAJAXAlert('error',' '.bfText::_('Error, please check permissions').'!');
		}

		$this->setView('css');
	}

	/**
	 * Save an updated table row and redirect to the index page
	 *
	 */
	function xsave($args) {

		/* @var $fortune Fortune */
		$this->log->log("xsave");

		/* get our last accessed model */
		$model = $this->session->get('lastModel','default');
		$row = $this->getModel($model);

		/* Get the incoming args from xajax and pass to bfModel */
		$row->setArgs($args);

		/* log */
		$this->log->log("saveDetails args:");
		$this->log->log($args);



		/* Save the details */
		/* Look in here for the bind! */
		$row->saveDetails();

		if ($this->_registry->get('errormsg')){
			$this->setxAJAXAlert('error',$this->_registry->get('errormsg').'!');
		} else {
			/* Set an Alert for feedback */
			$this->setxAJAXAlert('confirm',''.bfText::_('item successfully updated').'!');
		}

		$row->updateOrder();

		/* set our edit id to null so we dont get the edit details if we go to Add New view */
		//		$this->session->set('editid',null,'default');

		$view= $this->session->get('returnto' , 'home' );
		$xview = 'x'.$view;

		/* Set the task/view to redirect to */
		$this->_redirect($xview);

	}

	/**
	 * I save the order when the user has manually set the ordering numbers
	 * If you need to apply a WHERE condition then duoplicate this function in the component
	 *
	 */
	function xsaveOrder(){

		/* Get the arguments from the xajax post */
		$cids 		= $this->getArgument('cids');
		$orderings 	= $this->getArgument('ordering');
		$cid		= explode('|',$cids);
		$order		= explode('|',$orderings);
		$total		= count($cid);
		$conditions	= array ();

		/* get our last accessed model */
		$modelname = $this->session->get('lastModel','default');

		/* get our row */
		$row = $this->getModel($modelname);

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			$row->get( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					bfError::raiseError( 500, $row->getErrorMsg() );
					return false;
				}
				$conditions[] = array ($row->id);
			}
		}

		// execute updateOrder for each group
		foreach ($conditions as $cond){
			$row->load($cond[0]);
			$row->reorder();
		}

		$view = $this->session->get('returnto' , 'home' );
		$xview = 'x'.$view;

		/* Set the task/view to redirect to */
		$this->_redirect($xview);
	}


	/**
	 * Saves the new configuration to a file and returns home
	 */
	function xsaveConfig( $args ){
		global $mainframe;
		bfSecurity::checkPermissions('Admin.EditConfiguration',' save configuration');

		/* Load existing configuration file */
		$config = new bfConfig($mainframe->get('component'));

		/* Bind the args and save the components configuration file*/
		$config->saveConfigFile($args);

		/* reload config vars */
		$config->reload();

		/* Where to return to on filter or limit */
		/* Always return home after config save */
		$view = 'xcustomise';

		/* Set the task/view to redirect to */
		$this->_redirect($view);

		/* Set a JS Alert for feedback */
		$this->setxAJAXAlert('confirm', bfText::_('Configuration File Saved!'));
	}

	/**
	 * Saves the new configuration to a file and returns home
	 *
	 */
	function xapplyConfig($args){
		global $mainframe;
		bfSecurity::checkPermissions('Admin.EditConfiguration',' apply configuration');

		/* Get the arguments from the xajax post */

		/* Load existing configuration file */
		$config = new bfConfig($mainframe->get('component'));

		/* Bind the args and save the components configuration file*/
		$config->saveConfigFile($args);

		/* Set the view to redirect to */
		$this->setLayout('none');

		/* Set a JS Alert for feedback */
		$this->setxAJAXAlert('confirm',bfText::_('Changes Saved').'!');
	}

	/**
	 * Get a database row for editing
	 *
	 */
	function xedit($args) {
		/* @var $fortune bfModel */

		/* If we are coming into this function from an apply function */
		$cid = @$args['cid'][0] ? $args['cid'][0] : '';

		if (!$cid) {
			$cids = $this->getArgument('cid');
			if (is_array($cids)) $cid = $cids[0];
		}

		if (!$cid){
			$cid = $this->getArgument(1);
		}

		$l =& bfLog::getInstance();
		$l->log('editing id = ' . $cid);
		/* get our last accessed model */
		$modelname = $this->session->get('lastModel','','default');

		$row =& $this->getModel($modelname);

		/* Get this row from the table */
		$row->get($cid);

		/* Checkout */
		$row->checkOut();

		/* Set the view to display */
		$this->setView('edit_'.$modelname);
	}

	/**
	 * Generic edit file
	 *
	 */
	function xeditfile(){
		$args = $this->getAllArguments();
		$fileName = $args[1];
		$fileContents = @file_get_contents($fileName);
		$this->_registry->setValue('fileDetails', array('fileName'=>$fileName, 'fileContents'=>$fileContents));
		$this->setView('editfile');
	}

	function xresethits(){
		$id=$this->getArgument('id');
		$model=$this->getArgument('model');
		$row =& $this->getModel($model);
		$row->resetHits($id);
		$this->setLayout('text');
		$this->setXajaxTarget('hits'.$id);
		$this->setXajaxAction('addassign');
		$this->setMessage('0');
		$this->xajax->addscript('xajax.$(\'hits\').value = \'0\';  ');
		$this->setxAJAXAlert('info',bfText::_('Hits reset to zero...'));
	}

	/**
	 * Mark a row as published
	 * This function is called from the click on the
	 * small state icon in index view
	 */
	function xtogglepublish() {

		$id=$this->getArgument(1);
		$model=$this->getArgument(3);

		if (!$model){
			/* get our last accessed model */
			$model = $this->session->get('lastModel','','default');
		}
		$row=$this->getModel($model);

		/* set this rows published state */
		$hasPublished = array_key_exists( 'published', $row );
		if ($hasPublished){
			$row->publish($id);
		}

		/* Set layout to be a single xAJAX request: text */
		$this->setLayout('text');

		/* Set the innerHTML of the target div */
		$this->setMessage(bfHTML::publishInformationDiv($id));
		$this->setXajaxAction('addassign');
		$this->setXajaxTarget('pub'.$id);

		/* needed to fool back to form_element model after
		toggle of sub-index of validations */
		if ($model=='element_validation'){
			$fool =& $this->getModel('form_element');
		}
	}

	/**
	 * Mark a row as enabled
	 * This function is called from the click on the
	 * small state icon in index view
	 */
	function xtoggleenable() {

		$id=$this->getArgument(1);
		$model=$this->getArgument(3);

		if (!$model){
			/* get our last accessed model */
			$model = $this->session->get('lastModel','','default');
		}

		$row = $this->getModel($model);
		$row->load($id);

		if ($row->enabled == 0){
			$row->enabled = 1;
		}else {
			$row->enabled = 0;
		}
		$row->store();

		/* Set layout to be a single xAJAX request: text */
		$this->setLayout('text');

		/* Set the innerHTML of the target div */
		if ($row->enabled == 1){
			$this->setMessage(bfHTML::publishInformationDiv($id, 'enabled'));
		} else {
			$this->setMessage(bfHTML::unpublishInformationDiv($id, 'enabled'));
		}
		$this->setXajaxAction('addassign');
		$this->setXajaxTarget('pub'.$id);
	}

	/**
	 * Mark a row as unpublished
	 * This function is called from the click on the small state
	 * icon in index view
	 */
	function xtoggleunpublish($args) {

		$args = $this->getAllArguments();

		$id=$this->getArgument(1);
		$model=$this->getArgument(3);

		$log =& bfLog::getInstance();
		$log->log('~~~~~~~~~~~~~~ ::' . $args);
		if (!$model){
			/* get our last accessed model */
			$model = $this->session->get('lastModel','','default');
		}
		$row=$this->getModel($model);
		$hasPublished = array_key_exists( 'published', $row );
		if ($hasPublished){
			$row->unpublish($id);
		}
		$this->setLayout('text');
		$this->setMessage(bfHTML::unPublishInformationDiv($id));
		$this->setXajaxAction('addassign');
		$this->setXajaxTarget('pub'.$id);
	}

	/**
	 * Publish a set of table rows
	 *  This function is called from the click on the TOOLBAR ICON in index view
	 */
	function xpublish($args) {
		/* Get the arguments from the xajax post */
		//cids is always an array
		$cids = $args['cid'];
		/* get our last accessed model */
		$model = $this->session->get('lastModel','','default');

		$row=$this->getModel($model);
		$row->publish($cids);

		/* Where to return to on filter or limit */
		$view = 'x'.$this->session->get('returnto');

		/* Set the task/view to redirect to */
		$this->_redirect($view);

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',sizeof($cids).' '.bfText::_('item(s) successfully published').'!');
	}

	/**
	 * Unpublish a set of table rows
	 *  This function is called from the click on the TOOLBAR ICON in index view
	 */
	function xunpublish($args) {
		//cids is always an array
		$cids = $args['cid'];
		/* get our last accessed model */
		$model = $this->session->get('lastModel','','default');

		$row=$this->getModel($model);
		$row->unpublish($cids);

		/* Where to return to on filter or limit */
		$view = 'x'.$this->session->get('returnto');

		/* Set the task/view to redirect to */
		$this->_redirect($view);

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',sizeof($cids).' '.bfText::_('item(s) successfully unpublished').'!');
	}

	/**
	 * Delete a set of table rows and return to the index page
	 *
	 */
	function xremove($args) {
		/* get our last accessed model */
		$model = $this->session->get('lastModel','','default');

		$row=$this->getModel($model);
		$cids = $args['cid'];
		foreach($cids as $id) {
			$row->delete($id);
		}

		/* Where to return to on filter or limit */
		$view = 'x'.$this->session->get('returnto');

		/* Set the task/view to redirect to */
		$this->_redirect($view);

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',sizeof($cids).' '.bfText::_('item(s) successfully removed').'!');
	}

	/**
	 * change the row's access mode.
	 *
	 */
	function xtogglePermissionACL(){

		$id=$this->getArgument(1);
		$next=$this->getArgument(2);
		$model=$this->getArgument(3);

		$l =& bfLog::getInstance();
		$l->log('~~~~~~~~~~~~~');
		$l->log($model);
		if (!$model){
			/* get our last accessed model */
			$model = $this->session->get('lastModel','','default');
		}
		$row=$this->getModel($model);

		$row->toggleAccess($id,$next);
		$this->setLayout('text');
		$this->setMessage(bfHTML::drawPermissionLinks($id, $next, 1));
		$this->setXajaxAction('addassign');
		$this->setXajaxTarget('xaccess'.$id);

		/* needed to fool back to form_element model after toggle of sub-index of validations */
		if ($model=='element_validation'){
			$fool =& $this->getModel('form_element');
		}
	}
	
	
	/**
	 * change the row's access mode.
	 *
	 */
	function xtoggleAccess(){

		$id=$this->getArgument(1);
		$next=$this->getArgument(2);
		$model=$this->getArgument(3);

		$l =& bfLog::getInstance();
		$l->log('~~~~~~~~~~~~~');
		$l->log($model);
		if (!$model){
			/* get our last accessed model */
			$model = $this->session->get('lastModel','','default');
		}
		$row=$this->getModel($model);

		$row->toggleAccess($id,$next);
		$this->setLayout('text');
		$this->setMessage(bfHTML::drawAccessLinks($id, $next, 1));
		$this->setXajaxAction('addassign');
		$this->setXajaxTarget('xaccess'.$id);

		/* needed to fool back to form_element model after toggle of sub-index of validations */
		if ($model=='element_validation'){
			$fool =& $this->getModel('form_element');
		}
	}

	/**
	 * Enter description here...
	 *
	 */
	function xorder(){
		$direction = $this->getArgument(1);
		$id = $this->getArgument(2);
		$where = $this->getArgument(3);

		/* get our last accessed model */
		$model = $this->session->get('lastModel','','default');

		$model->order($direction,$id,$where);

	}

	/**
	 * Archive a set of table rows. Redirect to view.
	 *
	 */
	function xarchive($args){
		$cids=$args['cid'];

		/* get our last accessed model */
		$model = $this->session->get('lastModel','','default');

		$row=$this->getModel($model);

		$row->toggleArchive($cids, -1);

		/* Where to return to on after archive */
		$view = 'x'.$this->session->get('returnto');


		/* Set the task/view to redirect to */
		$this->_redirect($view);

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',sizeof($cids).' '.bfText::_('item(s) successfully archived').'!');
	}

	/**
	 * Unarchive a set of table rows. Redirect to view.
	 *
	 */
	function xunarchive($args){
		$cids=$args['cid'];
		/* get our last accessed model */
		$model = $this->session->get('lastModel','','default');

		$row=$this->getModel($model);
		$row->toggleArchive($cids, 0);

		/* Where to return to on after unarchive */
		$view = 'x'.$this->session->get('returnto');

		/* Set the task/view to redirect to */
		$this->_redirect($view);

		/* Set an Alert for feedback */
		$this->setxAJAXAlert('confirm',sizeof($cids).' '.bfText::_('item(s) successfully unarchived').'!');

	}

	/**
	 * Get the items for the linting page. Pagination is handled
	 * automatically by the bfModel.
	 *
	 */
	function xindex($modelname) {
		$this->log->log("xindex for model $modelname");
		$model = $this->getModel($modelname);
		$model->getAll();
		/* Where to return to on filter or limit */
		$this->session->returnto( $this->getView() );
	}

	function xinstallmambot(){
		global $mainframe;
		$mambotName = $this->getArgument(1);
		$this->log->log("installing mambot $mambotName");
		$this->setLayout('none');

		/* determin the mmabot type */
		$parts = explode('.',$mambotName);
		$mambotType = $parts[0];
		array_shift($parts);

		/* reconstruct the file name */
		$mambotFileName = $mambotName;

		/* copy the files */
		$filename = $mambotFileName.'.php';
		$source = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'addons' . DS . 'plugins' . DS . $filename;
		$desc = JPATH_ROOT . DS . _PLUGIN_DIR_NAME . DS .$mambotType . DS . $filename;

		$txt = 'FROM '.$source;
		$this->log->log($txt);
		$txt = 'TO '.$desc;
		$this->log->log($txt);

		if (@copy($source,$desc)){
			/* db */
			$sql = "INSERT INTO `#__"._PLUGIN_DIR_NAME."` VALUES ('', '".$mambotName." (Used By ".$mainframe->get('component')." )', '".$mambotName."', '".$mambotType."', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '')";
			$db =& bfCompat::getDBO();
			$db->setQuery($sql);
			$db->query();
			$str = str_replace('.','_',$mambotName);
			$this->xajax->addscript("jQuery('td#status-".$str."').html('Installed').removeClass('red').addClass('green');");
			$buttons = new bfButtons('left',false);
			$buttons->addButton('cancel', 	'\'xuninstallmambot'.'\', \''.$mambotName.'\'', 'Uninstall');
			$tog = $buttons->getHTML();
			$this->xajax->addassign("toggle-".$str,'innerHTML', $tog);
		} else {
			$this->xajax->addalert(bfText::_('Error installing - check file permissions!'));
		}

		/* copy the XML file - if exists */
		$filename = $mambotFileName.'.x';
		$source = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'addons' . DS . 'plugins' . DS . $filename;
		$desc = JPATH_ROOT . DS . _PLUGIN_DIR_NAME . DS .$mambotType . DS . $filename . 'ml';

		$txt = 'FROM '.$source;
		$this->log->log($txt);
		$txt = 'TO '.$desc;
		$this->log->log($txt);

		if (@!copy($source,$desc)){
			$this->xajax->addalert(bfText::_('Error installing - check file permissions!'));
		}




	}

	function xuninstallmambot(){
		$mambotName = $this->getArgument(1);
		$this->log->log("uninstalling mambot $mambotName");
		$this->setLayout('none');

		/* determin the mmabot type */
		$parts = explode('.',$mambotName);
		$mambotType = $parts[0];
		array_shift($parts);

		/* reconstruct the file name */
		$mambotFileName = $mambotName;

		/* delete the files */
		$filename = $mambotFileName.'.php';
		$desc = JPATH_ROOT . DS . _PLUGIN_DIR_NAME . DS .$mambotType . DS . $filename;

		/* logging */
		$txt = 'removing '.$desc;
		$this->log->log($txt);

		if (@unlink($desc)){
			/* remove entry from the plugins table */
			$sql = "DELETE FROM `#__"._PLUGIN_DIR_NAME."` WHERE element = '".$mambotName."'";
			$db =& bfCompat::getDBO();
			$db->setQuery($sql);
			$db->query();
			$str = str_replace('.','_',$mambotName);
			$this->xajax->addscript("jQuery('td#status-".$str."').html('".bfText::_('Uninstalled')."').removeClass('green').addClass('red');");
			$buttons = new bfButtons('left',false);
			$buttons->addButton('ok', 	'\'xinstallmambot'.'\', \''.$mambotName.'\'', bfText::_('Install'));
			$tog = $buttons->getHTML();
			$this->xajax->addassign("toggle-".$str,'innerHTML', $tog);
		} else {
			$this->xajax->addalert(bfText::_('Error uninstalling! - check file permissions'));
		}

		/* delete the cml files */
		$filename = $mambotFileName.'.xml';
		$desc = JPATH_ROOT . DS . _PLUGIN_DIR_NAME . DS .$mambotType . DS . $filename;

		/* logging */
		$txt = 'removing '.$desc;
		$this->log->log($txt);

		if (@!unlink($desc)){
			$this->xajax->addalert(bfText::_('Error uninstalling! - check file permissions'));
		}


	}

	function xinstallmodule(){
		global $mainframe;
		$moduleName = $this->getArgument(1);
		$this->log->log("installing mambot $moduleName");
		$this->setLayout('none');

		/* copy the files */
		$filename = $moduleName.'.php';
		$source = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'addons' . DS . 'modules' . DS . $filename;
		$desc = JPATH_ROOT . DS . 'modules' . DS . $filename;

		$txt = 'FROM '.$source;
		$this->log->log($txt);
		$txt = 'TO '.$desc;
		$this->log->log($txt);

		if (@copy($source,$desc)){
			/* copy xml too */
			$source = bfCompat::getAbsolutePath() . DS . 'components' . DS . $mainframe->get('component') . DS . 'addons' . DS . 'modules' . DS . $moduleName.'.x';
			$desc = JPATH_ROOT . DS . 'modules' . DS . $moduleName.'.xml';
			@copy($source,$desc);

			/* db */
			$sql = "INSERT INTO `#__modules` VALUES ('', '".$moduleName." (Provided by ".$mainframe->get('component').")', '', 0, 'left', 0, '0000-00-00 00:00:00', 1, '".$moduleName."', 0, 0, 1, '', 0, 0);";
			$db =& bfCompat::getDBO();
			$db->setQuery($sql);
			$db->query();
			$str = str_replace('.','_',$moduleName);
			$this->xajax->addscript("jQuery('td#status-".$str."').html('".bfText::_('Installed')."').removeClass('red').addClass('green');");

			$buttons = new bfButtons('left',false);
			$buttons->addButton('cancel', 	'\'xuninstallmodule'.'\', \''.$moduleName.'\'', bfText::_('Uninstall'));
			$tog = $buttons->getHTML();
			$this->xajax->addassign("toggle-".$str,'innerHTML', $tog);
		} else {
			$this->xajax->addalert(bfText::_('Error installing - check file permissions!'));
		}

	}
	function xuninstallmodule(){
		$moduleName = $this->getArgument(1);
		$this->log->log("uninstalling mambot $moduleName");
		$this->setLayout('none');

		/* copy the files */
		$filename = $moduleName.'.php';
		$desc = JPATH_ROOT . DS . 'modules' . DS . $filename;

		/* logging */
		$txt = 'removing '.$desc;
		$this->log->log($txt);

		if (@unlink($desc)){
			/* get rid of xml too */
			$filename = $moduleName.'.xml';
			$desc = JPATH_ROOT . DS . 'modules' . DS . $filename;

			/* remove entry from the plugins table */
			$sql = "DELETE FROM `#__modules` WHERE module = '".$moduleName."'";
			$db =& bfCompat::getDBO();
			$db->setQuery($sql);
			$db->query();
			$this->log->log($db->getErrorMsg());
			$str = str_replace('.','_',$moduleName);
			$this->xajax->addscript("jQuery('td#status-".$str."').html('".bfText::_('Uninstalled')."').removeClass('green').addClass('red');");
			$this->xajax->addscript(
			"jQuery('td#toggle-".$str."').html('<a href=\"javascript:void(0);\" onclick=\"bfHandler(\'xinstallmodule\',\'".$moduleName."\');\" \">"
			.bfText::_('Click to install') ."</a>');");

			$buttons = new bfButtons('left',false);
			$buttons->addButton('ok', 	'\'xinstallmodule'.'\', \''.$moduleName.'\'', bfText::_('Install'));
			$tog = $buttons->getHTML();
			$this->xajax->addassign("toggle-".$str,'innerHTML', $tog);


		} else {
			$this->xajax->addalert(bfText::_('Error uninstalling! - check file permissions'));
		}
	}

	/**
	* Moves the order of a record up or down
	*
	*/
	function xorderItem() {
		/* Get the arguments from the xajax post */
		$args = $this->getAllArguments();

		/* get the directrion */
		$direction = $args[2];

		/* get the item we are moving */
		$cid = $args[1];

		/* get our last accessed model */
		$modelname = $this->session->get('lastModel','default');

		/* get our row */
		$row =& $this->getModel($modelname);

		$row->load( (int) $cid );
		
		$where = null;
		$row->move($direction, $where);

		$view= $this->session->get('returnto' , 'home' );
		$xview = 'x'.$view;

		/* Set the task/view to redirect to */
		$this->_redirect($xview);
	}
}
?>
