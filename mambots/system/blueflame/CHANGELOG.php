<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version $Id: CHANGELOG.php 827 2007-06-12 18:03:41Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */
?>

1. Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
this component, including beta and release candidate versions.
Our thanks to all those people who've contributed bug reports and
code fixes.

2. Legend
---------
* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

== 8 Jun 2007 ==
	! Joomla Tags v0.1.801 released with bfFramework v0.20!
	Many MAJOR FRAMEWORK changes in order to alow multiple bf components on a page!!

== 5 Jun 2007 ==
	! Joomla Tags v0.1.737 released with bfFramework v0.14 !
	Many changes!!
	
== 4 June 2007 ==
	! Joomla Tags v0.1.720 released with bfFramework v0.12 !
	REQUIRES XAJAX UPGRADE - just delete the mambot and the component will reinstall newer version

== 29 May 2007 ==
 ^ Upgraded mootools to v1.1

== 28 May 2007 ==
 ! Joomla Tags v0.1.667 released with bfFramework v0.10 !
 # PT: XML for mambots installed now
 + PT: Allow basic file editing
 ^ PT: Allow for CaSe Unsensitive urls in queries
 
== 22 May 2007 ==
 # PT: Fixed: foreach error bfAdminEntry

== 21 May 2007 ==
 ! Joomla Tags v0.1.615 released with bfFramework v0.9 !

== 21 May 2007 ==
 + PT: Added beta Joom!fish 1.7 Support (Beta)
 + PT: Added OpenSEF RC2 Support (Beta)

== 18 May 2007 ==
 + PT: Patch Jpromoter Session Management
 # PT: Mysql 4.0 compatibility
 + PT: Added check to sefencode if running SEF Advance

== 17 May 2004 ==
 ! Joomla Tags v0.1.586 released with bfFramework v0.8 !
 
== 16 May 2004 ==
 ! REQUIRES LATEST XAJAX TO BE UPDATED
 ^ PT: Change JPATH_BASE to _BF_JPATH_BASE to prevent conflict with Fireboard component
 #+^ PT: Add bfCheck for SMF Bridge and provide patch for their system mambot
 + PT: Check for Site online
 + PT: Version update check
 
== 14 May 2007 ==
! Joomla Tags v0.1.574 Released with bfFramwwork v0.7!
 + PT: Added captcha and akismet libs for future development
 ^ PT: Char Encoding changes
 # PT: frontend session management fixed
  
== 11 May 2007 ==
 ^ PT: Enabled bfCache by default!
 + PT: Added caching to bfText
 + PT: Enabled SQL Query caching

== 11 May 2007 ==
 ! bfFramework 0.5 Released with Joomla Tags v0.1.543/545/548 !
 + PT: Mysql Set Names UTF-8 toggle added - see: http://forum.phil-taylor.com/index.php/topic,1374.msg4593.html
 ^ PT: Ensure that bfUTF8 only parses valid utf-8 strings
 + PT: Better support for SEF Advance

== 10 May 2007 ==
 ! bfFramework 0.4 Released with Joomla Tags 1.0.518 !
 + PT: SVN Add Props
 # PT: bfSmarty creating folders and error messages
 # PT: Fix Page Title rendering
 + PT: Additional checking for xAJAX old version
 + PT: Additional checking for OpenSEF Compatibility
 
== 09 May 2007 ==
 ! bfFramework 0.3 Released with Joomla Tags 1.0.424 !