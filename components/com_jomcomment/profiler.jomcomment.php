<?php

/**
 * Jom Comment 
 * @package JomComment
 * @copyright (C) 2006 by Azrul Rahim - All rights reserved!
 * @license Copyrighted Commercial Software
 **/

# Don't allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

/**
 * Our very own profiler class
 */ 
class JomProfiler extends mosProfiler 
{
	var $_html = "";
	var $prefix; 	# we need this for 1.5
	var $start;		# we need this for 1.5

	/**
	 * Put a marker at current execution point
	 */	 	
	function mark($label) 
	{
		$this->_html .= sprintf("\n<div class=\"profiler\">$this->prefix %.3f $label</div>", $this->getmicrotime() - $this->start);
	}

	/**
	 * Attach any notes, if necessary
	 */	 	
	function addDebugNote($note) 
	{
		$this->_html .= sprintf("\n<div class=\"profiler\">%s</div>", $note);
	}

	/**
	 * Return the profiler output, along with number of queries
	 */	 	
	function getHTML() 
	{
		global $database;

		$this->_html .= $database->_ticker . ' queries executed';
		return $this->_html;
	}
}
