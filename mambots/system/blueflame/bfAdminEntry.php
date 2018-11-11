<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );
/**
 * @version $Id: bfAdminEntry.php 1076 2007-07-12 15:26:20Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 *
 * I am the point of entry to this component
 * I set up the Control Panel items like toolbar and hidden divs
 * so we can populate them by xAJAX
*/
if (defined( '_VALID_MOS' ) OR defined( '_JEXEC' )){
	/* ok we are in Joomla 1.0.x or Joomla 1.5+ */
	if (!defined('_VALID_MOS'))	{
		/* We are in Joomla 1.5 */
		define('_VALID_MOS', '1');
		define('_PLUGIN_DIR_NAME','plugins');
		define('_BF_PLATFORM','JOOMLA1.5');
	} else if (!defined('_JEXEC')){
		/* we are in Joomla 1.0 */
		define('_JEXEC', '1');
		define('_PLUGIN_DIR_NAME','mambots');
		define('_BF_PLATFORM','JOOMLA1.0');
		define('JPATH_ROOT', $GLOBALS['mosConfig_absolute_path']);
		if (!defined('DS')) 		define('DS', DIRECTORY_SEPARATOR);
	}
} else {
	header('HTTP/1.1 403 Forbidden');
	die('Direct Access Not Allowed');
}

//error_reporting(E_ALL);

/* Pull in the bfFramework */
require_once(JPATH_ROOT . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfCompat.php');
require_once(JPATH_ROOT . DS .  _PLUGIN_DIR_NAME . DS . 'system' . DS . 'blueflame' . DS . 'bfFramework.php');


/* debug */
$log->spacer(5);
$log->log('=============== Page refesh :: Rebuilding Cpanel ===============');

/* Check we have rights to do this */
bfSecurity::checkPermissions('AdminConsole','Load admin panel');

/* Initialise the session before calling the controller constructor */
$bfsession =& bfSession::getInstance($mainframe->get('component'));
/* @var $bfsession bfSession */

/* Grab our registry */
$registry =& bfRegistry::getInstance($mainframe->get('component'), $mainframe->get('component'));
/* @var $registry bfRegistry */

/* Set the Page Generator Meta Tag */
bfDocument::setGenerator('BFFramework');

bfLoad('bfChecks');
$check = new bfChecks();
if ($check->runchecks() === false){
	return;
}

/**
 * Debug Screen
 */
$debug = bfRequest::getVar('debug',null,'REQUEST','INT');
if ($debug){
	bfSecurity::checkPermissions('Admin.viewDebug',' view Debug infomation',true);
	$log->log('Displaying debug information page');
	include(_BF_FRONT_LIB_VIEW_DIR . DS . 'debug.php');
	return;
}

/* Load our Framework Javascript Lib and CSS */
/* Use compressor */
bfDocument::addscript(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=js&c='.$mainframe->get('component_shortname').'&f=mootools,jquery,jquery.tabs,jquery.thickbox_js,bfadmin_js,admin_js,jquery.accordion');
bfDocument::addCSS(bfCompat::getLiveSite() . '/'._PLUGIN_DIR_NAME.'/system/blueflame/bfCombine.php?type=css&c='.$mainframe->get('component_shortname').'&f=bfadmin_css,admin_css');

/* Add firebug */
//bfDocument::addscript('../firebug/firebug.js');

/* Load Overlib JS so er can use it if needed - cannot  be included by xAJAX */
//bfCommonHTML::loadOverlib();
//bfCommonHTML::loadCalendar();

if (_BF_PLATFORM=='JOOMLA1.0'){
	$_MAMBOTS->loadBotGroup('system');
	
	$c = 0;
	if (count(@$_MAMBOTS->_events['onAfterStart'])){
		foreach ($_MAMBOTS->_events['onAfterStart'] as $trigger){
			
			/* Damage limitation for com_smf - SMF Bridge System Mambot */
			if ($trigger[0]=='SMF_header_include'){
				unset($_MAMBOTS->_events['onAfterStart'][$c]);
			}
			
			/* JP Meta Edit hack */
			if ($trigger[0]=='jpMetaEdit'){
				unset($_MAMBOTS->_events['onAfterStart'][$c]);
			}
			
			/* JP Meta Edit hack */
			if ($trigger[0]=='bot_jstats_activate'){
				unset($_MAMBOTS->_events['onAfterStart'][$c]);
			}
			
			$c++;
		}
	}
	$_MAMBOTS->trigger('onAfterStart', array(true), true);
	$_MAMBOTS->trigger('xajax_onAfterStart', array(true), true);
}

/* Set the Default Page Title - Should be overridden by xAJAX tasks Later */
bfDocument::setTitle($registry->getValue('Component.Title') . ' v' . $registry->getValue('Component.Version') );

/* include our other framework libs */
bfLoad('bfController');
bfLoad('bfModel');

/* Load the admin controller file */
require($registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.controller.admin'));

/* Calculate the class name*/
$controller_class = $mainframe->get('component') . 'Controller';

/* Create new controller object */
$controller = new $controller_class();
/* @var $controller bfController */

/* Set the path to our models */
//$controller->setModelPath(_BF_FRONT_MODEL_DIR);

/* Set our component Name*/
/* @TODO cant we move this to the defaults in controller ? */
$controller->setComponentName($mainframe->get('component'));


if (bfRequest::getVar('tree',null) !== null){
	define('_POPUP',0);
	bfLoad('bfJSTree');
	$tree = new bfJSTree();
	function getArticles($cat, $controller, $tree){
		error_reporting(E_ALL);
		$model = $controller->getModel('category');
		$articles = $model->getArticlesForThisCategory($cat->id);
		foreach ($articles as $article){
			$tree->_append(' insDoc(a_'.$tree->makejssafe($cat->title).', gLnk("S", "'.$article->title.'", "demoFrameless.html?pic=%22beenthere_lisbon%2Ejpg%22"))');
		}
	}


	$iiii =0;
	function recursive_build($parent, &$db, $tree, $controller){
		global $iiii;
		$iiii++;
		if ($iiii > 50 ) die('too many recursion');
		$db->setQuery('SELECT * FROM #__kb_categorys WHERE parentid = '.$parent->id.' ORDER BY title');
		$parentcats =$db->loadObjectList();
		foreach ($parentcats as $cat){
			$tree->_append('a_' . $tree->makejssafe($cat->title).' = insFld(a_'.$tree->makejssafe($parent->title).', gFld("'.$cat->title.'", "javascript:undefined"))');
			getArticles($cat, $controller, $tree);
			$db->setQuery('SELECT count(id) FROM #__kb_categorys WHERE parentid = '.$cat->id.' ORDER BY title');
			$haskids =$db->loadObjectList();
			if ($haskids) recursive_build($cat, &$db, $tree,$controller);
		}

	}
	$db =& bfCompat::getDBO();
	$db->setQuery('SELECT * FROM #__kb_categorys WHERE parentid = "0" ORDER BY title');
	$parentcats =$db->loadObjectList();
	foreach ($parentcats as $cat){
		$tree->_append('a_' . $tree->makejssafe($cat->title).' = insFld(foldersTree, gFld("'.$cat->title.'", "javascript:undefined"))');
		getArticles($cat, $controller, $tree);
		recursive_build($cat, &$db, $tree,$controller);

	}
	//$tree->_append("foldersTree.addChildren([". implode(', ', $items)."])");


	$tree->addJStoHEAD();

?>
<TABLE border=0><TR><TD><FONT size=-2><A style="font-size:7pt;text-decoration:none;color:silver" href="http://www.treemenu.net/" target=_blank>Javascript Tree Menu</A></FONT></TD></TR></TABLE>

<SPAN class=TreeviewSpanArea>
            <SCRIPT>initializeDocument()</SCRIPT>
            <NOSCRIPT>
             A tree for site navigation will open here if you enable JavaScript in your browser.
            </NOSCRIPT>
           </SPAN>
           <?php

           return;
}

$tmpl = bfRequest::getVar('tmpl', null, 'REQUEST', 'STRING', 0);

if ($tmpl=='component'){ // Then Preview Mode

	/* Set our registry flag */
	$registry->setValue('isPopup',true);

	/* Set up our hidden div used for feedback messages and loading flash screen */
	//echo "<div id=\"tag-message\" style=\"display:none;\">\n\t<div class=\"loading\">Loading, Please wait ...</div>\n</div>";

	/* Load dependances */
	bfLoad('bfHTML');

	/* Set the args */
	$controller->setArguments( bfRequest::get('REQUEST') , false );

	/* execute the task */
	$controller->execute($task);

	/* render the view */
	$controller->renderView();

	return;
}

/* Reset Our Session */
$bfsession->reset();

/* Import the js for out tabs and sliders if needed */
//jimport('joomla.html.pane');
//$tabs 		=& bfPane::getInstance('Tabs');
//$sliders 	=& bfPane::getInstance('Sliders');
//
/* Set up our hidden div used for feedback messages and loading flash screen */
echo "<div id=\"tag-message\" style=\"display:none;\">\n\t<div class=\"loading\">".bfText::_("Loading, Please wait")." ...</div>\n</div>";
echo "<div id=\"loading\" style=\"display:block;\">\n\t<div class=\"loading\">".bfText::_("Loading, Please wait")." ...</div>\n</div>";
echo '<iframe id="bf_iframe" name="bf_iframe" style="width: 0px; height: 0px;display:none;" src="about:blank"></iframe>';
if (_BF_PLATFORM=='JOOMLA1.5'){
	/* Load editors js into head - only woks when editors are turned on in J config - loads of JS */
	/* @var $editor JEditor */
	$editor =& JFactory::getEditor();
	$editor->_loadEditor(); // Fudge
	$editor->initialise();
} else {
	global $_MAMBOTS, $my, $mainframe;
	$user = bfUser::getInstance();
	$my->params = $user->get('params') ? $user->get('params') : '';
	$my->gid = $user->get('gid') ? $user->get('gid') : '';
	include(bfCompat::getCfg('absolute_path').DS.'editor'.DS.'editor.php');
	$mainframe->set( 'loadEditor', true );
	initEditor();
}

/* specific for forms */
$formid = bfRequest::getVar('formid','','REQUEST', 'int');
if ($formid){
	$bfsession->set('lastFormId',$formid,'default');
} else {
	$bfsession->set('lastFormId','','default');
}

/* Set up our components main DIV on first page load - never needs to be done again */
bfHTML::displayAdminHeader($controller);

echo "\n<!-- Start of component div -->\n<div id=\"".$mainframe->get('component')."\"></div>\n<!-- End of component div -->";

$ver = file_get_contents(_BF_FRAMEWORK_LIB_DIR . DS . 'bfVersion.php');
$ver = str_replace("<?php\n/*\n", '' , $ver);
$ver = str_replace("\n*/\n?>", '' , $ver);

/* Add bfCopywrite */
echo "\n" . '<br /><div class="clear"></div>'
. '<div id="bfCopyright">'
. $registry->getValue('Component.Title') . ' v'. $registry->getValue('Component.Version')
. ' - bfFramework v'.$ver

.' <br /><a title="'.bfText::_('Visit our site').'::'.bfText::_('Click here to visit the Blue Flame IT Ltd website for help support and updates').'" class="hasTip" href="http://www.phil-taylor.com/in.php?'.bfCompat::getLiveSite().'" target="_blank">&copy; ' . date('Y') .' Blue Flame IT Ltd.</a>  '
. '<br />&raquo; <b><i>'
.bfText::_('Power In Simplicity').'!</i></b> &laquo;'
. '</div>';

/* Call our first xAJAX function to populate the newly built controlpanel with our default view */
echo "\n\n".'<script type="text/javascript">
<!--

           bfHandler(\''.$registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.defaultHomePageView').'\');
           jQuery(\'#wrapper\').hide();
		   jQuery(\'.menudottedline\').hide();
		   jQuery.ready( function(){ jQuery(\'.footer\').hide() } );
		   setTimeout(function(){jQuery(\'.footer\').hide();}, 500);
		   -->
           </script>';

echo '<div id="showjoomla" style="text-decoration: none; position: absolute; top: 5px; left: 560px;"><a href="#" onclick="jQuery(\'#wrapper\').toggle();jQuery(\'.menudottedline\').toggle();jQuery(\'.footer\').toggle();jQuery(this).blur();">&raquo; Show Joomla</a></div>';

//           if (_BF_PLATFORM=='JOOMLA1.5') echo "\n\n".'<script>jQuery(\'div#toolbar-box\').toggle();</script>';

//echo '<div id="toogleJoomla" class="toggleJoomla"><a onclick="showJoomla();" href="#">Show Menus</a></div>';
/* Finally, add our branding watermark */
//           echo '<script>if (document.all||document.getElementById) document.body.style.background="url(\'http://www.phil-taylor.com/images/bg.gif\') white bottom left no-repeat fixed"</script>';

//bfHTML::keepAlive();

?>