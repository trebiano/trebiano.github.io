<?php
/* ----- CONFIGURATIONS -----
Used for Mambo 4.5.0 only!

If you are using Mambo 4.5.1 or above or Joomla! 1.0 or above
all settings are done in administrator panel.
You don't need to edit anything here.
*/

// content strings
$_SEF_BLOGSECTION = "blogsection";
$_SEF_BLOGCATEGORY = "blogcategory";
$_SEF_ARCHIVESECTION = "archivesection";
$_SEF_ARCHIVECATEGORY = "archivecategory";
// component strings
$_SEF_FRONTPAGE = "frontpage";
$_SEF_WEBLINKS = "weblinks";
$_SEF_POLL = "poll";
$_SEF_BANNERS = "banners";
$_SEF_CONTACT = "contact";
$_SEF_LOGIN = "login";
$_SEF_REGISTRATION = "registration";
$_SEF_SEARCH = "search";
$_SEF_NEWSFEEDS = "newsfeeds";
$_SEF_WRAPPER = "wrapper";
// custom components with extension
$custom_comp = array(
	"customcomp" => "cc"
);
// URL settings
$sef_enabled = 1;					// turn SEF Advance on / off

$_SEF_SPACE = "-";					// divide words with dashes
									// can be changed to an underscore "_"
$sufix = "";						// specify a sufix like .html
									// leave empty for directory like URL
$longurl = 1;						// 0 shorter URLs (by title)
									// 1 longer URLs (by name)
$lowercase = 0;						// 1 URL in lower case
									// 0 no change
$homeroot = 1;						// Home menu URL preferance
									// 0 Home links to "frontpage/"
									// 1 Home links to root
$uniqitem = 0;						// append creation date and id
									// to make items more unique
									// and Google news friendly
$sef_fish = 0;						// Mambelfish support allows
									// you to access multilingual
									// content via URL
$sef_bird = 1;						// Redirect requests for built-in SEF URLs
									// to corresponding SEF Advance URLs
$sef_nsrd = 0;						// Redirect requests for non-SEF URLs
									// to corresponding SEF Advance URLs

// custom 404 page
$custom404 = "";					// specify a custom 404 not found page
									// leave empty for homepage

// url replace
									// specify the special chars for replace rather than url encode
$url_replace = array(
	"å" => "aa"
);
									// specify the chars you want excepted from url encode function
$url_exception = array("?","!");
									// specify the components you want excepted from url rewrite
$com_exception = array("com_something");
									// specify the alias pairs
$url_alias = array(
	"index.php?option=com_content&task=view&id=4&Itemid=9" => "Newsflash_3"
);

$sef_cache = 0;						// use cache

$sef_cachetime = 86400;				// cache time in seconds

$sef_debug = 0;						// debug the site

$sef_debugip = '';					// IP address to restrict the debug output to
?>