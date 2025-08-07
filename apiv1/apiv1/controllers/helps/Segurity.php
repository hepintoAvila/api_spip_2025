<?php
/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/ 

/**
 * Make a cURL request with given variables and authentication
 *
 * @param array $variables
 * @param string $refer
 * @param int $timeout
 * @param array $header
 * @return mixed
 */
function decryptData($encryptedData, $secretKey) {
    $method = 'AES-256-CBC';
    $ivLength = openssl_cipher_iv_length($method);

    // Verifica si el delimitador '::' está presente en $encryptedData
    if (strpos($encryptedData, '::') === false) {
        throw new Exception('Invalid encrypted data format.');
    }

    // Divide el $encryptedData usando el delimitador '::'
    list($encryptedData, $iv) = explode('::', $encryptedData, 2);

    // Asegúrate de que tanto $encryptedData como $iv estén definidos
    if (!isset($encryptedData) || !isset($iv)) {
        throw new Exception('Invalid encrypted data format.');
    }

    // Decodifica el IV desde base64
    $iv = base64_decode($iv);

    // Verifica si el IV tiene la longitud correcta
    if (strlen($iv) !== $ivLength) {
        throw new Exception('Invalid IV length.');
    }

    // Desencripta los datos
    $decryptedData = openssl_decrypt($encryptedData, $method, $secretKey, 0, $iv);

    // Verifica si la desencriptación fue exitosa
    if ($decryptedData === false) {
        throw new Exception('Decryption failed.');
    }

    return $decryptedData;
}

function obtenerPass($encryptedData,$secretKey){
    try {
        $decryptedData = decryptData($encryptedData, $secretKey);
            return $decryptedData;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
     
    }
function verificarPassword($password, $storedPass, $storedAlea) {
    $pass = spip_sha256($storedAlea . $password);
	return $pass === $storedPass;
}

/**
 * Main routine called from an application using this include.
 *
 * General usage:
 *   require_once('sha256.inc.php');
 *   $hashstr = spip_sha256('abc');
 *
 * @param string $str Chaîne dont on veut calculer le SHA
 * @return string Le SHA de la chaîne
 */
function spip_sha256($str) {
	return hash('sha256', $str);
}

/**
 * @param string $str Chaîne dont on veut calculer le SHA
 * @param bool $ig_func
 * @return string Le SHA de la chaîne
 * @deprecated 4.0
 * @see spip_sha256()
 */
function _nano_sha256($str, $ig_func = true) {
	return spip_sha256($str);
}
	
    ?>