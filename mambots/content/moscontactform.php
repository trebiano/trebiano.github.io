<?php
/**
* @version $Id: moscontactform.php 3100 2006-04-12 13:35:56Z ndtreviv $
* @package Joomla
* @copyright Copyright (C) 2007 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

$_MAMBOTS->registerFunction( 'onPrepareContent', 'botcontactform' );


function botcontactform( $published, &$row, &$params, $page=0 ) {
global $mosConfig_absolute_path;
	// define the regular expression for the bot
	$regex = "#{contactform}(.*?){/contactform}#s";

	if (!$published) {
		$row->text = preg_replace( $regex, '', $row->text );
		return;
	}


	// perform the replacement
	$row->text = preg_replace_callback( $regex, 'botcontactform_replacer', $row->text );

	return true;
}

function botcontactform_replacer ( &$matches ) {
	global $mosConfig_absolute_path;
	global $mosConfig_live_site;
	$submit_path = $mosConfig_live_site."/mambots/content/moscontactform/";
	$form_html = "";
	
	$form_html .= "<script language='javascript' type='text/javascript' src='".$mosConfig_live_site."/mambots/content/moscontactform/prototype.js'></script>\n";
	$form_html .= "<script language='javascript' type='text/javascript' src='".$mosConfig_live_site."/mambots/content/moscontactform/effects.js'></script>\n";
	$form_html .= "<script language='javascript' type='text/javascript' src='".$mosConfig_live_site."/mambots/content/moscontactform/moscontactform.js'></script>\n";
	$form_html .= "<div id='moscontactformholder'>\n";
	$form_html .= "<form action='' id='moscontactform' onSubmit='try{mosContactFormSubmit(".'"'.$submit_path.'"'.");}catch(e){alert(e);} return false;'>\n";
	$form_html .= "<div class='contact_form_item'><div class='contact_form_label'>Nome:</div><div class='contact_form_field'><input type='text' id='moscontactform_name' size='30' /></div></div>\n";
	$form_html .= "<div class='contact_form_item'><div class='contact_form_label'>Email:</div><div class='contact_form_field'><input type='text' id='moscontactform_email' size='30' /></div></div>\n";
	$form_html .= "<div class='contact_form_item'><div class='contact_form_label'>Messaggio:</div><div class='contact_form_field'><textarea id='moscontactform_text' cols='32' rows='8'></textarea></div></div>\n";
	$form_html .= "<div class='contact_form_clear'></div>";
	$form_html .= "<div class='contact_form_item'><div class='contact_form_label'><input type='submit' class='contact_form_submit' value='Invia' /></div></div>\n";
	$form_html .= "<input type='hidden' id='moscontactform_contact' value='".$matches[1]."' />";
	$form_html .= "</form>\n";	
	$form_html .= "</div>\n";	
	
	return $form_html;
}
?>
