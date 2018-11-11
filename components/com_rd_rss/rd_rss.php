<?php
/**
 * 
 * @version $Id: rd_rss.php,v 1.3 2005/12/21 12:28:55 deutz Exp $
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

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

// load feed creator class
require_once( $mosConfig_absolute_path .'/includes/feedcreator.class.php' );
// load component class
require_once( $mainframe->getPath('class', 'com_rd_rss' ));

$id = mosGetParam( $_REQUEST, 'id', '0' );

switch ( $task ) {
	case 'live_bookmark':
		rdRss::feed($id, false );
		break;

	default:
		rdRss::feed($id, true );
		break;
}

class rdRss {

	function getParameters ($id) {
		global $database,$mosConfig_offset, $mosConfig_live_site;
		
		$feed = new rdRssData( $database );
		$feed->load( $id );
		$params = new mosParameters( $feed->params );

		$params->id =$id;
		$params->catids = $feed->catids;
		$params->published = $feed->published;
		if (method_exists($database,"getNullDate")) {		
			$params->nullDate 	= $database->getNullDate();
		} else {
			$params->nullDate = '0000-00-00 00:00:00';
		}		
		$params->now 		= date( 'Y-m-d H:i:s', time() + $mosConfig_offset * 60 * 60 );
		$iso 		= split( '=', _ISO );
		
		// parameter intilization
		$params->date    		= date( 'r' );
		$params->year    		= date( 'Y' );
		$params->encoding    	= $iso[1];
		$params->link    		= htmlspecialchars( $mosConfig_live_site );
		$params->cache    		= $params->def( 'cache', 1 );
		$params->cache_time    	= $params->def( 'cache_time', 3600 );
		$params->count   		= $params->def( 'count', 5 );
		$params->orderby    	= $params->def( 'orderby', '' );
		$params->title    		= $params->def( 'title', 'Joomla! powered Site' );
		$params->description   	= $params->def( 'description', 'Joomla! site syndication' );
		$params->image_file   	= $params->def( 'image_file', 'joomla_rss.png' );
		if ( $params->image_file  == -1 ) {
			$params->image	= NULL;
		} else{
			$params->image	= $mosConfig_live_site .'/images/M_images/'. $params->image_file;
		}
		$params->image_alt    	= $params->def( 'image_alt', 'Powered by Joomla!' );
		$params->limit_text    	= $params->def( 'limit_text', 1 );
		$params->text_length    	= $params->def( 'text_length', 20 );
		// get feed type from url
		$params->feed    		= mosGetParam( $_GET, 'feed', 'RSS2.0' );
		// live bookmarks
		$params->live_bookmark   	= $params->def( 'live_bookmark', '' );

		return $params;
	}	

	function getData ($params, $frontpage=0) {
		global $database;
		
		// Determine ordering for sql
		switch ( strtolower( $params->orderby ) ) {
			case 'date':
				$orderby = 'a.created';
				break;
	
			case 'rdate':
				$orderby = 'a.created DESC';
				break;
	
			case 'alpha':
				$orderby = 'a.title';
				break;
	
			case 'ralpha':
				$orderby = 'a.title DESC';
				break;
	
			case 'hits':
				$orderby = 'a.hits DESC';
				break;
	
			case 'rhits':
				$orderby = 'a.hits ASC';
				break;

			case 'front':
				$orderby = 'f.ordering';
				break;
	
			default:
				if ($frontpage) {
					$orderby = 'f.ordering';
				} else {	
					$orderby = 'a.created';
				}	
				break;
		}
	
		$nullDate = $params->nullDate;
		$now = $params->now;
		$and 		= '';
		
		if ($frontpage) {
			$join 		= "\n INNER JOIN #__content_frontpage AS f ON f.content_id = a.id";
		} else {	
			if ($params->catids) {
				$join = "\n INNER JOIN #__categories AS c ON c.id = a.catid";
				$and  = "\n AND c.id in (".$params->catids.")";
			}	
		}	
		// query of content items
		$query = "SELECT a.*, u.name AS author, u.usertype, UNIX_TIMESTAMP( a.created ) AS created_ts"
		. "\n FROM #__content AS a"
		. $join
		. "\n LEFT JOIN #__users AS u ON u.id = a.created_by"
		. "\n WHERE a.state = 1"
		. $and
		. "\n AND a.access = 0"
		. "\n AND ( publish_up = '$nullDate' OR publish_up <= '$now' )"
		. "\n AND ( publish_down = '$nullDate' OR publish_down >= '$now' )"
		. "\n ORDER BY $orderby"
		. ( $params->count ? "\n LIMIT ". $params->count : '' )
		;
		$database->setQuery( $query );
		//echo $database->getQuery( );
		$rows = $database->loadObjectList();
		
		return $rows;

	}
	
	
	function feed( $id, $showFeed ) {
		global $database, $mainframe;
		global $mosConfig_live_site, $mosConfig_offset, $mosConfig_absolute_path;
		
		$params = rdRss::getParameters($id);
		//print_r($params);
		// set filename for live bookmarks feed
		if ( !$showFeed & $params->live_bookmark ) {
			// standard bookmark filename
			$params->file = $mosConfig_absolute_path .'/cache/'. $params->live_bookmark . "_" . $id;
		} else {
		// set filename for rss feeds
			$params->file = strtolower( str_replace( '.', '', $params->feed ) );
			$params->file = $mosConfig_absolute_path .'/cache/'. $params->file. "_" .$id .'.xml';
		}
	
		// load feed creator class
		$rss 	= new UniversalFeedCreator();
		// load image creator class
		$image 	= new FeedImage();
	
		// loads cache file
		if ( $showFeed && $params->cache ) {
			$rss->useCached( $params->feed, $params->file, $params->cache_time );
		}
	
		$rss->title 			= $params->title;
		$rss->description 		= $params->description;
		$rss->link 				= $params->link;
		$rss->syndicationURL 	= $params->link;
		$rss->cssStyleSheet 	= NULL;
		$rss->encoding 			= $params->encoding;
	
		if ( $params->image ) {
			$image->url 		= $params->image;
			$image->link 		= $params->link;
			$image->title 		= $params->image_alt;
			$image->description	= $params->description;
			// loads image info into rss array
			$rss->image 		= $image;
		}
		if ($id == '1') {$fp=1;} else {$fp=0;} 
		$rows = rdRss::getData($params,$fp);	
		
		if (count($rows) && $params->published) {
			foreach ( $rows as $row ) {
				// title for particular item
				$item_title = htmlspecialchars( $row->title );
				$item_title = html_entity_decode( $item_title );
		
				// url link to article
				// & used instead of &amp; as this is converted by feed creator
				$item_link = $mosConfig_live_site .'/index.php?option=com_content&task=view&id='. $row->id .'&Itemid='. $mainframe->getItemid( $row->id );
		  		$item_link = sefRelToAbs( $item_link );
		
				// removes all formating from the intro text for the description text
				$item_description = $row->introtext;
				$item_description = mosHTML::cleanText( $item_description );
				$item_description = html_entity_decode( $item_description );
				if ( $params->limit_text ) {
					if ( $params->text_length ) {
						// limits description text to x words
						$item_description_array = split( ' ', $item_description );
						$count = count( $item_description_array );
						if ( $count > $params->text_length ) {
							$item_description = '';
							for ( $a = 0; $a < $params->text_length; $a++ ) {
								$item_description .= $item_description_array[$a]. ' ';
							}
							$item_description = trim( $item_description );
							$item_description .= '...';
						}
					} else  {
						// do not include description when text_length = 0
						$item_description = NULL;
					}
				}
		
				// load individual item creator class
				$item = new FeedItem();
				// item info
				$item->title 		= $item_title;
				$item->link 		= $item_link;
				$item->description 	= $item_description;
				$item->source 		= $params->link;
				$item->date			= date( 'r', $row->created_ts );
		
				// loads item info into rss array
				$rss->addItem( $item );
			}
		}
		//save feed file
		$rss->saveFeed( $params->feed, $params->file, $showFeed );
	}
}
?>