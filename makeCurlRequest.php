<?php
 
function makeCurlRequest($variables,$authData)
{
	
	# appel SPIP
	$_POST = array_merge($variables, $authData);
	include('spip.php');
}

 
?>