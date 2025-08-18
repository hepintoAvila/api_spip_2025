<?php
// makeCurlRequestImport.php
/**
 * @About:      API Interface
 * @File:       makeCurlRequestImport.php
 * @Date:       febrero-2022
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo
 **/
 require_once 'config.php';
function makeCurlRequestImport($payload, $authData) {
    $header = [];
    $refer = '';
	$url = Config::$url_api;
    $ch = curl_init();
    $ssl = stripos($url, 'http://') === 0;

    // Configuración básica de CURL
    $options = [
        CURLOPT_URL =>  $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => http_build_query($payload),
        CURLOPT_USERPWD => $authData['var_login'] . ':' . $authData['password'],
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
        CURLOPT_HTTPHEADER => array_merge(['Expect:'], $header),
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        CURLOPT_FOLLOWLOCATION =>1,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_AUTOREFERER => 1,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_REFERER => $refer,
        CURLOPT_FAILONERROR => true
    ];

    if ($ssl) {
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
    }

    curl_setopt_array($ch, $options);

    $returnData = curl_exec($ch);

    if (curl_errno($ch)) {
        $returnData = curl_error($ch);
    }

    curl_close($ch);
    return $returnData;
}
?>