<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: layout.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class Layout extends bfModel {

	/**
     * @var
     * I do lots of stuff with direct access to tables so need to
     * know when I'm in test mode.
     */
	var $_mode;
	var $_nometa = true;
	/**
     * I call the PHP5 Constructor
     *
     */
	function Layout() {
		$this->__construct();
	}

	/**
	 * PHP5 Constructor
	 * I set up the table name, the ORM and the search fields
	 */
	function __construct() {
		global $mainframe;
		parent::__construct('#__'.$mainframe->get('component_shortname').'_layouts');

		$this->_search_fields = array('id','title','html');

		// Am I in test mode?
		$session =& bfSession::getInstance();
		$this->_mode = $session->get('mode');
	}

	function get($id){
		parent::get($id);
		$filename = bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'view'. DS.'user_templates' . DS . md5($this->id) . '.php';
		$this->filename = $filename;
		if (file_exists($filename)){
			$this->html = file_get_contents($filename);
		} else {
			$this->html = 'Could not locate: '.$filename;
		}
	}

	function saveDetails(){
		global $mainframe;

		/* save to database */
		parent::saveDetails();

		/* get raw html from args */
		$registry =& bfRegistry::getInstance($mainframe->get('component'));
		$args = $registry->getValue('args');

		/* save to file */
		$temp_file = bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'view'. DS.'user_templates' . DS . md5($this->id) . '.php';

		if (!$this->filename){
			$this->filename = md5($this->id);
			$this->store();
		}

		$this->html = $args[1]['html'];

		/* check file exists */
		if (!file_exists($temp_file)){
			if (@ !touch($temp_file)){
				bfError::raiseError('500',bfText::_('Could not create file ').$temp_file);
				return;
			}
		}

		/* write template file */
		$fd = fopen($temp_file, 'w');
		if ( false === $fd ) {
			bfError::raiseError('500',bfText::_('Could not save ').$temp_file);
			return ;
		}
		fputs($fd, $this->html);
		fclose($fd);
		@ chmod($temp_file, 0644);

	}

	function delete($id){

		$this->load($id);
		if ($this->framework=='1'){
			bfError::raiseError('500',bfText::_('Could not remove layout, it is used by the framework!'));
			return false;
		}

		parent::delete($id);

		/* delete file */
		$temp_file = bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'view'. DS.'user_templates' . DS . md5($this->id) . '.php';

		/* check file exists */
		if (file_exists($temp_file)){
			if ( !unlink($temp_file)){
				bfError::raiseError('500',bfText::_('Could not delete file ').$temp_file);
				return;
			}
		}
	}

	function getLayoutOptions($selected=null, $tagname='template',$appliesto = null){
		if ($appliesto!==null){
			$where = ' appliesto = \''.$appliesto.'\'';
		} else {
			$where = null;
		}
		$this->getAll($where,false,'title ASC');
		$options = array();
		$options[] = bfHTML::makeOption('','-- Use Global Preference --');
		foreach ($this->rows as $row){
			$options[] = bfHTML::makeOption($row->id,$row->title);
		}
		return bfHTML::selectList2($options,$tagname,' class="flatinputbox"','value','text', $selected);
	}
}
?>
