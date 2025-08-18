<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2017                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/



if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
include_spip('ecrire/classes/classgeneral');

abstract class PagesSecurity {
    protected $general;

    public function __construct() {
        $this->general = new General('apis_keys');
    }

    abstract public function generateSecretKey();
    abstract public function asignarSecretKey($userId);
    abstract public function getSecretKey($userId);
    abstract function validaExiste($userId);
    abstract function generateAppKey($secretKey);
    abstract function valideApiKey($rescred);
}

class ApiKeyManager extends PagesSecurity {
	
    public function generateSecretKey() {
        $key = random_bytes(32);
        return bin2hex($key);
    }

    private function addApiKey($chartic) {
        $id=$this->general->guardarDatos($chartic);
		return $id;
    }

    private function updateApiKey($chartic, $arg1, $arg2) {
        $this->general->actualizarDatos($chartic, $arg1, $arg2);
    }
	
	public function validaExiste($userId){
		return sql_countsel('apis_keys', "user_id=" . intval($userId)) > 0;
	}
	
	public function asignarSecretKey($userId) {
    
		if($this->validaExiste($userId)){
			// Si ya existe, actualiza la clave secreta
			$secretKey = $this->getSecretKey($userId);
			if (strlen($secretKey) !== 64) {
				// La clave secreta no tiene la longitud correcta
				// Puedes generar una nueva clave secreta y almacenarla
				$secretKey = $this->asignarSecretKey($userId);
			}
			
			
			echo "Contenido de la clave secreta: " . $secretKey . "\n";
			
			$chartic['user_id'] = $userId;
			$chartic['secret_key'] = $secretKey;
			$this->updateApiKey($chartic, 'user_id', $userId);
			$AppKey = $this->encryptData($userId, $secretKey);
			echo "Longitud de la clave secreta: " . $AppKey . "\n";
			return $AppKey;		
		} else {
			// Si no existe, crea una nueva clave secreta
			$secretKey = $this->getSecretKey($userId);
			if (strlen($secretKey) !== 64) {
				// La clave secreta no tiene la longitud correcta
				// Puedes generar una nueva clave secreta y almacenarla
				$secretKey = $this->asignarSecretKey($userId);
			}
			echo "Longitud de la clave secreta: " . strlen($secretKey) . "\n";
			echo "Contenido de la clave secreta: " . $secretKey . "\n";
			$chartic['user_id'] = $userId;
			$chartic['secret_key'] = $secretKey;
			$id = $this->addApiKey($chartic);
			$AppKey = $this->encryptData($userId, $secretKey);
			echo "Longitud de la clave secreta: " . $AppKey . "\n";
			return $AppKey;		
		}
	}
	
	public function encryptData($data, $secretKey) {
		$method = 'AES-256-CBC';
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
		$ivEncoded = base64_encode($iv);
		$encryptedData = openssl_encrypt($data, $method, hex2bin($secretKey), 0, $iv);
		return $encryptedData . '::' . $ivEncoded;
	}
	
	public function decryptData($encryptedData, $secretKey) {
		// Verifica si la $secretKey es válida y tiene la longitud correcta
		if (strlen($secretKey) !== 64) {
			throw new Exception('Invalid secret key length. Expected 64 hexadecimal characters.');
		}

		$method = 'AES-256-CBC';

		// Verifica si el delimitador '::' está presente en $encryptedData
		if (strpos($encryptedData, '::') === false) {
			throw new Exception('Invalid encrypted data format.');
		}

		// Divide el $encryptedData usando el delimitador '::'
		list($encryptedData, $ivEncoded) = explode('::', $encryptedData, 2);

		// Asegúrate de que tanto $encryptedData como $ivEncoded estén definidos
		if (!isset($encryptedData) || !isset($ivEncoded)) {
			throw new Exception('Invalid encrypted data format.');
		}

		// Decodifica el IV desde base64
		$iv = base64_decode($ivEncoded);

		// Verifica si el IV tiene la longitud correcta
		$ivLength = openssl_cipher_iv_length($method);
		if (strlen($iv) !== $ivLength) {
			throw new Exception("Invalid IV length. Expected $ivLength bytes.");
		}

		// Desencripta los datos
		$decryptedData = openssl_decrypt($encryptedData, $method, hex2bin($secretKey), 0, $iv);

		// Verifica si la desencriptación fue exitosa
		if ($decryptedData === false) {
			$error = openssl_error_string();
			throw new Exception("Decryption failed: $error");
		}
		return $decryptedData;
	}

	public function generateAppKey($secretKey) {
		// No es necesario generar un AppKey adicional, se puede utilizar el encryptedData como AppKey
		return null;
	}

	public function getSecretKey($userId) {
		$from = 'apis_keys AS R';
		$select = 'R.secret_key';
		$where = 'R.user_id = "' . sql_quote($userId) . '"';
		$sql = sql_select($select, $from, $where);

		try {
			$row = sql_fetch($sql);
			return $row['secret_key'];
		} catch (Exception $e) {
			return $e->getMessage();
		}
	}
 
    public function valideApiKey($data){
		
					if (!is_array($data)){
						$records['data'] = array(
							'status' => 401,
							'type' => 'error',
							'message' => 'Credenciales incorrectas'
						);
						echo json_encode($records);
						exit;
					}	
	   			try {
					    
					   if (!function_exists('getallheaders')) {
						function getallheaders() {
							$headers = [];
							foreach ($_SERVER as $key => $value) {
								if (strpos($key, 'HTTP_') === 0) {
									$header = str_replace('HTTP_', '', $key);
									$header = str_replace('_', '-', $header);
									$header = ucwords(strtolower($header));
									$headers[$header] = $value;
								}
							}
								return $headers;
							}
						}
					 
						try {
						$headers = getallheaders();
					
						$apisKey = new ApiKeyManager();
						$id_auteur = $data['id_auteur'];
						
						
						$testData = $headers['x-sices-api-apikey'];
						$apisKey = new ApiKeyManager();
						$id_auteur = $data['id_auteur'];
						$testData = $headers['x-sices-api-apikey'];
						$testSecretKey = $this->getSecretKey($id_auteur);
						$encryptedTestData = $this->encryptData($testData, $testSecretKey);
						$decryptedTestData = $this->decryptData($encryptedTestData, $testSecretKey);
						}catch (Exception $e) {
							$records['data'] = array(
								'status' => 400,
								'error' => array(
									'message' => $e->getMessage(),
									'code' => $e->getCode(),
								)
							);
						}
						//echo 'Datos desencriptados: ' . $decryptedTestData . "\n";
						//echo 'Datos originales: ' . $testData . "\n";

						if (trim($decryptedTestData) === trim($testData)) {
							//echo "Los datos coinciden\n";
							return true;
						} else {
							//echo "Los datos no coinciden\n";
							//echo "Diferencias:\n";
							//echo "Desencriptados: " . strlen($decryptedTestData) . " caracteres\n";
							//echo "Originales: " . strlen($testData) . " caracteres\n";
							return false;
						}
					
				}catch (Exception $e) {
					$records['data'] = array(
						'status' => 400,
						'error' => array(
							'message' => $e->getMessage(),
							'code' => $e->getCode(),
						)
					);
					echo json_encode($records);
					exit;
				}
			}

}