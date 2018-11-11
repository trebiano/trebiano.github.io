<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: debug.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 */

/* Get our registry */
$registry =& bfRegistry::getInstance();

/* Get Joomla Version info */
jimport('joomla.version');
$version = new bfVersion();

/* Get components List */
$db =& bfFactory::getDBO();
$db->setQuery('SELECT DISTINCT name , `option` FROM #__components WHERE iscore = "0" AND `option` != "" ');
$components = $db->loadObjectList();
$componentsList="\n";
/* Build list */
foreach ($components as $com){
	$componentsList .= "\t\t" . $com->option . ' = ' . $com->name . "\n";
}


/* Display Textarea with config in */
?>
<textarea style="width: 100%; height: 300px;">
Joomla Version :: <?php echo $version->getLongVersion(); ?>

PHP Version :: <?php echo phpversion(); ?>

Components installed :: <?php echo $componentsList; ?>

/* @TODO */
MySQL Version ::
SEF On ::
Legacy Mode ::
xAJAX Plugin Installed ::
xAJAX Plugin Version ::
bfFramework version ::
</textarea>

<script>

$('toolbar').innerHTML = '<?php echo '<h1 style="float: left">'. $registry->getValue('Component.Title') . ' v' . $registry->getValue('Component.Version') . ' :: '.bfText::_('Debug Information').' &nbsp;&nbsp;&nbsp;&nbsp;</h1>'; ?>'

</script>
