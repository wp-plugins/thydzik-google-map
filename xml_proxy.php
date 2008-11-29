<?php
	// based on PHP Proxy example for Yahoo! Web services. 
	$url = str_replace(' ','%20', $_GET['url']);

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