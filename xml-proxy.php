<?php
	// based on PHP Proxy example for Yahoo! Web services. 
	//$url = str_replace(' ','%20', $_GET['url']);

	$url =  lindecrypt(hexstr($_GET['url']));
	
	// function linencrypt($pass) {
		// $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB); //get vector size on ECB mode 
		// $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); //Creating the vector
		// $cryptedpass = mcrypt_encrypt (MCRYPT_RIJNDAEL_256, "nYj4mJXeU1MPzAuwlzodiH", $pass, MCRYPT_MODE_ECB, $iv); //Encrypting using MCRYPT_RIJNDAEL_256 algorithm 
		// return $cryptedpass;
	// }
	
	// function strhex($string) {
		// $hexstr = unpack('H*', $string);
		// return array_shift($hexstr);
	// }
	
	function lindecrypt($enpass) {
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB); 
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$decryptedpass = mcrypt_decrypt (MCRYPT_RIJNDAEL_256, "nYj4mJXeU1MPzAuwlzodiH", $enpass, MCRYPT_MODE_ECB, $iv); //Decrypting...
		return rtrim($decryptedpass);
	}
	
	function hexstr($hexstr) {
		$hexstr = str_replace(' ', '', $hexstr);
		$hexstr = str_replace('\x', '', $hexstr);
		$retstr = pack('H*', $hexstr);
		return $retstr;
	}

	// exit if not an xml file
	if (array_pop(explode(".", $url)) <> 'xml') exit;
	
	// Open the Curl session
	$session = curl_init($url);
	
	// Don't return HTTP headers. Do return the contents of the call
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	
	// Make the call
	$xml = curl_exec($session);
	
	// The web service returns XML. Set the Content-Type appropriately
	header("Content-Type: text/xml");
	
	echo $xml;
	curl_close($session);

?>