<?php
/***************************************************************************\
 *  SPIP, Système de publication pour l'internet                           *
 *                                                                         *
 *  Copyright © avec tendresse depuis 2001                                 *
 *  Arnaud Martin, Antoine Pitrou, Philippe Rivière, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribué sous licence GNU/GPL.     *
 *  Pour plus de détails voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion de la recherche ajax du mini navigateur de rubriques
 *
 * Cette possibilité de recherche apparaît s'il y a beaucoup de rubriques dans le site.
 *
 * @package SPIP\Core\Rechercher
 **/
use Spip\Chiffrer\SpipCles;

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

		include_spip('base/connect_sql');
		include_spip('inc/filtres_ecrire');
		include_spip('inc/filtres');
		include_spip('inc/utils');
		include_spip('inc/json');
		include_spip('inc/autoriser');
		include_spip('exec/model/admin/claseapi');
		include_spip('inc/auth');
        
/**
 * Función para procesar cada usuario individualmente
 */
function procesar_usuario_importado($usuario) {
    // 1. Verificar si el usuario ya existe
    $existe = sql_getfetsel(
        'id_auteur',
        'api_auteurs',
        'email = ' . sql_quote($usuario['email']) . ' OR nom = ' . sql_quote($usuario['identificacion'])
    );

    if ($existe) {
        return [
            'id' => $usuario['id'] ?? null,
            'status' => 'skipped',
            'message' => 'Usuario ya existe en el sistema',
            'id_auteur' => $existe,
            'email' => $usuario['email'],
            'identificacion' => $usuario['identificacion']
        ];
    }
   
    // 2. Preparar datos para inserción
    $new_pass=$usuario['identificacion'];
    
 
            
            $tipo=$usuario['tipoUsuario'];
            $login=$usuario['identificacion'];
            $email=$usuario['email'];
			$email = unicode2charset(utf_8_to_unicode($email), 'iso-8859-1');
			
				$sql = sql_select('idRol','apis_roles','tipo="'.$tipo.'"');
				while ($row = sql_fetch($sql)) {	
					  $idRol=$row['idRol']; 
				}
	            // 3. Insertar nuevo usuario
            $options=array('tipo'=>$tipo,'entidad'=>'cb_1','clave'=> $new_pass,'status'=> 'Inactivo','idrol'=>$idRol,'nom'=> $email);
            $inscrire_auteur = charger_fonction('inscrire_auteur', 'action');
            $desc = $inscrire_auteur('0minirezo', $email, $login, $options);
             
            $id_auteur = sql_getfetsel(
                'id_auteur',
                'api_auteurs',
                'email = ' . sql_quote($usuario['email']) . ' OR nom = ' . sql_quote($usuario['identificacion'])
            );
				//ASIGNAR PASWORD
				$cpass = array();
				include_spip('inc/acces');
				include_spip('auth/sha256.inc');
				$htpass = generer_htpass($new_pass);
				$alea_actuel = creer_uniqid();
				$alea_futur = creer_uniqid();
				$pass = spip_sha256($alea_actuel . $new_pass);
				$cpass['pass'] = $pass;
				$cpass['htpass'] = $htpass;
				$cpass['alea_actuel'] = $alea_actuel;
				$cpass['alea_futur'] = $alea_futur;
				$cpass['low_sec'] = '';
				include_spip('action/editer_auteur');
				auteur_modifier($desc['id_auteur'], $cpass, true); //
			/*
             try {
                // Configuración del correo
                $to = $email;
                $subject = 'Bienvenido a nuestra plataforma';
				  $message = 'Estimado ' . ($usuario['nombre'] ?? 'usuario') . ",\n\n" .
					   "Le damos la bienvenida a nuestra plataforma. A continuación encontrará sus credenciales de acceso:\n\n" .
					   "Usuario: " . $usuario['email'] . "\n" .
					   "Contraseña: " . $new_pass . "\n\n" .
					   "Para acceder al sistema, visite: " . url_de_base() . "\n\n" .
					   "Recomendamos cambiar su contraseña después del primer ingreso.\n\n" .
					   "Atentamente,\n" .
					   "El equipo de " . $GLOBALS['meta']['nom_site'] . "\n\n" .
					   "----------------------------------------\n" .
					   "<html>
			<head>
				<style>
					body { font-family: Arial, sans-serif; margin: 20px; }
					.credentials { background: #f5f5f5; padding: 15px; border-radius: 5px; }
					.button { 
						display: inline-block; padding: 10px 15px; 
						background: #0066cc; color: white; 
						text-decoration: none; border-radius: 4px;
					}
				</style>
			</head>
			<body>
				<h2 style=\"color: #0066cc;\">Bienvenido a " . $GLOBALS['meta']['nom_site'] . "</h2>
				<p>Estimado " . ($usuario['nombre'] ?? 'usuario') . ",</p>
				<p>Sus credenciales de acceso son:</p>
				<div class=\"credentials\">
					<p><strong>Usuario:</strong> " . $usuario['email'] . "</p>
					<p><strong>Contraseña temporal:</strong> " . $new_pass . "</p>
				</div>
				<a href=\"" . url_de_base() . "\" class=\"button\">Acceder ahora</a>
				<p><em>Por seguridad, cambie su contraseña después del primer ingreso.</em></p>
				<hr>
				<p style=\"font-size: 0.9em; color: #666;\">
					Atentamente,<br>
					El equipo de " . $GLOBALS['meta']['nom_site'] . "<br>
					<small>Este es un mensaje automático, por favor no responda.</small>
				</p>
			</body>
			</html>";
                $from = 'no-reply@lacasadelbarbero.com.co'; // Usar un dominio que controles
                
                // Cabeceras mejoradas
                $headers = [
                    'From' => $from,
                    'Reply-To' => $from,
                    'X-Mailer' => 'PHP/' . phpversion(),
                    'Content-type' => 'text/html; charset=utf-8',
                    'MIME-Version' => '1.0'
                ];
                
                // Convertir array de headers a string
                $headersString = '';
                foreach ($headers as $key => $value) {
                    $headersString .= "$key: $value\r\n";
                }
                
                // Intento de envío
                $success = mail($to, $subject, $message, $headersString);
                
                if ($success) {
                    // Log exitoso (puedes registrar en BD aquí)
                    error_log('Correo enviado a: ' . $to);
                    echo json_encode([
                        'success' => true,
                        'message' => 'Correo enviado con éxito'
                    ]);
                } else {
                    // Obtener más información del error
                    $error = error_get_last();
                    throw new Exception($error['message'] ?? 'Error desconocido al enviar el correo');
                }
            } catch (Exception $e) {
                // Log del error
                error_log('Error enviando correo: ' . $e->getMessage());
                
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error en el servidor: ' . $e->getMessage()
                ]);
            }
			*/
	    // 4. Retornar resultado exitoso
	 
			return [
				'id' => $usuario['id'] ?? null,
				'status' => 'success',
				'message' => 'Usuario importado correctamente',
				'id_auteur' => $id_auteur,
				'email' => $usuario['email'],
				'emailEnviado' => '',
				'identificacion' => $usuario['identificacion']
			];

}

        function api_importar($usuarios) {
            try {
                // 1. Validar datos de entrada
                if (empty($usuarios)) {
                    throw new Exception('Datos de importación vacíos', 400);
                }
        
                // 2. Decodificar el JSON si es un string
                if (is_string($usuarios[0])) {
                    $usuarios = json_decode($usuarios[0], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Formato JSON inválido en los datos', 400);
                    }
                }
        
                // 3. Validar estructura de cada usuario
                $errores = [];
                foreach ($usuarios as $index => $usuario) {
                    if (!isset($usuario['identificacion']) || trim($usuario['identificacion']) === '') {
                        $errores[] = "Usuario {$index}: Falta identificacion o está vacío";
                    }
                    if (!isset($usuario['email']) || trim($usuario['email']) === '') {
                        $errores[] = "Usuario {$index}: Falta email o está vacío";
                    }
                    // Validar formato de email
                    if (isset($usuario['email']) && !filter_var($usuario['email'], FILTER_VALIDATE_EMAIL)) {
                        $errores[] = "Usuario {$index}: Email no válido";
                    }
                }
        
                if (!empty($errores)) {
                    return [
                        'status' => 'error',
                        'code' => 400,
                        'errors' => $errores,
                        'message' => 'Errores de validación en los datos',
                        'input_data' => $usuarios // Para depuración
                    ];
                }
        
                // 4. Procesar cada usuario
                $resultados = [];
                foreach ($usuarios as $usuario) {
                    $resultado = procesar_usuario_importado($usuario);
                    $resultados[] = $resultado;
                }
               
                return $resultados;
        
            } catch (Exception $e) {
                return [
                    'status' => 'error',
                    'code' => $e->getCode() ?: 500,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace() // Solo para entorno de desarrollo
                ];
            }
        }
		
		//$campos = $GLOBALS['tables_principales']['api_clientes']['field'];
		//$select = implode(',',array_keys($campos));
        $opcion = $_POST["params"]["opcion"];
		$opcion = base64_decode($opcion);
        
       
        if($opcion !== null){

        switch ($opcion) {
        case 'importar':
            $datos_json = $_POST["data"] ?? '[]';

            // 2. Crear el array de usuarios (ya no necesitas array() adicional)
            $usuarios = json_decode($datos_json, true);
            
            // 3. Validar JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                die(json_encode([
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Formato JSON inválido'
                ]));
            }
            $resultados = api_importar($usuarios);
			
            $records = array('status'=>202,'message'=>'::OK:: Registro importados con exito!','data'=>$resultados);
            $var = var2js($records);	
            echo $var;	
            
              	
            break;
        }

	    } else {
            // Maneja el caso cuando 'accion' no esté presente en GET ni POST
            $records['data'] = array('status'=>404);
            $var = var2js($records);
            echo $var; 
        }           
