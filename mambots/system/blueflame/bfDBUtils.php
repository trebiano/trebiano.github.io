<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * Utility class to install, upgrade and migrate database
 * Tables at the same time as keeping a note of the version
 * numbers of a table
 * 
 * @version $Id: bfDBUtils.php 1106 2007-07-13 16:26:52Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

$str = mysql_get_server_info();
if (ereg('4.0', $str)){
	define('_BF_MYSQL', '4.0');
} else {
	define('_BF_MYSQL', '4.1');
}

class bfDBUtils {

	/**
	 * The existing tables in the database
	 *
	 * @var array
	 */
	var $_existingTables = array();

	/**
	 * The database prefix
	 *
	 * @var string
	 */
	var $_dbprefix = 'jos_';

	/**
	 * List of tables that we cant find and therefore need to install
	 *
	 * @var array
	 */
	var $_cantFindTables = array();

	/**
	 * A list of tables that this component requires
	 *
	 * @var array
	 */
	var $_componentTables = array();

	/**
	 * The array of sql queries that insert the default data for a newly created table
	 *
	 * @var array
	 */
	var $_componentDefaultData = array();

	/**
	 * The new version numbers of the tables
	 *
	 * @var array
	 */
	var $_componentDBVersions = array();

	/**
	 * PHP4 Constructor
	 *
	 * @return bfDBUtils
	 */
	function bfDBUtils(){
		return $this->__construct();
	}

	/**
	 * PHP5 Constructor
	 *
	 */
	function __construct(){
		global $mainframe;
		/* include xml2array */
		include_once(JPATH_ROOT . DS . 'administrator'. DS . 'components' . DS. $mainframe->get('component') . DS.'bfXML.php');

		/* get the dbo */
		$this->_db =& bfCompat::getDBO();

		/* get the database table prefix used on the joomla site */
		$this->_dbprefix = bfCompat::getdbPrefix();

		/* get a list of the tables already in the mysql db */
		$this->_getCurrentTables();

		/* get this components installation tables */
		$xml = new bfXml();
		$this->_componentTables = $xml->parse(JPATH_ROOT . DS . 'components' . DS. $mainframe->get('component') . DS . 'sql' . DS .'install' . DS .'tables.sql');

		/* fix for if no file found */
		if (!is_array($this->_componentTables)) $this->_componentTables = array();

		/* get version info */
		if (count($this->_componentTables)){
			foreach ($this->_componentTables as $k=>$v){
				if (ereg('_version', $k)){
					/* remove version info from component tables array */
					unset($this->_componentTables[$k]);

					/* strip _version from the table name - only used in the XML */
					$k = str_replace('_version','',$k);

					/* add table name and version number to array */
					$this->_componentDBVersions[$k] = $v;
				}
			}
		}
		/* load up the default data for tables into the array */
			$xml = new bfXml();
		$da = $xml->parse(JPATH_ROOT . DS . 'components' . DS. $mainframe->get('component') . DS . 'sql' . DS . 'install' . DS .'data.sql');
		$this->_componentDefaultData = $da;

	}

	/**
	 * I populate the class with a list of the current
	 * db tables 
	 *
	 */
	function _getCurrentTables(){
		$this->_db->setQuery('SHOW TABLES');
		$existingTables = $this->_db->LoadResultArray();
		foreach ($existingTables as $t){
			$this->_existingTables[$t] = $t;
		}
	}

	/**
	 * I check that all required tables are installed and if not I attempt to install them.
	 *
	 */
	function checktables(){

		/* check for bfVersions db */
		if (!array_key_exists($this->_dbprefix.'bfdbversions',$this->_existingTables)){
			/* not found so install it */
			$this->_createbfDBVersionTable();
			// echo 'Creating DB Table';
		}

		$dberrors = 0;

		/**
		 * For every table we require:
		 *  1. Check its in the db already
		 *  2. If not try and install it (Which adds default data)
		 */
		foreach ($this->_componentTables as $tableName => $sql){
			if (!array_key_exists($this->_dbprefix.$tableName,$this->_existingTables)){
				$dberrors++;
				$this->_cantFindTables[] = $tableName;
			}
		}

		if ($dberrors > 0){
			$this->_installTables();
		}

		$this->_checkForUpgradeToDBTables();
	}

	function _checkForUpgradeToDBTables(){
		$needsUpgrade = false;

		/* for all our component tables, check db version and xml versioon */
		$currentTables =& $this->_componentTables;
		$requiredVersions =& $this->_componentDBVersions;
		foreach ($currentTables as $tableName => $sql){
			$existingVer = $this->getTableVersionFromDB($tableName);
			$latestVer = $requiredVersions[$tableName];
//echo 'Table = ' . $tableName .'  Existing = '. $existingVer . ' Latest = '. $latestVer;
//echo '<br />';
			if ($latestVer > $existingVer){
				$needsUpgrade = true;
			}
		}

		if ($needsUpgrade === true){
			$this->_processUpgradeTasks();
		}
	}

	function _processUpgradeTasks(){
		
		$needsUpgrade = array();
		$db =& bfCompat::getDBO();
		/* for all our component tables, check db version and xml version */
		$currentTables =& $this->_componentTables;
		$requiredVersions =& $this->_componentDBVersions;
		foreach ($currentTables as $tableName => $sql){
			$existingVer = $this->getTableVersionFromDB($tableName);
			$latestVer = $requiredVersions[$tableName];

			if (!$existingVer){
				$existingVer = $this->_populatedbversion($tableName);
			}


			if ($latestVer > $existingVer){
				$needsUpgrade[] = array('tablename'=>$tableName,'existingversion'=>$existingVer);
				//								echo 'Table = ' . $tableName .'  Existing = '. $existingVer . ' Latest = '. $latestVer;
				//								echo '<br />';
			}


		}

		if (count($needsUpgrade)){
			global $mainframe;
			/* get this components installation tables */
			$xml = new bfXml();
			$tasks = $xml->parse(JPATH_ROOT . DS . 'components' . DS. $mainframe->get('component') . DS . 'sql' . DS . 'upgrade' . DS .'upgrade.sql');

			foreach ($needsUpgrade as $table){
				foreach ($tasks['task'] as $task){
					if ($task['tablename'] == $table['tablename']){
						//												echo $task['tablename'] . ' == ' . $table['tablename'] .' <br />';
						if ($task['applytoversion'] == $table['existingversion']){
							$sqls = explode('; ',$task['sql']);
							foreach ($sqls as $sql){
								$db->setQuery($sql);
								//																echo $db->getQuery();
								$db->query();
								echo $db->getErrorMsg();
							}

							$this->updateVerinDb($table['tablename'], $task['provides']);
							echo '<br />'.$table['tablename'].' upgraded!... ';

						}
					}
				}
			}
		}

		die( bfText::_('Database tables have been auto upgraded - please refresh this page to continue') );
	}

	function getTableVersionFromDB($tableName){
		global $mainframe;
		$db =& bfCompat::getDBO();
		$db->SetQuery('SELECT version FROM #__bfdbversions WHERE component = \''.$mainframe->get('component').'\' AND tablename = \''.$tableName.'\'');
		$ver = $db->LoadResult();
		return $ver;
	}

	function checkComponentLink(){
		global $mainframe;
		$sn = str_replace('com_','',$mainframe->get('component'));
		$db =& bfCompat::getDBO();
		$registry =& bfRegistry::getInstance($mainframe->get('component'));

		$db->setQuery("DELETE FROM `#__components` WHERE `option` ='".$mainframe->get('component')."'");
		$db->query();

		$sql = "INSERT INTO `#__components` (`id`, `name`, `link`, `menuid`, `parent`, `admin_menu_link`,
					`admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`) VALUES
					('', '".$registry->getValue('Component.Title').' v'.$registry->getValue('Component.Version')
		."', 'option=com_".$sn."', 0, 0, 'option=com_".$sn
		."', 'Configure ".$registry->getValue('Component.Title').' v'.$registry->getValue('Component.Version')
		."', 'com_".$sn."', 0, '../mambots/system/blueflame/view/images/menulogo.gif', 0, '');
					";
		$db->setQuery($sql);
		$db->query();
	}


	function migrate(){
		global $mainframe;
		$db =& bfCompat::getDBO();
		$results = array();
		$errors = array();

		/* clean up first - we did warn them! */
		$results[] = '<h1>'. bfText::_('Clear Current Tables') .'</h1><ul class="bfsubmenu">';
		$xml = new bfXml();
		$tasks = $xml->parse(JPATH_ROOT . DS . 'components' . DS. $mainframe->get('component') . DS . 'sql' . DS . 'cleanup' . DS .'removeall.sql');

		foreach ($tasks['task'] as $task){
			$db->setQuery($task['sql']);
			$ret = $db->query();
			if ($ret == 0){
				/* error */
				$results[] = '<li class="error">' .bfText::_('ERROR!'). ' - ' . $task['title'] . '<br />'. $db->getErrorMsg() . '</li>';
			} else {
				$results[] = '<li class="noerror">' . $task['title'] . ' - '. bfText::_('Done') . '!</li>';
			}
		}
		$results[] = '</ul>';
		/* run migration sql */

		/* migration */
		$results[] = '<h1>Run Migration Tasks</h1><ul class="bfsubmenu">';
		$xml = new bfXml();
		$tasks = $xml->parse(JPATH_ROOT . DS . 'components' . DS. $mainframe->get('component') . DS . 'sql' . DS . 'migrate' . DS .'migrate.sql');
		foreach ($tasks['task'] as $task){
			$db->setQuery($task['sql']);
			$ret = $db->query();
			if ($ret == 0){
				/* error */
				$results[] = '<li class="error">' .bfText::_('ERROR!'). ' - ' . $task['title'] . '<br />'. $db->getErrorMsg() . '</li>';
			} else {
				$results[] = '<li class="noerror">' . $task['title'] . ' - '. bfText::_('Done!') . '</li>';
			}
		}
		$results[] = '</ul>';

		/* install any missing/new tables */
		$this->checktables();

		return array($results, $errors);
	}

	function updateVerinDb($tablename, $currentVer){
		global $mainframe;
		$db =& bfCompat::getDBO();
		$db->setQuery(
		"UPDATE #__bfdbversions SET version = '".$currentVer."' WHERE component = '".$mainframe->get('component')."' AND tablename = '".$tablename."'");
		$db->query();
	}

	/**
	 * I install missing tables required by this component
	 *
	 */
	function _installTables(){
		foreach ($this->_cantFindTables as $installThisTable){
			if (array_key_exists($installThisTable, $this->_componentTables)){
				$sql = $this->_componentTables[$installThisTable];
				if (is_array( $sql )){
					$sql = implode ('', $this->_componentTables[$installThisTable] );
				}

				/**
				 * Mysql 4.0 backwards compatibility
				 */
				if (_BF_MYSQL=='4.0'){
					$sql = str_replace('collate utf8_bin' ,'' ,$sql);
					$sql = str_replace('DEFAULT CHARSET=utf8 COLLATE=utf8_bin' ,'' ,$sql);
					$sql = str_replace('character set utf8 collate utf8_bin' ,'' ,$sql);
				}
				
				$this->_db->setQuery($sql);
				$this->_db->query();
				$this->_getCurrentTables();
				$this->_addDefaultData($installThisTable);
				$this->_addtobfVersionsDB($installThisTable,$this->_componentDBVersions[$installThisTable]);

			}
		}
	}

	function _populatedbversion($table){
		return $this->_addtobfVersionsDB($table,$this->_componentDBVersions[$table]);
	}



	/**
	 * I add an entry to the bfversiondb to keeptrack of database version numbers
	 *
	 * @param string $tablename The table name to add
	 * @param float $version The version number
	 */
	function _addtobfVersionsDB($tablename,$version){
		global $mainframe;
		$sql = "DELETE FROM #__bfdbversions WHERE tablename = '".$tablename."'";
		$this->_db->setQuery($sql);
		$this->_db->query();
		$sql = "INSERT INTO `#__bfdbversions` ( `tablename` , `version` , `component` )
				VALUES (
				'".$tablename."', '".$version."', '".$mainframe->get('component')."'
				);";
		$this->_db->setQuery($sql);
		$this->_db->query();
		return $version;
	}

	/**
	 * I install default data to a newly created table
	 *
	 * @param string $installThisTable The table to populate
	 */
	function _addDefaultData($installThisTable=null){

		/* check database table exists */
		if (array_key_exists($installThisTable, $this->_componentTables)){
			if (array_key_exists($installThisTable, $this->_componentDefaultData)){
				$sql = $this->_componentDefaultData[$installThisTable];
				if (is_array($sql)){
					$sql = implode ('', $this->_componentDefaultData[$installThisTable] );
				}
				$this->_db->setQuery($sql);
				$this->_db->query();
			}
		}
	}

	/**
	 * I create the bfDBVersions database Table.
	 *
	 */
	function _createbfDBVersionTable(){
		$sql = 'CREATE TABLE `#__bfdbversions` (
				`tablename` VARCHAR( 255 ) NOT NULL ,
				`version` VARCHAR( 255 ) NOT NULL ,
				`component` VARCHAR( 255 ) NOT NULL
				) ENGINE = MYISAM ;';
		$this->_db->setQuery($sql);
		$this->_db->query();
	}
}
?>