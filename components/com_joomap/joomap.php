<?php defined('_VALID_MOS') or die('Direct Access to this location is not allowed.'); ?>
<?php
	/* Joomap by Daniel Grothe
	 * a sitemap component for Joomla! CMS (http://www.joomla.org)
	 * Author Website: http://www.ko-ca.com/
	 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
	 * Additional work by mic (http://www.mgfi.info)
	 */

	/**
	 * The Joomap component frontend
	 * @author Daniel Grothe
	 * @see joomla.html.php
	 * @see joomla.google.php
	 * @package Joomap
	 */

	// load Joomap language file
	$LangPath = $GLOBALS['mosConfig_absolute_path'] . '/administrator/components/com_joomap/language/';
	if( file_exists( $LangPath . $GLOBALS['mosConfig_lang'] . '.php') ) {
	    require_once( $LangPath . $GLOBALS['mosConfig_lang'] . '.php' );
	} else {
	    require_once( $LangPath . 'english.php' );
	}

	require_once( $GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_joomap/classes/JoomapConfig.php' );
	$config = new JoomapConfig;
	$config->load();

	require_once( $GLOBALS['mosConfig_absolute_path'].'/administrator/components/com_joomap/classes/JoomapPlugins.php' );
	JoomapPlugins::loadPlugins();

	$joomap = new Joomap( $config );
	$tree = $joomap->generateTree();
	//$joomap->printDebugTree( $tree );		// DEBUG output


	$view = mosGetParam( $_REQUEST, 'view', 'html' );
	switch( $view ) {

		case 'google':															// Google Sitemaps output
			require_once( $GLOBALS['mosConfig_absolute_path'] .'/components/com_joomap/joomap.google.php' );
			JoomapGoogle::printTree( $joomap, $tree );
			break;

		default:																// Html output
			require_once( $mainframe->getPath('front_html') );
			$mainframe->addCustomHeadTag( '<link rel="stylesheet" type="text/css" media="all" href="' . $GLOBALS['mosConfig_live_site'] . '/components/com_joomap/css/joomap.css" />' );
			JoomapHtml::printTree( $joomap, $tree );
			break;

	}

	/**
	 * Generates a node-tree of all the Menus in Joomla!
	 * This is the main class of the Joomap component.
	 * @author Daniel Grothe
	 * @access public
	 */
	class Joomap {
		/** @var JoomapConfig Configuration settings */
		var $config;
		/** @var integer The current user's access level */
		var $gid;
		/** @var boolean Is authentication disabled for this website? */
		var $noauth;
		/** @var string Current time as a ready to use SQL timeval */
		var $now;

		/** Default constructor, requires the config as parameter. */
		function Joomap( $config ) {
			$this->config = $config;
		}

		/** Generate a full website tree */
		function &generateTree() {
			$menus = $this->config->getMenus();
			$root = array();
			foreach ( $menus as $menutype => $menu ) {
				if( !$menu->show )
					continue;

				$node = new stdclass;

				$node->ordering = $menu->ordering;
				$node->tree = $this->getMenuTree($menutype);

				if( count($node->tree) == 0 )									// ignore empty menus
					continue;

				$node->browserNav = 3;
				$node->type = 'separator';
				$node->name = $this->getMenuTitle($menutype);					// get the mod_mainmenu title from modules table
				$root[] = $node;												// add the menu to the sitetree
			}
			usort($root, array('Joomap','sort_ordering'));						//sort the root tree according to ordering
			return $root;
		}

		/** Get a Menu's tree
		 * Get the complete list of menu entries where the menu is in $menutype.
		 * If the component, that is linked to the menuentry, has a registered handler,
		 * this function will call the handler routine and add the complete tree.
		 * A tree with subtrees for each menuentry is returned.
		 */
		function &getMenuTree( &$menutype ) {
			global $database, $mosConfig_offset, $mainframe, $my;

			if( strlen($menutype) == 0 ){
				return null;
			}

			$menuExluded	= explode( ',', $this->config->exclmenus ); 		// by mic: fill array with excluded menu IDs
			// echo '<br />[DEBUG excluded menus] ' . $this->config->exclmenus . '<br />';

			$this->now		= date( 'Y-m-d H:i:s', time() + $mosConfig_offset * 60 * 60 );
			$this->noauth 	= $mainframe->getCfg( 'shownoauth' );
			$this->gid		= $my->gid;

			/* * noauth is true
			   - No authentication required throughout the whole website!
			   - Select all menuentries without regard to the client's access-level.
			   * noauth is false
			   - Access to certain areas of the website is restricted!
			   - Select only those menuentries for which the client has a high enough access-level.
			*/
			$sql = "SELECT m.id, m.name, m.parent, m.link, m.type, m.browserNav, m.menutype, m.ordering, m.params, m.componentid, c.name AS component"
            . "\n FROM #__menu AS m"
            . "\n LEFT JOIN #__components AS c ON m.type='components' AND c.id=m.componentid"
            . "\n WHERE m.published='1' AND m.menutype = '".$menutype."'"
            . ( $this->noauth ? '' : "\n AND m.access <= '". $this->gid ."'" )
            . "\n ORDER BY m.menutype,m.parent,m.ordering";

			// Load all menuentries
			$database->setQuery( $sql );
			$items = $database->loadObjectList();

			if( count($items) == 0)		//ignore empty menus
				return null;

			$root = array();

			foreach($items as $i => $item) {									// Add each menu entry to the root tree.

                if( in_array( $item->id, $menuExluded ) ) {						// ignore exluded menu-items
                    continue;
                }

				$node = new stdclass;
				$node->id 			= $item->id;
				$node->name 		= $item->name;								// displayed name of node
				$node->parent 		= $item->parent;							// id of parent node
				$node->browserNav 	= $item->browserNav;						// how to open link
				$node->ordering 	= isset( $item->ordering ) ? $item->ordering : $i;	// display-order of the menuentry
				$node->link 		= isset( $item->link ) ? htmlspecialchars( $item->link ) : '';	// convert link to valid xml
				$node->type 		= $item->type;								// menuentry-type

				$node->tree 		= JoomapPlugins::getTree( $this, $item );	// Determine the menu entry's type and call it's handler

				$root[ $node->id ] 	= $node;									//add this node to the root tree
			}

			foreach($root as $node) {											//move children into the tree of their parent
				if( $node->parent > 0 && isset($root[ $node->parent ]) ) {
					$root[ $node->parent ]->tree[] = &$root[ $node->id ];
				}
			}

			foreach($root as $node) {											//remove all children from the toptree
				if( $node->parent > 0) {
					unset( $root[ $node->id ] );
				}
			}

			usort($root, array('Joomap','sort_ordering'));						//sort the top tree according to ordering

			return $root;
		}

		/** Look up the title for the module that links to $menutype */
		function getMenuTitle($menutype) {
			global $database;
			$query = "SELECT * FROM #__modules WHERE published='1' AND module='mod_mainmenu' AND params LIKE '%menutype=". $menutype ."%'";
			$database->setQuery( $query );
			if( !$database->loadObject($row) )
				return '';
			return $row->title;
		}

		/** Print tree details for debugging and testing */
		function printDebugTree( &$tree ) {
			foreach( $tree as $menu) {
				echo $menu->name."<br />\n";
				echo '<pre>';
				print_r( $menu->tree );
				echo '</pre>';
			}
		}

		/** called with usort to sort menus */
		function sort_ordering( &$a, &$b) {
			if( $a->ordering == $b->ordering )
				return 0;
			return $a->ordering < $b->ordering ? -1 : 1;
		}
	};

?>
