<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: bfPagination.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

/**
* Page navigation support class
* @package Joomla
*/
class bfPageNav {
	/** @var int The record number to start dislpaying from */
	var $limitstart = null;
	/** @var int Number of rows to display per page */
	var $limit = null;
	/** @var int Total number of rows */
	var $total = null;

	function bfPageNav( $total, $limitstart, $limit ) {
		$this->total		= (int) $total;
		$this->limitstart	= (int) max( $limitstart, 0 );
		$this->limit		= (int) max( $limit, 0 );
	}
	/**
	* Returns the html limit # input box
	* @param string The basic link to include in the href
	* @return string
	*/
	function getLimitBox ( $link ) {
		$limits = array();
		for ($i=5; $i <= 30; $i+=5) {
			$limits[] = bfHTML::makeOption( "$i" );
		}
		$limits[] = bfHTML::makeOption( "50" );

		// build the html select list
		$link = $link ."&amp;limit=' + this.options[selectedIndex].value + '&amp;limitstart=". $this->limitstart;
		$link = bfCompat::sefRelToAbs( $link );
		return bfHTML::selectList2( $limits, 'limit', 'class="inputbox" size="1" onchange="document.location.href=\''. $link .'\';"', 'value', 'text', $this->limit );
	}
	/**
	* Writes the html limit # input box
	* @param string The basic link to include in the href
	*/
	function writeLimitBox ( $link ) {
		return bfPageNav::getLimitBox( $link );
	}
	/**
	* Writes the html for the pages counter, eg, Results 1-10 of x
	*/
	function writePagesCounter() {
		$txt = '';
		$from_result = $this->limitstart+1;
		if ($this->limitstart + $this->limit < $this->total) {
			$to_result = $this->limitstart + $this->limit;
		} else {
			$to_result = $this->total;
		}
		if ($this->total > 0) {
			$txt .= _PN_RESULTS." $from_result - $to_result "._PN_OF." $this->total";
		}
		return $to_result ? $txt : '';
	}

	/**
	* Writes the html for the leafs counter, eg, Page 1 of x
	*/
	function writeLeafsCounter() {
		$txt = '';
		$page = ceil( ($this->limitstart + 1) / $this->limit );
		if ($this->total > 0) {
			$total_pages = ceil( $this->total / $this->limit );
			$txt .= bfText::_('Page')." $page ".bfText::_('Of')." $total_pages";
		}
		return $txt;
	}

	/**
	* Writes the html links for pages, eg, previous, next, 1 2 3 ... x
	* @param string The basic link to include in the href
	*/
	function writePagesLinks( $link ) {
		$txt = '';

		$displayed_pages = 10;
		$total_pages = $this->limit ? ceil( $this->total / $this->limit ) : 0;
		$this_page = $this->limit ? ceil( ($this->limitstart+1) / $this->limit ) : 1;
		$start_loop = (floor(($this_page-1)/$displayed_pages))*$displayed_pages+1;
		if ($start_loop + $displayed_pages - 1 < $total_pages) {
			$stop_loop = $start_loop + $displayed_pages - 1;
		} else {
			$stop_loop = $total_pages;
		}

		$link .= '&amp;limit='. $this->limit;

		if (!defined( '_PN_LT' ) || !defined( '_PN_RT' ) ) {
			DEFINE('_PN_LT','&lt;');
			DEFINE('_PN_RT','&gt;');
		}

		$pnSpace = '';
		if (_PN_LT || _PN_RT) $pnSpace = "&nbsp;";

		if ($this_page > 1) {
			$page = ($this_page - 2) * $this->limit;
			$txt .= '<a href="'. sefRelToAbs( "$link&amp;limitstart=0" ) .'" class="pagenav" title="'. bfText::_('Start') .'"><<' . $pnSpace . bfText::_('Start') .'</a> ';
			$txt .= '<a href="'. sefRelToAbs( "$link&amp;limitstart=$page" ) .'" class="pagenav" title="'. bfText::_('Previous') .'"><'.  $pnSpace . bfText::_('Previous') .'</a> ';
		} else {
			$txt .= '<span class="pagenav">&lt;&lt;'. $pnSpace . bfText::_('Start') .'</span> ';
			$txt .= '<span class="pagenav">&lt;'.  $pnSpace . bfText::_('Previous') .'</span> ';
		}

		for ($i=$start_loop; $i <= $stop_loop; $i++) {
			$page = ($i - 1) * $this->limit;
			if ($i == $this_page) {
				$txt .= '<span class="pagenav">'. $i .'</span> ';
			} else {
				$txt .= '<a href="'. sefRelToAbs( $link .'&amp;limitstart='. $page ) .'" class="pagenav"><strong>'. $i .'</strong></a> ';
			}
		}

		if ($this_page < $total_pages) {
			$page = $this_page * $this->limit;
			$end_page = ($total_pages-1) * $this->limit;
			$txt .= '<a href="'. sefRelToAbs( $link .'&amp;limitstart='. $page ) .' " class="pagenav" title="'. bfText::_('Next') .'">'. bfText::_('Next') . $pnSpace .'></a> ';
			$txt .= '<a href="'. sefRelToAbs( $link .'&amp;limitstart='. $end_page ) .' " class="pagenav" title="'. bfText::_('End') .'">'. bfText::_('End') . $pnSpace . '>></a>';
		} else {
			$txt .= '<span class="pagenav">'. bfText::_('Next') . $pnSpace . '&gt;</span> ';
			$txt .= '<span class="pagenav">'. bfText::_('End') . $pnSpace .'&gt;&gt;</span>';
		}
		return $txt;
	}
}
?>