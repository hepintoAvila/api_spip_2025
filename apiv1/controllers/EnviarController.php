<?php
require_once '../../api2025/makeCurlRequest.php';
require_once '../../api2025/importer_csv.php';
class EnviarController {
        public static function handleRequest() {
            // Obtener datos de autenticación
            $authData = self::getAuthData();
           // Obtener parámetros de la URL
            $urlParams = self::getUrlParams();
			  if (isset($_FILES['File'])) {
					$file = $_FILES['File'];
					$tmpName = $file['tmp_name'];
					$options = array(
					  'head' => $_POST['head'],
					  'delim' => $_POST['delim'],
					  'enclos' => $_POST['enclos'],
					  'len' => $_POST['len'],
					  'charset_source' => $_POST['charset_source'],
					);
					 $preparedData = inc_importer_csv_dist($tmpName, $options);
				  }else{
					 $preparedData = json_decode(file_get_contents('php://input'), true); 
				  }
            if (!$urlParams){
                echo json_encode(["error" => "Invalid URL structure for 'auteur' action"]);
                return;
            }
			if (!$authData){
                echo json_encode(["error" => "Invalid URL structure for 'authData' action"]);
                return;
            }
                $payload = array('params' =>json_encode($urlParams),'data' => json_encode($preparedData),'timestamp' => time());
				$variables = array_merge($urlParams, $payload);
				 
				 echo makeCurlRequest($variables,$authData);

        }
    
  public static function getAuthData() {
    $authData = [];
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
    $headers = getallheaders();
     //print_r($headers);
    // Normalizar nombres de headers (case-insensitive)
    $headerKeys = array_change_key_case($headers, CASE_UPPER);
    // Obtener headers con nombres normalizados
        $encryptedData = '';
        if (isset($headerKeys['X-SICES-API-APIKEY'])) {
            $encryptedData = $headerKeys['X-SICES-API-APIKEY'];
        }
        
    // Verificar si es una solicitud de Postman (con datos sin codificar)
 
    
    if (empty($encryptedData)) {
        error_log("Headers recibidos: " . print_r($headers, true));
        echo json_encode(['error' => 'Missing or invalid headers', 'debug' => [
            'X-SICES-API-Apikey' => isset($headers['X-SICES-API-Apikey'])
        ]]);
        exit;
    }

	
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
		 	   $authData = ['var_login' => $php_auth_user, 'password' => $php_auth_pw];
			 } catch (Exception $e) {
                ///error_log("Error en obtenerPass: " . $e->getMessage());
                throw new Exception('NO AUTORIZADO_PHP_AUTH_USER');
            }
	   }
  
    return $authData;
}
    
       public static function getUrlParams() {
			$urlParams = [];
			$applyBase64 = false;
			// Buscar parámetro "header" en GET o POST
			if (isset($_GET['header']) && $_GET['header'] === 'true') {
				$applyBase64 = true;
			} elseif (isset($_POST['header']) && $_POST['header'] === 'true') {
				$applyBase64 = true;
			}

			// Obtener parámetros de GET o POST
			if ($applyBase64) {
				foreach ($_REQUEST as $key => $value) {
					if ($key !== 'header') { // Ignorar el parámetro "header"
						$urlParams[$key] = base64_encode($value);
					}
				}
			} else {
				$urlParams = $_REQUEST;
			}

			return $urlParams;
		}
         
    }
 
?>
