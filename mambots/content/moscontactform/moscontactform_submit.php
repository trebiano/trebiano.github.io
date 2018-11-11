<?php
define('_VALID_MOS', 1);
header("Content-type: text/xml");

DEFINE('_NO_NAME_ERROR','Non hai inserito alcun nome! Pertanto i dati del form non sono stati inviati.');
DEFINE('_NO_EMAIL_ERROR', 'Non hai inserito un recapito e-mail valido! Pertanto i dati del form non sono stati inviati.');
DEFINE('_NO_TEXT_ERROR', 'Non hai inserito alcun messaggio! Pertanto i dati del form non sono stati inviati.');
DEFINE('_THANK_YOU_MESSAGE', 'Ti ringraziamo per averci contattato! Riceverai presto una risposta con informazioni riguardanti le richieste che ci hai inoltrato. Cordiali Saluti dallo Staff di Trebiano E-Business Partner');

	$xml = "";

	if(!isset($_GET['name'])){
		$xml .= "<moscontactform><message>"._NO_NAME_ERROR."</message></moscontactform>";
	}
	elseif(!isset($_GET['email'])){
		$xml .= "<moscontactform><message>"._NO_EMAIL_ERROR."</message></moscontactform>";
	}
	elseif(!isset($_GET['text'])){
		$xml .= "<moscontactform><message>"._NO_TEXT_ERROR."</message></moscontactform>";
	}
	else  {
		require_once('../../../configuration.php');
		require_once($mosConfig_absolute_path . '/language/italian.php');
		require_once($mosConfig_absolute_path . '/includes/joomla.php');

		global $mainframe, $database, $Itemid;
		global $mosConfig_sitename, $mosConfig_live_site, $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_db;
		
		$name = $_GET['name'];
		$email = $_GET['email'];
		$text = $_GET['text'];
		$contact = '';
		if(isset($_GET['contact'])){
			$contact = $_GET['contact'];
		}


		$prefix = sprintf( _ENQUIRY_TEXT, $mosConfig_live_site );
		$text 	= $prefix ."\n". $name. ' <'. $email .'>' ."\n\n". stripslashes( $text );		

		//$debug = '';

		$send_to = getEmailFromGroup($contact);
		//Send email to necessary people
		if($send_to['email'] == "N/A"){
			//$debug .= "<debug na=\"true\"><name>".$send_to['name']."</name><email>".$send_to['email']."</email><sql>".$send_to['sql']."</sql></debug>";
			mosMail( $email, $name , $mosConfig_mailfrom , $mosConfig_fromname .': '. "Contact Form", $text );
		} else {
			//$debug .= "<debug><name>".$send_to['name']."</name><email>".$send_to['email']."</email><sql>".$send_to['sql']."</sql></debug>";
			mosMail( $email, $name , $send_to['email'] , $send_to['name'] .': '. "Contact Form", $text );			
		}
		mosMail( $mosConfig_mailfrom, $mosConfig_fromname , $email , "Conferma Richiesta Informazioni", _THANK_YOU_MESSAGE );


		$xml .= "<moscontactform><message>"._THANK_YOU_MESSAGE."</message></moscontactform>";
	}
	
	echo $xml;

	function getEmailFromGroup($contact=''){
		global $mosConfig_mailfrom, $mosConfig_fromname, $database;
		
		$details = array();
		
		if(strcmp($contact, '') == 0){
			$details['email'] = $mosConfig_mailfrom;
			$details['name'] = $mosConfig_fromname;
			return $details;
		}
		
		$sql = "SELECT * FROM #__contact_details WHERE name = '".$contact."'";
		//$details['sql'] = $sql;
		$database->setQuery($sql);
		$rows = $database->LoadObjectList();
		if(count($rows) < 1){
			$details['email'] = "N/A";
			$details['name'] = "N/A";
			return $details;
		}
		
		$details['name'] = $rows[0]->name;
		$details['email'] = $rows[0]->email_to;
		
		return $details;
		
	}
?>
<body>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-2560711-1";
urchinTracker();
</script>
</body>