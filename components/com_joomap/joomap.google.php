<?php defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); ?>
<?php

	 /**
	 * Print "Google Sitemaps" list of the Joomap tree.
	 * Does not use "priority" or "changefreq".
	 * NOTE: When logged in, the tree will also contain private items!
	 * @author Daniel Grothe
	 * @see joomla.html.php
	 * @see joomla.google.php
	 * @package Joomap
	 */
	
	/** Wraps Google Sitemaps output */
	class JoomapGoogle {
		
		/** Convert sitemap tree to a Google Sitemaps list */
		function &getList( &$tree, &$exlink, $level = 0 ) {
			global $Itemid, $mosConfig_live_site;
			if( !$tree )
				return '';
			
			$out = '';
			$len_livesite = strlen($mosConfig_live_site);
			foreach($tree as $node) {
				$link = $node->link;
				switch( @$node->type ) {
					case 'separator':
						break;
					case 'url':
						if ( eregi( "index.php\?", $link ) ) {
							if ( strpos( 'Itemid=', $link ) === FALSE ) {
								$link .= '&amp;Itemid='.$node->id;
							}
						}
						break;
					default:
						$link .= '&amp;Itemid='.$node->id;
						break;
				}
				
				if( strcasecmp( substr( $link, 0, 5), 'http:' ) != 0 )
					$link = sefRelToAbs($link);									// apply SEF transformation

				if( strcasecmp( substr($link, 0, 5), 'http:' ) == 0				// ignore external and empty links
				 && strcasecmp( substr($link, 0, $len_livesite), $mosConfig_live_site ) == 0
				 && $node->browserNav != 3) {
				 	
					$out .= "<url>\n";
					$out .= " <loc>". $link ."</loc>\n";						// http://complete-url
					if( isset($node->modified) ) {
						$modified = date('Y-m-d\TH:i:s', $node->modified);		// ISO 8601 yyyy-mm-ddThh:mm:ss.sTZD
						$modified .= sprintf("%+03d:00", $GLOBALS['mosConfig_offset']);
						$out .= " <lastmod>". $modified ."</lastmod>\n";		
					}
		   			//$out .= " <changefreq>always</changefreq>";				// always, hourly, daily, weekly, monthly, yearly, never
			   		//$out .= " <priority>0.8</priority>";						// 0.0 - 1.0
					
		 			$out .= "</url>\n";
				}
				
				if( isset($node->tree) ) {
					$out .= JoomapGoogle::getList( $node->tree, $exlink, $level + 1 );
				}
			}
			return $out;
		}
		
		/** Print a Google Sitemaps representation of tree */
		function printTree( &$joomap, &$root ) {

			echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";
			
			$tmp = array();
			foreach( $root as $menu ) {											// concatenate all menu-trees
				foreach( $menu->tree as $node ) {
					$tmp[] = $node;
				}
			}
			echo JoomapGoogle::getList( $tmp, $exlink );
			
			echo "</urlset>\n";
			die();
		}
	};
?>