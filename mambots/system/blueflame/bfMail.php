<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * Class for sending mail with debug set to maximum
 *
 * 
 * @version $Id: bfMail.php 1084 2007-07-12 16:31:44Z phil $
 * @package #PACKAGE#
 * @subpackage #SUBPACKAGE#
 * @copyright Copyright (C) 2006 Blue Flame IT Ltd. All rights reserved.
 * @license Commercial
 * @link http://www.blueflameit.ltd.uk
 * @author Blue Flame IT Ltd.
 */

class bfMail {

	function bfMail( $from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
		global $mosConfig_debug, $mainframe;
		
		/* stop joomla 1012 having fits */
		if (!defined('_ISO')) define('_ISO','charset=iso-8859-1');

		/* Sanity check */
		if (!$from)	die('Could not send email as the FROM was not set!');
		if (!$fromname)	die('Could not send email as the FROMNAME was not set!');
		if (!$subject)	die('Could not send email as the SUBJECT was not set!');
		if (!$body)	die('Could not send email as the BODY was not set!');

		$mail = mosCreateMail( $from, $fromname, $subject, $body );

		if ($mainframe->getCfg('mailer')=='sendmail'){
			if (!file_exists($mainframe->getCfg('sendmail'))){
				ob_clean();
				die('Your Joomla Global Configuration is set to use Sendmail, but no sendmail was found on your server at: ' . $mainframe->getCfg('sendmail') );
			}
		}
		// activate HTML formatted emails
		if ( $mode ) {
			$mail->IsHTML(true);
		}

		if (!$recipient) die('Could not send email as the recipient was not set!');
		if (is_array( $recipient )) {
			foreach ($recipient as $to) {
				$mail->AddAddress( $to );
			}
		} else {
			$mail->AddAddress( $recipient );
		}

		if (isset( $cc )) {
			if (is_array( $cc )) {
				foreach ($cc as $to) {
					$mail->AddCC($to);
				}
			} else {
				$mail->AddCC($cc);
			}
		}

		if (isset( $bcc )) {
			if (is_array( $bcc )) {
				foreach ($bcc as $to) {
					$mail->AddBCC( $to );
				}
			} else {
				$mail->AddBCC( $bcc );
			}
		}

		if ($attachment) {
			if (is_array( $attachment )) {
				foreach ($attachment as $fname) {
					$mail->AddAttachment( $fname );
				}
			} else {
				$mail->AddAttachment($attachment);
			}
		}

		//Important for being able to use mosMail without spoofing...
		if ($replyto) {
			if (is_array( $replyto )) {
				reset( $replytoname );
				foreach ($replyto as $to) {
					$toname = ((list( $key, $value ) = each( $replytoname )) ? $value : '');
					$mail->AddReplyTo( $to, $toname );
				}
			} else {
				$mail->AddReplyTo($replyto, $replytoname);
			}
		}

		$mailssend = $mail->Send();

		if( $mailssend =='0' || $mailssend == false ) {
			//echo "<h1>The email was NOT sent!</h1>";
		}
		if( $mail->error_count > 0 ) {
		/*	echo "<blockquote style='border: 2px solid red;'><h1 style=\"color:red\">Error!</h1><br/><br/>The mail message<b> $fromname <$from></b> about <b>$subject</b> to <b>$recipient</b> <b>failed</b><br /><br />";
			echo "The Mailer Error Was : <b>" . $mail->ErrorInfo . "</b><br /><br />" ;
			echo "Trying to use mail function: <b>" .$GLOBALS['mosConfig_mailer'] . '</b><br /><br />';
			echo "The from Name (which should be set) was: <b>" . $fromname . '</b><br /><br />';
			echo "The from Email (which should be set, and should be a valid email address) was: " . $from . '<br /><br />';
			echo "<!-- <pre>";
			unset($mail->language);
			$mail->Password = 'Removed by debugger as security precaution!'; 
			$mail->PluginDir = 'Removed by debugger as security precaution!'; 
			print_R($mail);
			echo "</pre> --></blockquote>";
			echo "<h2>If you are the site administrator</h2><p>... and you need assistance in debuging email sending issues then please use <a target=\"_blank\" href=\"http://forum.phil-taylor.com/index.php/board,15.0.html\">Blue Flame (Phil-A-Form) Support Forums</a>.</p>";
			*/
		} else {
			//echo "<!-- eMail accepted by the mail server... -->";
		}
		return $mailssend;
	}
}
?>