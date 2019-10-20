<?php	/*

	letolti az email mellekleteket egy imap fiokbol
		Zsombor, 2017-10
	
	Az $iterator-nak egy fuggvenyt kell megadni, ami 
	elso parameterkent a fajl nevet kapja meg,
	masodik parameterkent pedig a nyers fajltartalom adatot
	
Peldaul:

	zs\net\emailattachment::pulldata("{mail/pop3/novalidate-cert}", IMAP_USER, IMAP_PASS,function($name,$data)use(&$kulcsok){
	  printf("%s: %d byte\n", $name, strlen($data) );
	});

	ha itertaor FALSE ertekkel ter visssza, az uzenetek feldolgozasa nem folytatodik.
	
*/


namespace zs\net;

# Coded By Jijo Last Update Date [Jan/19/06] (http://www.phpclasses.org/browse/package/2964.html)
# Updated 2008-12-18 by Dustin Davis (http://nerdydork.com)
	# Added delete_emails parameter


	
abstract class emailattachment {

	function pulldata($host,$login,$password,$iterator,$delete_emails=false) {
		// make sure save path has trailing slash (/)
		
		if(!is_callable("\\imap_open"))throw new \Exception("php-imap nincs telepítve");
		
		$mbox = \imap_open ($host, $login, $password, 0, 0, array('DISABLE_AUTHENTICATOR' => 'GSSAPI')) or die("can't connect: " . \imap_last_error());
		$message = array();
		$message["attachment"]["type"][0] = "text";
		$message["attachment"]["type"][1] = "multipart";
		$message["attachment"]["type"][2] = "message";
		$message["attachment"]["type"][3] = "application";
		$message["attachment"]["type"][4] = "audio";
		$message["attachment"]["type"][5] = "image";
		$message["attachment"]["type"][6] = "video";
		$message["attachment"]["type"][7] = "other";
		
		for ($jk = 1; $jk <= \imap_num_msg($mbox); $jk++) {
			if(!$structure = @\imap_fetchstructure($mbox, $jk )){
				# echo "sorszam: $jk\n";
			}
			$parts = $structure->parts;
			$fpos=2;
			
			for($i = 1; $i < count($parts); $i++) {
				$message["pid"][$i] = ($i);
				$part = $parts[$i];
				
				if(strtolower($part->disposition) == "attachment") {
					$message["type"][$i] = $message["attachment"]["type"][$part->type] . "/" . strtolower($part->subtype);
					$message["subtype"][$i] = strtolower($part->subtype);
					$ext=$part->subtype;
					$params = $part->dparameters;
					$filename=$part->dparameters[0]->value;
					
					$mege="";
					$data="";
					$mege = @\imap_fetchbody($mbox,$jk,$fpos);  
					$filename="$filename";
					$data = @self::getdecodevalue($mege,$part->type);	
					$iterator_value = $iterator($filename, $data);
					$fpos+=1;
				}
			}
			if ($delete_emails) \imap_delete($mbox,$jk);
			if(false===@$iterator_value)break;
		}
		// \imap_expunge deletes all tagged messages
		if ($delete_emails) \imap_expunge($mbox);
		\imap_close($mbox);
	}



	private function getdecodevalue(&$message,$coding) {
		switch($coding) {
			case 0:
			case 1:
				$message = @\imap_8bit($message);
				break;
			case 2:
				$message = @\imap_binary($message);
				break;
			case 3:
			case 5:
				$message = @\imap_base64($message);
				break;
			case 4:
				$message = @\imap_qprint($message);
				break;
		}
		return $message;
	}


}