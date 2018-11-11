<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: framework.config.php 1172 2007-07-25 12:13:41Z chris $
 * @revision $Revision: 1172 $
 * @package Joomla Tags
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license see LICENSE.php
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 * 
 * 
 * DO NOT MAKE MODIFICATIONS IN THIS FILE UNLESS YOU KNOW WHAT YOU ARE DOING !
 */

$registry =& bfRegistry::getInstance('com_tag', 'com_tag');

$registry->setValue('bfFramework_'.'tag'.'.defaultHomePageView', 'xtags');

/* Sub-menu elements in Administrator Screen and xAJAX actions */
$registry->setValue('bfFramework_'.'tag'.'.submenus.'.bfText::_('Tags'), 	'xtags|tags|'.bfText::_('View All Tags'));
$registry->setValue('bfFramework_'.'tag'.'.submenus.Moderation', 	'xmoderation|tagsadd|'.bfText::_('View All Unpublished Tags'));
$registry->setValue('bfFramework_'.'tag'.'.submenus.Tag Cloud', 	'xtagcloud|cloud|'.bfText::_('View Tag Cloud'));
$registry->setValue('bfFramework_'.'tag'.'.submenus.Customise', 'xcustomise|customise|'.bfText::_('Edit Preferences and customise options'));
$registry->setValue('bfFramework_'.'tag'.'.submenus.Maintenance', 'xmaintenance|maintenance|'.bfText::_('Run Maintenance on the database'));
$registry->setValue('bfFramework_'.'tag'.'.submenus.Addons', 	'xplugins|plugins|'.bfText::_('Install and uninstall the additional mambots and modules easily'));

/* Index view fields */
$registry->setValue('bfFramework_'.'tag'.'.indexFields.tags', array('id','tagname'=>'Tag','count'=>'Content Items','hits','access','published') );
$registry->setValue('bfFramework_'.'tag'.'.indexFields.category', array('id','title'=>'Category Title','totalincategory'=>'Listings','hits','access','published') );
$registry->setValue('bfFramework_'.'tag'.'.indexFields.comment',  array('id','name'=>'Persons Name','email'=>'Email','ip'=>'IP','date','browser','comment'=>'Comment Left','published') );
$registry->setValue('bfFramework_'.'tag'.'.indexFields.customfields',  array('id','friendlyname'=>'Friendly Name','fieldname'=>'Field Name','type'=>'HTML Field','published','access','ordering') );
$registry->setValue('bfFramework_'.'tag'.'.indexFields.layouts', array('id','title'=>bfText::_('Template Title'),'appliesto'=>bfText::_('Pertains To')) );

$registry->setValue('bfFramework_'.'tag'.'.addons.plugins', array( 'content.tag'=>bfText::_('Provides Tagging Interface In Content') ) );

$registry->setValue('bfFramework_'.'tag'.'.addons.modules', array(
'mod_tag_latest'=>bfText::_('Displays Latest Tags'),
'mod_tag_popular'=>bfText::_('Displays Popular Tags'),
'mod_tag_cloud'=>bfText::_('Displays Tag Cloud')));

$registry->setValue('bfFramework_'.'tag'.'.layout.pertainsto', array(
'tag'
));

$registry->setValue('joomfish_compatible', true);
$registry->setValue('joomfish.elementsfile', 'tag.jf');


$registry->setValue('bfFramework_'.'tag'.'.Customise.Tasks',
array(
array('xconfiguration', bfText::_('Tags Preferences'),bfText::_('Change your preferences to suit your website, loads of goodies in here'),'config'),
array('xlayouts',bfText::_('Set Up Your Layout Templates'),bfText::_('Change the way that your component frontend is shown by editing these templates - get creative!'),'customise'),
array('xcss',bfText::_('Edit Stylesheets'),bfText::_('Change the way that your component is rendered using CSS Styles'),'customise'),
array('xpatch',bfText::_('Patch Joomla to include Tag Tabs when editing content'),bfText::_('See the options for Joomla Tags Tab integration'),'maintenance')
));

$registry->setValue('bfFramework_'.'tag'.'.Maintenance.Tasks',
array(
array('clearObjectCache', bfText::_('Clear Object Cache'),bfText::_('This task purges all cached objects, including cached SQL Queries and Smarty Templates. <br />Run this task  as often as you want, and especially after making broad changes to any layout templates'),'clear'),
array('removeblanktagnames', bfText::_('(Legacy) Remove Blank Tags'),bfText::_('If you upgraded/migrated from the early version of Tags then you might have some blank tags. Run this tool to remove these'),'sum'),
array('clearContentCache',bfText::_('Clear Joomla Content Cache'),bfText::_('This task purges all cached Joomla!').'&trade; '.bfText::_('content items.<br />Run this task as often as you like.'),'clear'),
array('resettaghits',bfText::_('Reset All Hits To Zero'),bfText::_('This task resets the "Hits" total for all tags to Zero.<br /><b>Note: This ALSO purges the object cache to ensure correct data is stored in the cache</b><br />Run this task as often as you like.'),'statistics'),
array('importmetatags',bfText::_('Import Content Metatags as tags'),bfText::_('Imports all the meta keywords from your content items and creates tags for them.  Duplicates are removed and content items are tagged with tags matching the meta keywords.'),'clear'),
array('migratefromtagscomponent',bfText::_('Migrate Tags Data from "Tags Component"'),bfText::_('WARNING: This will remove ALL data in this component, and replace it with the migrated tags data, there is no confirm dialog so only hit this button if you are sure! (Backups ARE NOT made - MAKE SURE _YOU_ BACKUP YOUR DATABASE FIRST!!! )'),'clear')
));



/**
 * ##############################################################################################################################################################################
 * FRAME WORK REGISTRY VALUES ONLY
 * Hardly any changes needed below
 */

/* Get our final version number appended with the svn revision number (like a build number) */
$SVNRevision = '([0-9][0-9][0-9][0-9])'; //[0-9]
preg_match_all( $SVNRevision, '$Revision: 1172 $', $Revision );
$registry->setValue('Component.Version','0.1.'.$Revision[0][0]);
$registry->setValue('Component.Title','Trebiano E-Business Partner');
$registry->setValue('defaultLang',bfCompat::getCfg('lang_site'));
$registry->setValue('defaultEncoding',bfCompat::getCfg('lang_site'));
$registry->setValue('defaultdir','rtl');

/**
 * Itemid
 */
if (!bfCompat::isAdmin()){
	$itemid = bfUtils::findItemid();
	$registry->setValue('Itemid',$itemid);
}
/* Set minimum access levels */
/**
 * Gids:
 * 25 Super Administrator
 * 18 Registered
 * 19 Author
 * 20 Editor
 * 21 Publisher
 * 23 Manager
 * 24 Administrator
 * 25 Super Administrator
 * 0  Not Logged in
 */
/* Access control: View front console */
$registry->setValue('Security.Front','0');

/* Access control: View admin console */
$registry->setValue('Security.AdminConsole','25');

/* Access control: View or edit configuration pages */
$registry->setValue('Security.Admin.viewDebug','25');


/* Specific task function calls in the controller can be ACL'ed , must have x prefix*/

/* Access control: Ability to load the admin control panel */
$registry->setValue('Security.AdminController.xcpanel','25');
$registry->setValue('Security.AdminController.xlayouts','25');

/* Front end normal functions - must NOT have x prefix*/
/* Access control: Ability to View list of all forms */
$registry->setValue('Security.FrontController.viewforms','0');

/**
 * set the default xajax bfHandler's name - must be unique else many bf components will fall over each other
 * You also need to manually change this is js.js as we cant set it in JS files
 */
$registry->setValue('bfFramework_'.'tag'.'.bfHandler','bfHandler');

/* Set our default controller file and path */
$registry->setValue('bfFramework_'.'tag'.'.controller.admin', bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'controller' . DS . 'admin' . DS . 'com_tag' . '.php');
$registry->setValue('bfFramework_'.'tag'.'.controller.front', bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'controller' . DS . 'front' . DS . 'com_tag' . '.php');

/* Hidden fields to show */
$registry->setValue('bfFramework_'.'tag'.'.hidden_field_defaults', array('boxchecked' => '','hidemainmenu' => '','task' => '','total' => '','view' => '','returnto' => ''));

/* defaults for our page state */
$registry->setValue('bfFramework_'.'tag'.'.state_defaults', array('page' => '1','limit' => 10,'filter' => '') );

/**
 * Set the defaults for bfModel
 * @todo I am not sure if these are still used though - might be enforced my mysql field type
 */
$registry->setValue('bfFramework_'.'tag'.'.bfModel.defaults.published',	'0');
$registry->setValue('vbfModel.defaults.access',		'0');
$registry->setValue('bfFramework_'.'tag'.'.bfModel.defaults.checked_out',	'0');
$registry->setValue('bfFramework_'.'tag'.'.bfModel.defaults.layout',		'table');


/**
 * Configure the names of the tabs in Configuration Screen
 */
$registry->setValue('bfFramework_'.'tag'.'.config_tabs.General', 	bfText::_('General Preferences' ));
$registry->setValue('bfFramework_'.'tag'.'.config_tabs.Layout', 	bfText::_('Layouts'));
$registry->setValue('bfFramework_'.'tag'.'.config_tabs.User Submission', 	bfText::_('User Submission' ));
$registry->setValue('bfFramework_'.'tag'.'.config_tabs.Articles', 	bfText::_('Advanced Preferences' ));
$registry->setValue('vconfig_tabs.Framework', bfText::_('Framework' ));


/* CSS Files */
$registry->setValue('bfFramework_'.'tag'.'.css.front', array('Framework Frontside CSS', bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'view' . DS . 'front' . DS . 'front.css'));
$registry->setValue('bfFramework_'.'tag'.'.css.admin', array('Framework Adminside CSS', bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'view' . DS . 'admin' . DS . 'admin.css'));
$registry->setValue('component.css.front',   array('Component Frontside CSS', bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'view' . DS . 'front' . DS . 'front.css'));
$registry->setValue('component.css.admin',   array('Component Adminside CSS', bfCompat::getAbsolutePath() . DS . 'components' . DS . 'com_tag' . DS . 'view' . DS . 'admin' . DS . 'admin.css'));

/**
 * Configuraton Vars - used in building configuration pages
 * Also these set the defaults
 * And tell the configuration view which tab to place the items on
 * Items are rendered in the order they are here!
 */
$db=&bfCompat::getDBO();

$db->setQuery("SELECT * FROM #__tag_layouts"); // WHERE appliesto = 'tag'
$templates = $db->loadObjectList();
$layoutArray = array();
if (@count($templates)){
	foreach (@$templates as $template){
		$layoutArray[$template->id] = $template->title;
	}
}

$config_vars = array();

$config_vars['defaultTemplate'] = array(
'defaultTemplate',
bfText::_('Default Template'),
'string',
'selectBox',
'2',
bfText::_('Layouts'),
bfText::_('Select the default template used to display the tags details'),
$layoutArray
);

$config_vars['footerDetail'] = array(
'footerDetail',
bfText::_('Full Article Template'),
'string',
'selectBox',
'2',
bfText::_('Layouts'),
bfText::_('Select the template that will be displayed at the bottom of the full text of an article'),
$layoutArray
);

$config_vars['footerIntro'] = array(
'footerIntro',
bfText::_('Introtext Template'),
'string',
'selectBox',
'3',
bfText::_('Layouts'),
bfText::_('Select the template that will be displayed at the bottom of the introtext of an article'),
$layoutArray
);

$config_vars['order'] = array
(
'order',									// ID
bfText::_('Default Content Items Order'), 					// Text Label
'string', 								    // Input Type
'selectbox',									// Form Field Type
'1',										// Default Value
bfText::_('General Preferences'),									// Tab
bfText::_('Select the order preference for content items'),						// Tip
array(
'created'=>bfText::_('Date Created'),
'title'=>bfText::_('Content Title'),
'hits'=>bfText::_('Hits'),
'title_alias'=>bfText::_('Title Alias')
)
);

$config_vars['orderdir'] = array
(
'orderdir',									// ID
bfText::_('Default Content Items Order Direction'), 					// Text Label
'string', 								    // Input Type
'selectbox',									// Form Field Type
'ASC',										// Default Value
bfText::_('General Preferences'),									// Tab
bfText::_('Select the order direction preference for content items'),						// Tip
array('ASC'=>bfText::_('Ascending'),'DESC'=>bfText::_('Desending'))
);

$config_vars['limitperpage'] = array
(
'limitperpage',									// ID
bfText::_('Default Content Items Limit Per Page'), 					// Text Label
'string', 								    // Input Type
'textbox',									// Form Field Type
'10',										// Default Value
bfText::_('General Preferences'),									// Tab
bfText::_('Select the number of content items to display before pagination')						// Tip
);

$config_vars['popularlimit'] = array
(
'popularlimit',									// ID
bfText::_('Number Of Popular Tags To Show'), 					// Text Label
'string', 								    // Input Type
'textbox',									// Form Field Type
'10',										// Default Value
bfText::_('General Preferences'),									// Tab
bfText::_('Select the number of tags to display in the popular list')						// Tip
);

$config_vars['latestlimit'] = array
(
'latestlimit',									// ID
bfText::_('Number Of Latest Tags To Show'), 					// Text Label
'string', 								    // Input Type
'textbox',									// Form Field Type
'10',										// Default Value
bfText::_('General Preferences'),									// Tab
bfText::_('Select the number of tags to display in the latest list')						// Tip
);

$config_vars['allowfrontendsubmission'] = array
(
'allowfrontendsubmission',									// ID
bfText::_('Allow Tags to Be Submitted By Visitors'), // Text Label
'int', 								    	// Input Type
'yesnoradiolist',							// Form Field Type
'1',										// Default Value
bfText::_('User Submission'),		// Tab
bfText::_('Turn this on to allow visitors to add tags, set the permissions level in the next config value')								// Tip
);

$config_vars['frontendsubmissionaccesslevel'] = array
(
'frontendsubmissionaccesslevel',									// ID
bfText::_('User Level Allowed To Submit Tags'), // Text Label
'int', 								    	// Input Type
'selectbox',							// Form Field Type
'0',										// Default Value
bfText::_('User Submission'),		// Tab
bfText::_('This sets the permissions level that allows a user to submit tags'),
array('0'=>bfText::_('Public'),'1'=>bfText::_('Registered'),'2'=>bfText::_('Special'))							// Tip
);

$config_vars['holdformoderation'] = array
(
'holdformoderation',									// ID
bfText::_('Hold submitted tags as unpublished until moderated'), // Text Label
'int', 								    	// Input Type
'yesnoradiolist',							// Form Field Type
'1',										// Default Value
bfText::_('User Submission'),		// Tab
bfText::_('With this set to yes then all new submitted tags will be flagged as unpublished until an admin marks them as published')								// Tip
);

$config_vars['moderationemail'] = array
(
'moderationemail',									// ID
bfText::_('Email Address Of Tag Moderators'), 					// Text Label
'string', 								    // Input Type
'textbox',									// Form Field Type
'',										// Default Value
bfText::_('User Submission'),									// Tab
bfText::_('Enter a single email address to be sent a notification of submitted tags')						// Tip
);

$config_vars['emaileverytime'] = array
(
'emaileverytime',									// ID
bfText::_('Send email every time a tag is submitted'), // Text Label
'int', 								    	// Input Type
'yesnoradiolist',							// Form Field Type
'1',										// Default Value
bfText::_('User Submission'),		// Tab
bfText::_('Sends emails every time a tag is submitted, not just when held for moderation. If set to no emails will still be sent if a tag is held for moderation')								// Tip
);

$config_vars['tagcloudintegers'] = array
(
'tagcloudintegers',									// ID
bfText::_('Show quantities in the tag cloud and modules'), // Text Label
'int', 								    	// Input Type
'yesnoradiolist',							// Form Field Type
'1',										// Default Value
bfText::_('Advanced Preferences'),		// Tab
bfText::_('Show the number of hits or useage of a tag in the tag cloud')								// Tip
);

$config_vars['showonfrontpage'] = array
(
'showonfrontpage',									// ID
bfText::_('Show tagging interfaces on the frontpage'), // Text Label
'int', 								    	// Input Type
'yesnoradiolist',							// Form Field Type
'1',										// Default Value
bfText::_('Advanced Preferences'),		// Tab
bfText::_('Toggles showing the tag footer and tags on content introtexts when option=com_frontpage')								// Tip
);

$config_vars['showonintrotext'] = array
(
'showonintrotext',									// ID
bfText::_('Show tagging interfaces on the introtext'), // Text Label
'int', 								    	// Input Type
'yesnoradiolist',							// Form Field Type
'1',										// Default Value
bfText::_('Advanced Preferences'),		// Tab
bfText::_('Toggles showing the tag footer and tags on content introtexts')								// Tip
);

$config_vars['showsocialbookmarks'] = array
(
'showsocialbookmarks',									// ID
bfText::_('Display the links to social bookmark sites'), // Text Label
'int', 								    	// Input Type
'yesnoradiolist',							// Form Field Type
'1',										// Default Value
bfText::_('Advanced Preferences'),		// Tab
bfText::_('Show the icons and links to allow visitors to bookmark your content with social bookmarking sites')							// Tip
);

$config_vars['technorati'] = array
(
'technorati',									// ID
bfText::_('Display the links to Technorati.com'), // Text Label
'int', 								    	// Input Type
'yesnoradiolist',							// Form Field Type
'1',										// Default Value
bfText::_('Advanced Preferences'),		// Tab
bfText::_('Show the icons and links to allow visitors to search technorati.com')							// Tip
);

$config_vars['footerlocation'] = array
(
'footerlocation',									// ID
bfText::_('Display Tags at top or bottom of article'), // Text Label
'int', 								    	// Input Type
'selectbox',							// Form Field Type
'bottom',										// Default Value
bfText::_('Advanced Preferences'),		// Tab
bfText::_('Where would you like the tags list to be shown'),
array('top'=>bfText::_('Top'),'bottom'=>bfText::_('Bottom'))							// Tip
);


$config_vars['newacceslevel'] = array
(
'newacceslevel',									// ID
bfText::_('Access level all new tags get by default'), // Text Label
'int', 								    	// Input Type
'selectbox',							// Form Field Type
'0',										// Default Value
bfText::_('Advanced Preferences'),		// Tab
bfText::_('This sets the permissions level that all new tags get'),
array('0'=>bfText::_('Public'),'1'=>bfText::_('Registered'),'2'=>bfText::_('Special'))							// Tip
);

$config_vars['globalenablerss'] = array
(
'globalenablerss',									// ID
bfText::_('test item one'), 					// Text Label
'string', 								    // Input Type
'textbox',									// Form Field Type
'Yes',										// Default Value
'RSS Feeds',									// Tab
bfText::_('TEST TIP')						// Tip
);

$config_vars['bfCachingEnabled'] = array
(
'bfCachingEnabled',                                                                  // ID
bfText::_('Turn on Object Caching'), // Text Label
'int',                                                                        // Input Type
'yesnoradiolist',                                                     // Form Field Type
'1',                                                                          // Default Value
'Framework',                                                                      // Tab
bfText::_('By turning this on SQL queries will be cached.') // Tip
);

$config_vars['cachetime'] = array
(
'cachetime',								// 0 ID
bfText::_('Cache Lifetime'), 	// 1 Text Label
'string', 								    // 2 Input Type
'textbox',									// 3 Form Field Type
'6000',								// 4 Default Value
'Framework',									// 5 Tab
bfText::_('The time in seconds before purging cache bfCache Ojects')// 6 Tip
);

$config_vars['langDebug'] = array
(
'langDebug',                                                                  // ID
bfText::_('Turn on language debugging'), // Text Label
'int',                                                                        // Input Type
'yesnoradiolist',                                                     // Form Field Type
'1',                                                                          // Default Value
'Framework',                                                                      // Tab
bfText::_('By turning this on text will be surrounded by *s if the text has no translation and Ts if translated.') // Tip
);

$config_vars['devLog'] = array
(
'devLog',									// ID
bfText::_('Turn on Debug logging to file '), // Text Label
'int', 								    	// Input Type
'yesnoradiolist',							// Form Field Type
'1',										// Default Value
'Framework',									// Tab
bfText::_('By turning this on some developers debug information will be logged to a file, DO NOT leave this on too long as the file will become HUGE!')								// Tip
);


$config_vars['devLogFile'] = array
(
'devLogFile',								// 0 ID
bfText::_('File path and name to log to'), 	// 1 Text Label
'string', 								    // 2 Input Type
'textbox',									// 3 Form Field Type
'/tmp/bfLog',								// 4 Default Value
'Framework',									// 5 Tab
bfText::_('This should be the absolute path to a file name, E.g. /var/www/html/myLog.txt')// 6 Tip
);

/*
$config_vars['updateCheck'] = array
(
'updateCheck',										// 0 ID
bfText::_('Automatically check for newer verson'), 	// 1 Text Label
'int', 								   				// 2 Input Type
'yesnoradiolist',									// 3 Form Field Type
'1',												// 4 Default Value
'Debug',											// 5 Tab
bfText::_('When a newer version is found a warning will be displayed. This features needs access to the internet from this server and file_get_contents() function not disabled in PHP.ini')// 6 Tip
);
*/
$registry->setValue('bfFramework_'.'tag'.'.config_vars',$config_vars);
?>
