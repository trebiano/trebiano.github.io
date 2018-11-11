<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: superXMVC.php 827 2007-06-12 18:03:41Z phil $
 * @package bfFramework
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

/* if we forgot to include the framework */
if (!function_exists('bfLoad')){
	/* Pull in the bfFramework */
	include (JPATH_ROOT . DS .  'mambots' . DS . 'system' . DS . 'blueflame' . DS . 'bfFramework.php');
}
global $mainframe;
bfLoad('bfController');
bfLoad('bfModel');

/* Get our arguments */
$args = func_get_args();

$registry->setValue('args',$args);

/**
 * Pull in and set up the controller
 * run quick security test
 */
if (defined('_XAJAX_ADMIN')){
	
	/* Include our admin controller */
	require($registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.controller.admin'));

	/* Check the name of the xfunction */
	if (bfSecurity::checkXFunction($args)===false){
		/* Display popup alert */
		$objResponse->addalert(bfText::_('code(30), Access Denied to '). bfSecurity::cleanVar($args[0], 0));
		return $objResponse;
	}

} else{

	/* Include our front controller */
	require($registry->getValue('bfFramework_'.$mainframe->get('component_shortname').'.controller.front'));


	/* Check the name of the xfunction */
	if (bfSecurity::checkXPublicFunction($args)===false){
		/* Display popup alert */
		$objResponse->addalert(bfText::_('code(58), Access Denied to ') . bfSecurity::cleanVar($args[0], 0));
		return $objResponse;
	}
}


/* Get our task from xAJAX, its the first in the args array */
$task = (string) array_shift($args);

/* Security Check */
$task = (string) bfSecurity::cleanVar($task, 0);

/* Set our session mode */
$bfsession->setMode($task);

/* Make a controller instance and provide some sensible defaults */
$controller_class = $mainframe->get('component') . 'Controller';
$controller = new $controller_class();
/* @var $controller JController */

/* Pass the controller incoming args */
$controller->setArguments( $args );

/* push xAJAX ObjResponse into controller */
$controller->xajax =& $objResponse;

/* If the execute cannot find xfoo in the controller it sets view as foo */
$taskresult = $controller->execute( $task );

/* error checking - if we dont have a view we dont know what to do! */
$view = $controller->getView();

/* If we have no view then try the task name as a view name */
if (!isset($view) || $view == '') $view = $task;

/* Set the view name */
$bfsession->set('view', $view);

/*
* If this is an index then we need to know where to return to
* if we save or cancel an edit or change the filter states
*/
$bfsession->set('lastview' , $controller->getView() );

/* if an iframe */
//if (@defined('_BF_EMBED')) $controller->setLayout('embed');

/* Deal with the layout/view or just return some xajax actions*/
switch ($controller->getLayout()) {

	case 'simple':
		$simple = true;
		break;
	case "embed":
		
		/* stop loading menus etc... */
		$registry->setValue('isEmbed',1);
		
		/* Get the view, load it, parse it and get HTML back */
		$html = $controller->renderView();

		/* draw/set the Main DIV area with our view */
		$controller->xajax->addassign($controller->getXajaxTarget() , "innerHTML", $html);

		break;


	case 'none':
		break;

	case 'error':
		$controller->xajax->addalert('Error: ('.$registry->getValue('errno').') '.$registry->getValue('error'));
		break;

	case 'text':
		$action = $controller->getXajaxAction();
		$controller->xajax->$action($controller->getXajaxTarget() ,"innerHTML", $controller->getMessage());
		break;

	case 'html':
	case 'view':
		/* Get the view, load it, parse it and get HTML back */
		$html = $controller->renderView();

		/* draw/set the Main DIV area with our view */
		$controller->xajax->addassign($controller->getXajaxTarget() , "innerHTML", $html);

		/* if admin draw console */
		if (bfCompat::isAdmin()){

			/* Tool Bar Buttons */
			$toolbar = $controller->getToolbar( $task, true );
			$controller->xajax->addassign('bftoolbar', "innerHTML", $toolbar);

			/* HTML <title> tag */
			$controller->xajax->addscript('document.title="'.$controller->getPageTitle($task).'";');

			/* Set the Page Header */
			//			 $controller->xajax->addassign('bfHeader','innerHTML',$controller->getPageHeader());
			$controller->xajax->addscript(" jQuery('div#bfHeader').html('". $controller->getPageHeader() ."'); ");
		}

		/* See if we need to run more javascript */
		/* @TODO Hack in to controller */
		//
		$usedTabs = $registry->getValue('usedTabs');
		if ($usedTabs){
			$controller->xajax->addscript('jQuery(\'#bfTabs\').tabs({fxSlide: true, fxFade: true, fxSpeed: \'fast\'});jQuery(\'#bfTabs\').triggerTab();');
		}

		$hasPopups = $registry->getValue('hasPopups');
		if ($hasPopups){
			$controller->xajax->addscript('jQuery(document).ready(bfinit);');
		}

		$hideleftmenu = $registry->getValue('hideleftmenu');
		if ($hideleftmenu){
			$controller->xajax->addscript("jQuery('#leftmenu').hide();");
		}


		if (bfCompat::isAdmin()){
			$controller->xajax->addscript("scroll(0,0);");
		}

		break;

	case 'xml':
		// Not implemented yet but you probably only need to
		// point to an XML generating php file in the view directory.
		break;

}

/**
 * Init textareas into Editor Areas
 * fudge as TinyMCE is funny - see http://tinymce.moxiecode.com/punbb/viewtopic.php?pid=11534
 */
if (bfCompat::isAdmin()){
	$areas = $controller->getEditorAreas();
	if ($areas) {
		$scr = 'tinyMCE.idCounter=0;';
		foreach ($areas as $area){
			$scr .= "tinyMCE.execCommand('mceAddControl', true, '".$area."');";
		}
		$controller->xajax->addscript($scr);
	}
}
/* append to the objResponse any Alert message */
/* @var $controller bfController */
$msg = $controller->getxAJAXAlert();
if ($msg){
	$type = $msg[0];
	$text = $msg[1];
	$script = 'jQuery(\'div#tag-message\').html(\'<div class="'.$type.'">'.$text.'</div>\');
		 		jQuery(\'div#tag-message\').show();
	 			window.setTimeout(function(){ jQuery(\'div#tag-message\').hide(); } , 2000);
				';
	$controller->xajax->addscript($script);
}



if ($task != 'xkeepalive' && bfCompat::isAdmin() && !isset($simple)){
	/* fix mootools */
	$script = 'bf_fixTips();';
	$controller->xajax->addscript($script);
}

/* load thickbox JS init */
if ($registry->getValue('thickbox', false) === true){
	$controller->xajax->addscript('jQuery(document).ready(bfinit);');
}

/* load scripts */
$scr = $registry->getValue('script', null);
if ($scr!==null){
	$controller->xajax->addscript("jQuery(document).ready(function(){ ". $scr . " } );");
}



/* hide loading message */
if (!isset($simple)){
	$controller->xajax->addscript('hideLoadingMessage();');
}

/* IMPORTANT :: CLOSE THE SESSION - This saves the session back to the db or file */
if (_BF_PLATFORM=='JOOMLA1.5') JSession::close();
?>