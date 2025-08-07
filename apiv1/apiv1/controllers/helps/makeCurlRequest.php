<?php
// makeCurlRequest.php
/**
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/
require_once 'config.php';

 function makeCurlRequest($variables,  $data, $refer = "", $timeout = 10, $header = []) {
    $url = Config::$url_api;
	//error_log("Valor de url: " . $url);
    $POSTFIELDS = array_merge($variables, $data);
	
    
    $ch = curl_init();
    $ssl = stripos($url, 'http://') === 0;
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_USERPWD => $data['var_login'] . ':' . $data['password'],
        CURLOPT_POSTFIELDS => $POSTFIELDS,
        CURLOPT_FOLLOWLOCATION =>1,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_AUTOREFERER => 1,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        CURLOPT_HTTPHEADER => array_merge(['Expect:'], $header),
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_REFERER => $refer,
		
    ];

    if ($ssl) {
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
    }
    // Crea un array con las variables que deseas verificar
    curl_setopt_array($ch, $options);

    $returnData = curl_exec($ch);

    if (curl_errno($ch)) {
        $returnData = curl_error($ch);
    }

    curl_close($ch);
    return $returnData;
}
 
?>