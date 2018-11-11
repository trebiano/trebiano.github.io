<?php defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); ?>
<?php

	/**
	 * Wraps HTML representation of the Joomap tree as an unordered list (ul)
	 * @author Daniel Grothe
	 * @see joomla.php
	 * @package Joomap
	 */

	/** Wraps HTML output */
	class JoomapHtml {
		
		/** Convert sitemap tree to an 'unordered' html list.
		 * This function uses recursion, keep unnecessary code out of this!
		 */
		function &getHtmlList( &$tree, &$exlink, $level = 0 ) {
			global $Itemid;
			
			if( !$tree )
				return '';
			
			$out = '<ul class="level_'.$level.'">';
			foreach($tree as $node) {
				
				if ( $Itemid == $node->id )
					$out .= '<li class="active">';
				else
					$out .= '<li>';
				
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

				if( strcasecmp( substr( $link, 0, 5), 'http:' ) )
					$link = sefRelToAbs($link);						// apply SEF transformation

				switch( $node->browserNav ) {
					case 1:											// open url in new window
						$ext_image = '';
						if( $exlink[0] ){
							$ext_image = '&nbsp;<img src="'. $GLOBALS['mosConfig_live_site'] .'/components/com_joomap/images/'. $exlink[1] .'" alt="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" title="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
						}
						$out .= '<a href="'. $link .'" title="'. $node->name .'" target="_blank">'. $node->name . $ext_image .'</a>';
						break;

					case 2:											// open url in javascript popup window
						$ext_image = '';
						if( $exlink[0] ) {
							$ext_image = '&nbsp;<img src="'. $GLOBALS['mosConfig_live_site'] .'/components/com_joomap/images/'. $exlink[1] .'" alt="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" title="' . _JOOMAP_SHOW_AS_EXTERN_ALT . '" border="0" />';
						}
						$out .= '<a href="'. $link .'" title="'. $node->name .'" target="_blank" '. "onClick=\"javascript: window.open('". $link ."', '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=550'); return false;\">". $node->name . $ext_image."</a>";
						break;

					case 3:											// no link
						$out .= '<span>'. $node->name .'</span>';
						break;

					default:										// open url in parent window
						$out .= '<a href="'. $link .'" title="'. $node->name .'">'. $node->name .'</a>';
						break;
				}
				
				if( isset($node->tree) ) {
					$out .= JoomapHtml::getHtmlList( $node->tree, $exlink, $level + 1 );
				}
				$out .= '</li>' . "\n";
			}
			$out .= '</ul>' . "\n";
			return $out;
		}
		
		/** Print an component heading, etc. Then call getHtmlList() to print list */
		function printTree( &$joomap, &$root ) {
			global $database, $Itemid;
			$cfg = &$joomap->config;
		
			$menu = new mosMenu( $database );
			$menu->load( $Itemid );								// Load params for the Joomap menu-item
			$title = $menu->name;
			
			$exlink[0] = $cfg->exlinks;							// image to mark popup links
			$exlink[1] = $cfg->ext_image;

			if( $cfg->columns > 1 ) {							// calculate column widths
				$total = count($root);
				$columns = $total < $cfg->columns ? $total : $cfg->columns;
				$width	= (100 / $columns) - 1;
			}

			echo '<div class="'. $cfg->classname .'">';
			echo '<h2 class="componentheading">'. $title .'</h2>';
			echo '<div class="contentpaneopen"'. ($cfg->columns > 1 ? ' style="float:left;width:100%;"' : '') .'>';
			
			if( $cfg->show_menutitle || $cfg->columns > 1 ) {	// each menu gets a separate list
				foreach( $root as $menu ) {
					
					if( $cfg->columns > 1 )						// use columns
						echo '<div style="float:left;width:'.$width.'%;">';
					
					if( $cfg->show_menutitle )					// show menu titles
						//echo '<h2 class="menutitle">'.$menu->name.'</h2>';

					echo JoomapHtml::getHtmlList( $menu->tree, $exlink );
					if( $cfg->columns > 1 )
						echo "</div>\n";
				}

				if( $cfg->columns > 1 )
					echo '<div style="clear:left"></div>';

			} else {															// don't show menu titles, all items in one big tree
				$tmp = array();
				foreach( $root as $menu ) {										// concatenate all menu-trees
					foreach( $menu->tree as $node ) {
						$tmp[] = $node;
					}
				}
				echo JoomapHtml::getHtmlList( $tmp, $exlink );
			}
			
			//BEGIN: Advertisement (improve my google ranking)
			$keywords = array('Webdesign', 'Software Entwickler', 'Software Entwicklung', 'Programmierer');
			$location = array('Iserlohn', 'Hagen', 'Dortmund', 'Ruhrgebiet', 'NRW');
			$advert = $keywords[rand() % count($keywords)].' '.$location[rand() % count($location)];
			echo "<a href=\"http://www.ko-ca.com\" style=\"font-size:1px;display:none;\">$advert</a>";
			//END: Advertisement
			
			echo "</div>";
			echo "</div>\n";
		}
	};
?>