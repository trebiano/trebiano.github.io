<?php

defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $mosConfig_absolute_path, $mainframe, $_MAMBOTS;
$_MAMBOTS->registerFunction('onAfterStart', 'azrulSysBot');
include_once($mosConfig_absolute_path . "/mambots/system/pc_includes/template.php");

function azrulSysBot() {
	global $mosConfig_absolute_path, $option, $database;
	
	// pull query data from class variable
	$database->setQuery("SELECT `params` FROM `#__mambots` WHERE `element`='azrul.system'");
	$mambot = $database->loadResult();
 	
	global $mosConfig_absolute_path, $mainframe;
	require_once ($mosConfig_absolute_path . '/mambots/system/pc_includes/ajax.php');
	
	$jax = new JAX($GLOBALS['mosConfig_live_site'] . "/mambots/system/pc_includes", $mambot);
	$jax->setReqURI($GLOBALS['mosConfig_live_site'] . "/index.php");
	$jax->process();
	if (!isset ($_POST['no_html'])) {
		$mainframe->addCustomHeadTag($jax->getScript());
	}
}
