<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class Sms {
	
	private $username = "nayab"; // username lab9541
	private $secret_key = "660712eda43c7f7b5b6c2021351db92e"; // 32 characters secret key, get it from api page
	private $smsurl = "http://websms.maaliksoft.com:80/api/sendsms.php?"; // change websmsdomain.com to your provided 
	
	public function websmsSend($phone, $msg, $debug=false){		  
		  $username = $this->username;
		  $secret_key = $this->secret_key;
		  $smsurl = $this->smsurl;
	
		  $url = 'username='.$username;
		  $url.= '&secret='.$secret_key;
		  $url.= '&to='.urlencode($phone);
		  $url.= '&text='.urlencode($msg);
	
		  $urltouse =  $smsurl.$url;
		  if ($debug) { //echo "Request: <br>$urltouse<br><br>"; 
		  }
	
		  //Open the URL to send the message
		  $response = $this->actionhttpRequest($urltouse);
		  if ($debug) {
			  /*  echo "Response: <br><pre>".
			   str_replace(array("<",">"),array("&lt;","&gt;"),$response).
			   "</pre><br>"; */			   
			   }	
		  return($response);
	}
/////////////////////////////////////////////
	public function actionhttpRequest($url){
		$pattern = "/http...([0-9a-zA-Z-.]*).([0-9]*).(.*)/";
		preg_match($pattern,$url,$args);
		$in = "";
		$fp = fsockopen("$args[1]", $args[2], $errno, $errstr, 30);
		if (!$fp) {
		   return("$errstr ($errno)");
		} else {
			$out = "GET /$args[3] HTTP/1.1\r\n";
			$out .= "Host: $args[1]:$args[2]\r\n";
			$out .= "User-agent: PHP Web SMS client\r\n";
			$out .= "Accept: */*\r\n";
			$out .= "Connection: Close\r\n\r\n";
	
			fwrite($fp, $out);
			while (!feof($fp)) {
			   $in.=fgets($fp, 128);
			}
		}
		fclose($fp);
		return($in);
	}
	///////////////////////////////////////////
}
