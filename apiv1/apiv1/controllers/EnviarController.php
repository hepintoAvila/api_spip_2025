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
require_once 'helps/config.php';
require_once 'helps/DbHandler.php';
require_once 'helps/importer_csv.php';
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
				$db = new DbHandler();
                $payload = array('params' =>json_encode($urlParams),'data' => json_encode($preparedData),'timestamp' => time());
				$variables = array_merge($urlParams, $payload);
               
            $query = "SELECT * FROM api_auteurs WHERE login = ?";
            $resultado = $db->metodoGet($query, array($authData['var_login']));

            if (!empty($resultado)) {
					$email = $resultado[0]['email'];
                if (verificarPassword($authData['password'], $resultado[0]['pass'], $resultado[0]['alea_actuel'])) {
                    $authDatasend = ['var_login' => $resultado[0]['login'], 'password' => $authData['password'], 'email' => $email];
                   echo makeCurlRequest($variables,$authDatasend);
                } else {
                    $records['data'] = array('status' => '401');
                    echo json_encode($records);
                    exit;
                }
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
	
    $authData = [
        'var_login' => $var_login,
        'password' => $password
    ];
   
    return $authData;
}
    
       private static function getUrlParams() {
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
