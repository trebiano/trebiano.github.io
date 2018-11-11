<?xml version="1.0" encoding="iso-8859-1"?>
<mosinstall version="1.0.0" type="mambot" group="system">
	<name>XAJAX System Mambot For Joomla</name>
	<author>Blue Flame IT Ltd</author>
	<creationDate>April 2007</creationDate>
	<copyright>(C) 2006 Blue Flame IT Ltd (Phil Taylor). All rights reserved.</copyright>
	<license>http://www.gnu.org/copyleft/gpl.html GNU/GPL</license>
	<authorEmail>phil@phil-taylor.com</authorEmail>
	<authorUrl>www.phil-taylor.com</authorUrl>
	<version>0.3</version>
	<description><![CDATA[
	Blue Flame IT Ltd provides this mambot to ease 3PD development<br />
	See http://www.xajaxproject.org for more details
	]]></description>
	<files>
		<filename mambot="xajax.system">xajax.system.php</filename>
		<filename>xajax_0.2.4/xajax.inc.php</filename>
		<filename>xajax_0.2.4/xajaxCompress.php</filename>
		<filename>xajax_0.2.4/xajaxResponse.inc.php</filename>
		<filename>xajax_0.2.4/xajax_js/xajax.js</filename>
		<filename>xajax_0.2.4/xajax_js/xajax_uncompressed.js</filename>
	</files>
	<params>
		<param name="debug" type="radio" default="0" label="Debug Mode" description="enable xAJAX Debug Mode">
 			<option value="0">Off</option>
 			<option value="1">On</option>
 		</param>
 		<param name="statusMessagesOn" type="radio" default="0" label="Status Messages" description="show status messages">
 			<option value="0">Off</option>
 			<option value="1">On</option>
 		</param>
 		<param name="waitCursorOn" type="radio" default="0" label="Wait Cursor" description="Display Wait cursor">
 			<option value="0">Off</option>
 			<option value="1">On</option>
 		</param>
 		<param name="decodeUTF8" type="radio" default="1" label="Decode UTF-8 (Experimental)" description="Decode UTF-8 (Experimental)">
 			<option value="0">Off</option>
 			<option value="1">On</option>
 		</param>
 		<param name="encoding" type="text" default="UTF-8" size="10" label="Encoding" description="Char set" />
	</params>
</mosinstall>
