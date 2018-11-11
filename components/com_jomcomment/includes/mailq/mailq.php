<?php
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class JCMailQueue 
{
	var $_tablename;	# table name of the mail Q
	var $_maxburst;		# maximum email to send per session

	function JCMailQueue(){
		$this->_tablename 	= "jomcomment_mailq";
		$this->_maxburst	= 10;
	}
	
	# Add the given email to mailQ. Mail queue will simply add it to its q list
	# table and make NO attempt to deliver it
	# @todo: validate the email		
	function mail($email, $subject, $body, $mode=0){
		global $database;
		
		$sql = "INSERT INTO #__$this->_tablename SET `content`='$body', `email`='$email', `subject`='$subject'";
		$database->setQuery($sql);
		$database->query();
		
	}
	
	# Read from the queue, and send out the email, '$_maxburst' number
	# of email at most
	function send(){
		global $database;
		global $mosConfig_mailfrom, $mosConfig_fromname;
		
		$sql = "SELECT * FROM #__$this->_tablename WHERE status='0' LIMIT 0 , $this->_maxburst";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();
		
		if($rows)
		foreach($rows as $row){
			$this->_mark_as_read();			
			mosMail($mosConfig_mailfrom, $mosConfig_fromname, $row->email, $row-subject, $row->body); 
		}
		
		# Purge data older than 7-days
	}
	
	function _mark_as_read($id){
		global $id;
		
		$sql = "UPDATE #__$this->_tablename SET status='1' WHERE id='$id'";
		$database->setQuery($sql);
	}
	
	
}

?>
