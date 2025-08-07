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

require_once 'helps/Segurity.php';
require_once 'helps/makeCurlRequest.php';
require_once 'helps/DbHandler.php';
 


class AuthorController {
	
	
public static function registrarIntentoFallido($login, $ip) {
    $db = new DbHandler();
    $query = "INSERT INTO api_intentos_fallidos_login (login, ip) VALUES (?, ?)";
    $db->metodoInsert($query, array($login, $ip));
}

public static function obtenerIntentosFallidosPorIp($ip) {
    $db = new DbHandler();
    $query = "SELECT COUNT(*) AS intentos_fallidos FROM api_intentos_fallidos_login WHERE ip = ? AND timestamp > NOW() - INTERVAL 30 MINUTE";
    $resultado = $db->metodoGet($query, array($ip));
    
    if (!empty($resultado)) {
        return $resultado[0]['intentos_fallidos'];
    } else {
        return 0;
    }
}

public static function obtenerIntentosFallidos($login) {
    $db = new DbHandler();
    $query = "SELECT COUNT(*) AS intentos_fallidos FROM api_intentos_fallidos_login WHERE login = ? AND timestamp > NOW() - INTERVAL 30 MINUTE";
    $resultado = $db->metodoGet($query, array($login));
    
    if (!empty($resultado)) {
        return $resultado[0]['intentos_fallidos'];
    } else {
        return 0;
    }
}
public static function incrementarIntentosFallidos($login, $db) {
    $query = "INSERT INTO api_intentos_fallidos_login (login) VALUES (?)";
    $db->metodoInsert($query, array($login));
}
public static function obtenerTiempoBloqueo($login) {
    $db = new DbHandler();
    $query = "SELECT tiempo_bloqueo FROM api_intentos_fallidos_login WHERE login = ? ORDER BY id DESC LIMIT 1";
    $resultado = $db->metodoGet($query, array($login));
    
    if (!empty($resultado)) {
        return $resultado[0]['tiempo_bloqueo'];
    } else {
        return 0;
    }
}

public static function actualizarTiempoBloqueo($login, $tiempoBloqueo) {
    $db = new DbHandler();
    $query = "UPDATE api_intentos_fallidos_login SET tiempo_bloqueo = ? WHERE login = ? ORDER BY id DESC LIMIT 1";
    $db->metodoUpdate($query, array($tiempoBloqueo, $login));
}

public static function reiniciarIntentosFallidos($login) {
    $db = new DbHandler();
    $query = "DELETE FROM api_intentos_fallidos_login WHERE login = ? AND timestamp < NOW() - INTERVAL 30 MINUTE";
    $db->metodoDelete($query, array($login));
}
public static function handleRequest() {
    try {
        $authData = self::getAuthData();
        $urlParams = self::getUrlParams();

        if (!$urlParams) {
            throw new Exception("Invalid URL structure for 'auteur' action");
        }

        $variables = array_merge($urlParams, $authData);

        $db = new DbHandler();
        $res = $db->validarUsuario($variables['var_login']);

        if ($res) {
            $query = "SELECT * FROM api_auteurs WHERE login = ?";
            $resultado = $db->metodoGet($query, array($variables['var_login']));
			
            if (!empty($resultado)) {
				
                $email = $resultado[0]['email'];
                $nom = $resultado[0]['nom'];
				$login=$resultado[0]['login'];	
					$maxIntentos = 5;
					$tiempoBloqueo = 30; // minutos

					$intentosFallidos = self::obtenerIntentosFallidos($login);
					$tiempoBloqueoUsuario = self::obtenerTiempoBloqueo($login);

					if ($intentosFallidos >= $maxIntentos) {
						if ($tiempoBloqueoUsuario > time()) {
							$tiempoRestante = $tiempoBloqueoUsuario - time();
							$records['data'] = array('status' => '401', 'error' => 'Cuenta bloqueada temporalmente. Inténtalo de nuevo en ' . gmdate("i:s", $tiempoRestante) . ' minutos');
							echo json_encode($records);
							exit;
						} else {
							// Reiniciar el contador de intentos fallidos
							self::reiniciarIntentosFallidos($login);
						}
					}
					
                if (verificarPassword($variables['password'], $resultado[0]['pass'], $resultado[0]['alea_actuel'])) {
					$authDatasend = ['var_login' => $login, 'password' => $variables['password']];
                    $varsend = array_merge($urlParams, $authDatasend);
                    echo makeCurlRequest($varsend, $authDatasend);
                } else {
					$ip = $_SERVER['REMOTE_ADDR'];
					self::registrarIntentoFallido($login, $ip);
					$intentosFallidos = self::obtenerIntentosFallidos($login);
					$intentosFallidos++;
					if ($intentosFallidos >= $maxIntentos) {
						// Bloquear la cuenta
						$tiempoBloqueoUsuario = time() + ($tiempoBloqueo * 60);
						self::actualizarTiempoBloqueo($login, $tiempoBloqueoUsuario);
					}
					$records['data'] = array('status' => '401','menssage'=>'usuario o password incorrectos');
					echo json_encode($records);
					exit;
				}
            } else {
                throw new Exception("Usuario no encontrado");
            }
        } else {
            $records['data'] = array('status' => '401');
            echo json_encode($records);
            exit;
        }
		 
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error interno del servidor']);
        exit;
    }
}

	  private static function getAuthData() {
		$authData = [];
		$headers = getallheaders();
		 //print_r($headers);
		// Normalizar nombres de headers (case-insensitive)
		$headerKeys = array_change_key_case($headers, CASE_UPPER);
		// Obtener headers con nombres normalizados
			$encryptedData = $headerKeys['X-SICES-API-APIKEY'] ?? '';
			$secretKey = $headerKeys['X-SICES-API-APITOKEN'] ?? '';
			$var_login = $headerKeys['X-SICES-API-USER'] ?? '';
		// Verificar si es una solicitud de Postman (con datos sin codificar)
	 
		
		if (empty($encryptedData) || empty($secretKey)) {
			error_log("Headers recibidos: " . print_r($headers, true));
			echo json_encode(['error' => 'Missing or invalid headers', 'debug' => [
				'X-SICES-API-Apikey' => isset($headers['X-SICES-API-Apikey']),
				'X-SICES-API-ApiToken' => isset($headers['X-SICES-API-ApiToken']),
				'X-SICES-API-USER' => isset($headers['X-SICES-API-USER'])
			]]);
			exit;
		}
		$encryptedData = base64_decode($headerKeys['X-SICES-API-APIKEY'] ?? '');
		$secretKey = base64_decode($headerKeys['X-SICES-API-APITOKEN'] ?? '');
		$var_login = base64_decode($headerKeys['X-SICES-API-USER'] ?? ''); 
		$password = obtenerPass($encryptedData, $secretKey);
		
		
	   if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
		   $ha = base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6));
			   if (!empty($ha) && strpos($ha, ':') !== false) {
			   list($php_auth_user, $php_auth_pw) = explode(':', $ha);
				   $authData = [
					'var_login' => $php_auth_user,
					'password' => $php_auth_pw
					];
			   }else{
					 throw new Exception('1. NO AUTORIZADO_HTTP_AUTHORIZATION');
			   }	
	   } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $authData = ['var_login' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW']];
       } else {
			try {
			 if ($mode == 'useAuth') {
                   //error_log("Usando modo de autenticación");
                    $password = $passwordKey;
                } elseif ($mode == 'uselocal') {
                    //error_log("Usando modo local");
                    $password = '';
                } else {
                    //error_log("Modo desconocido: $mode");
                    throw new Exception('Modo de autenticación desconocido');
                }
				 $authData = ['var_login' => $php_auth_user, 'password' => $password];
			 } catch (Exception $e) {
                ///error_log("Error en obtenerPass: " . $e->getMessage());
                throw new Exception('2. NO AUTORIZADO_PHP_AUTH_USER');
            }
	   }
	   
		return $authData;
	}
    private static function getUrlParams() {
        $urlParams = [];
        $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($queryString, $params);

        foreach ($params as $key => $value) {
            if ($key !== 'header') {
                $urlParams[$key] = $value;
            }
        }

        return $urlParams;
    }
}
?>