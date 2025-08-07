<?php

// AuthorController.php
/**
 * @About:      API Interface
 * @File:       AuthorController.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/
 
require_once 'helps/Segurity.php';
require_once 'helps/makeCurlRequest.php';
require_once 'helps/config.php';
require_once 'helps/DbHandler.php';


class EnviarController {
    /**
     * Maneja la solicitud de envío de datos
     */
    public static function handleRequest() {
        // Obtener datos de autenticación
        $authData = self::getAuthData();
        
        // Obtener parámetros de la URL
        $urlParams = self::getUrlParams();
                
        // Verificar la autenticación del usuario
        $db = new DbHandler();
        $query = "SELECT * FROM api_auteurs WHERE login = ?";
        $resultado = $db->metodoGet($query, array($authData['var_login']));
        
        if (!empty($resultado)) {
            // Verificar la contraseña
            if (verificarPassword($authData['password'], $resultado[0]['pass'], $resultado[0]['alea_actuel'])) {
                // Enviar los datos
                $authDatasend = ['var_login' => $resultado[0]['login'], 'password' => $authData['password'], 'email' => $resultado[0]['email']];
                echo makeCurlRequest($urlParams, $authDatasend);
            } else {
                // Devolver error de autenticación
                $records['data'] = array('status' => '401');
                echo json_encode($records);
                exit;
            }
        }
    }

    /**
     * Obtiene los datos de autenticación de la solicitud
     * @return array Datos de autenticación
     */
    private static function getAuthData() {
        // Obtener encabezados de autenticación
        $headers = getallheaders();
        $headerKeys = array_change_key_case($headers, CASE_UPPER);
        
        // Verificar la presencia de los encabezados de autenticación
        if (empty($headerKeys['X-SICES-API-APIKEY']) || empty($headerKeys['X-SICES-API-APITOKEN'])) {
            // Devolver error de autenticación
            error_log("Headers recibidos: " . print_r($headers, true));
            echo json_encode(['error' => 'Missing or invalid headers']);
            exit;
        }
        
        // Decodificar los encabezados de autenticación
        $encryptedData = base64_decode($headerKeys['X-SICES-API-APIKEY']);
        $secretKey = base64_decode($headerKeys['X-SICES-API-APITOKEN']);
        $var_login = base64_decode($headerKeys['X-SICES-API-USER']);
        $password = obtenerPass($encryptedData, $secretKey);
        
        // Retornar los datos de autenticación
        return [
            'var_login' => $var_login,
            'password' => $password
        ];
    }

    /**
     * Obtiene los parámetros de la URL de la solicitud
     * @return array Parámetros de la URL
     */
    private static function getUrlParams() {
        // Verificar si se debe aplicar la codificación Base64
        $applyBase64 = false;
        if (isset($_GET['header']) && $_GET['header'] === 'true') {
            $applyBase64 = true;
        } elseif (isset($_POST['header']) && $_POST['header'] === 'true') {
            $applyBase64 = true;
        }
        
        // Obtener parámetros de la URL
        if ($applyBase64) {
            $urlParams = [];
            foreach ($_REQUEST as $key => $value) {
                if ($key !== 'header') {
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
