<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class UsuarioService {
	private $apis;
	private $programas;
    private $table_auteurs;
	private $estudiantes;
    private $table_turnos;
    private $table_libros_visita;
    private $table_usuarios;
    public function __construct() {
     $this->apis = new General('api_auteurs');
     $this->programas = new General('api_programas');
     $this->estudiantes = new General('upc_estudiantes');
     $this->table_auteurs = 'api_auteurs';
     $this->table_turnos = 'upc_turnos';
     $this->table_libros_visita = 'upc_libros_visita';
     $this->table_usuarios = 'upc_usuarios';
     $this->table_pcs= new General('upc_pcs');
	}
	public function obtenerEmail($datosPersonales, $documento) {
    return !empty($datosPersonales['PENG_EMAILINSTITUCIONAL']) ? $datosPersonales['PENG_EMAILINSTITUCIONAL'] : 
           (!empty($datosPersonales['PEGE_MAIL']) ? $datosPersonales['PEGE_MAIL'] : $documento . "@gmail.com");
        }
	public function addUsuarios($chartic){
		return $this->add($this->table_usuarios, $chartic);
	}
	public function addUsuariosMobile($chartic){
		return $this->add($this->table_auteurs, $chartic);
	}
    public function addTurnos($chartic){
        return $this->add($this->table_turnos, $chartic);
    }
    public function addVisitas($chartic){
        return $this->add($this->table_libros_visita, $chartic);
    }
	private function add($table, $champs, $set = null) {

        if ($set) {
            $champs = array_merge($champs, $set);
        }

        // Envoyer aux plugins
        $champs = pipeline('pre_insertion',
            array(
                'args' => array(
                    'table' => $table,
                ),
                'data' => $champs
            )
        );

        try {
            $id_article = sql_insertq($table, $champs);
        } catch (Exception $e) {
            spip_log("Error al insertar artículo: " . $e->getMessage(), _LOG_ERREUR);
        }

        pipeline('post_insertion',
            array(
                'args' => array(
                    'table' => $table,
                    'id_objet' => $id_article
                ),
                'data' => $champs
            )
        );

        return $id_article;
    }
    private function usuarios_inserer($champs, $set = null) {
        
        
        
        	if ($set) {
        		$champs = array_merge($champs, $set);
        	}
        
        	// Envoyer aux plugins
        	$champs = pipeline('pre_insertion',
        		array(
        			'args' => array(
        				'table' => 'api_auteurs',
        			),
        			'data' => $champs
        		)
        	);
        
            try {
                $id_article = sql_insertq("api_auteurs", $champs);
            } catch (Exception $e) {
                spip_log("Error al insertar artículo: " . $e->getMessage(), _LOG_ERREUR);
            }
        
        	pipeline('post_insertion',
        		array(
        			'args' => array(
        				'table' => 'api_auteurs',
        				'id_objet' => $id_article
        			),
        			'data' => $champs
        		)
        	);
        
        	return $id_article;
        }
	public function addAuteurs($data){
	    
	    
		$chartic['nom'] =$data['nom'];
		$chartic['bio'] ='';
		$chartic['nom_site'] ='';
		$chartic['url_site'] ='';
		$chartic['login'] =$data['login'];
		$chartic['pass'] ='';
		$chartic['low_sec'] ='';
		$chartic['statut'] ='';
		$chartic['webmestre'] ='';
		$chartic['maj'] ='';
		$chartic['pgp'] ='';
		$chartic['htpass'] ='';
		$chartic['en_ligne'] ='';
		$chartic['alea_actuel'] ='';
		$chartic['alea_futur'] ='';
		$chartic['prefs'] ='';
		$chartic['cookie_oubli'] ='';
		$chartic['source'] ='';
		$chartic['entidad'] ='';
		$chartic['id_rol'] ='';
		$chartic['clave'] ='';
		$chartic['tipo_usuario'] ='';
		$chartic['id_aspirante'] ='';
	return $chartic;	
		
	}
	public function updateAuteurs($data){
	    
			$cpass = array();
			$new_pass = $data['pass'];
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
			auteur_modifier($data['id_auteur'], $cpass, true); //
	}										
	public function getUbicaciones($titulo) {
        $sql = sql_select('id_tipo', 'upc_tipo_ubicaciones', 'titulo = ' . sql_quote($titulo));
        $row = sql_fetch($sql);
        if (!$row) {
           return 'id_tipo';
        }
        return $row['id_tipo'];
    }											
	public function getDocumento($documento) {
        $sql = sql_select('id', 'upc_estudiantes', 'PEGE_DOCUMENTOIDENTIDAD = ' . sql_quote($documento));
        $row = sql_fetch($sql);
        if (!$row) {
           return '0';
        }
        return $row['id'];
    }
    public function getDocumentoEstudiantes($documento) {
        $sql = sql_select('*', 'upc_estudiantes', 'PEGE_DOCUMENTOIDENTIDAD = ' . sql_quote($documento));
        $row = sql_fetch($sql);
        if (!$row) {
           return '0';
        }
        return $row;
    }
	public function getDocumentoId($id) {
		  $from = 'api_auteurs AS R';
		  $select = '*';
		  $where = 'R.id_auteur="'.intval($id).'" AND R.status = "Activo" ORDER BY R.id_auteur DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$usuarios = array();
			while ($row = sql_fetch($sql)) {
				$usuarios[] = $row;
			}
			if (!empty($usuarios)) {
				      return $usuarios;
				}else{
				$datosusuarios['Usuarios'] = array(
        				'id_auteur' => 0,
        				'nombres' => '',
        				'email' =>'',
        				'login' => '',
        				'tipo' =>'',
        			  );
				  return $datosusuarios;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
    }    
    public function getProgramas($id_programa) {
        $sql = sql_select('nombre_programa', 'upc_tipo_programas', 'id_programa = "'.intval($id_programa).'"');
        $row = sql_fetch($sql);
        if (!$row) {
           return 'nombre_programa';
        }
        return $row['nombre_programa'];
    }
    public function getProgramasId($titulo) {
        $sql = sql_select('id_programa', 'upc_tipo_programas', 'nombre_programa = ' . sql_quote($titulo));
        $row = sql_fetch($sql);
        if (!$row) {
           return 'id_programa';
        }
        return $row['id_programa'];
    }
    public function getRoles($rol) {
        $sql = sql_select('rol', 'apis_rol', 'id= "'.intval($rol).'"');
        $row = sql_fetch($sql);
        if (!$row) {
           return 'rol';
        }
        return $row['rol'];
    }
    public function getIdAuteur($data) {
        $sql = sql_select('id', 'upc_estudiantes', 'PEGE_DOCUMENTOIDENTIDAD= "'.intval($data['documento']).'"');
        $row = sql_fetch($sql);
        if (!$row) {
           return '0';
        }
        return $row['id'];
    } 
	public function addEstudiante($data){
			if (!is_array($data)) {
            throw new Exception('El parámetro $chartic debe ser un array');
        }	
				$id = $this->getDocumento($data['documento']);
				if(intval($id)==0){
				$chartic['MATE_CODIGOMATERIA']='';
				$chartic['GRUP_NOMBRE']='';
				$chartic['PEGE_DOCUMENTOIDENTIDAD']=$data['documento'];
				$chartic['ESTUDIANTE']=$data['documento'];
				$chartic['MATE_NOMBRE']='';
				$chartic['PEGE_MAIL']=$data['email']??'';
				$chartic['PROG_NOMBRE']=$data['programa'];
				$chartic['PEGE_TELEFONOCELULAR']='';
				$chartic['PEGE_TELEFONO']='';
				$chartic['PENG_EMAILINSTITUCIONAL']=$data['email']??'';
				$chartic['status']='Activo';
				$chartic['rol']=$data['tipo'];
				$chartic['id_rol']=$data['id_rol'];
				$this->estudiantes->guardarDatos($chartic);
				}else{
				    return $id;
				}
	}
	public function updateUsuarios($data){
			if (!is_array($data)) {
				throw new Exception('El parámetro $data debe ser un array');
			}

			// Validar que el id_turno esté presente en el array
			if (!isset($data['id_auteur'])) {
				throw new Exception('El parámetro id_auteur es obligatorio');
			}

			// Crear el array de datos para actualizar
			$chartic = array();
			foreach ($data as $key => $value) {
				// Ignorar el id_turno ya que se utiliza para la condición de actualización
				if ($key !== 'id_auteur') {
					$chartic[$key] = $value;
				}
			}

			try {
				$this->apis->actualizarDatos($chartic, 'id_auteur', $data['id_auteur']);
			} catch (Exception $e) {
				$records['data'] = array('status' => 401, 'error' => $e->getMessage());
				header('Content-Type: application/json');
				http_response_code(401);
				echo json_encode($records);
				exit;
			}
	}
	public function deleteUsuarios($arg1){
		sql_delete("api_auteurs","id_auteur=" . intval($arg1));
	}
	public function getUsuarios() {
		  $from = 'api_auteurs AS R';
		  $select = 'R.id_auteur,R.nom AS nombres,R.email,R.login,R.tipo';
		  $where = 'R.status = "Activo" ORDER BY R.id_auteur DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$usuarios = array();
			while ($row = sql_fetch($sql)) {
				$usuarios[] = $row;
			}
			$datosusuarios = array('Usuarios' => array());
			foreach ($usuarios as $val) {
			  $datosusuarios['Usuarios'][] = array(
				'id_auteur' => $val['id_auteur'],
				'nombres' => $val['nombres'],
				'email' => $val['email'],
				'login' => $val['login'],
				'tipo' => $val['tipo'],
			  );
			}
			if (!empty($datosusuarios['Usuarios'])) {
					$records = array('status'=>200,'type'=>'success','data'=>$datosusuarios, 'message'=>'Listodo de Usuarios');
				  return $records;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Usuarios');
				  return $records;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
	public function getEstudiantes($data) {
			
		  $from = 'upc_estudiantes AS R';
		  $select = 'R.id as id_estudiante, R.PEGE_DOCUMENTOIDENTIDAD AS documento,
		  R.ESTUDIANTE AS nombres,R.PENG_EMAILINSTITUCIONAL as email,R.PROG_NOMBRE as programa,R.PEGE_TELEFONOCELULAR as celular,R.id_rol,R.rol';
		  $where = 'R.PEGE_DOCUMENTOIDENTIDAD = "'.intval($data['documento']).'" AND R.status = "Activo" ORDER BY R.id DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$usuarios = array();
			while ($row = sql_fetch($sql)) {
				$usuarios[] = $row;
			}
			$datosusuarios = array('Estudiantes' => array());
			foreach ($usuarios as $val) {
			  $datosusuarios['Estudiantes'][] = array(
				'id_estudiante' => $val['id_estudiante'],
				'documento' => $val['documento'],
				'nombres' => $val['nombres'],
				'email' => $val['email'],
				'programa' => $val['programa'],
				'celular' => $val['celular'],
        		'id_rol' => $val['id_rol'],
        		'tipo' => $val['rol'],				
			  );
			}
			if (!empty($datosusuarios['Estudiantes'])) {
					$records = array('status'=>200,'type'=>'success','data'=>$datosusuarios, 'message'=>'Listodo de Estudiantes');
				  return $records;
				} else{
				    $datosusuariosd['Estudiantes'][] = array(
    				'id_estudiante' => 1,
    				'documento' => '00000000',
    				'nombres' => 'No encotrado',
    				'email' => '',
    				'programa' => 'sin programa',
    				'celular' => '0000000',
    			  );
					$records = array('status'=>200,'type'=>'success','data'=>$datosusuariosd,'message'=>'No existen registros de Estudiantes');
				  return $records;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
    public function getLoginAuth0($data) {
    			
    		  $from = 'upc_estudiantes AS R';
    		  $select = 'R.id as id_estudiante, R.PEGE_MAIL AS email,R.PEGE_DOCUMENTOIDENTIDAD as documento, R.ESTUDIANTE AS nombres,R.PENG_EMAILINSTITUCIONAL as email,R.PROG_NOMBRE as programa,
    		  R.PEGE_TELEFONOCELULAR as celular,R.id_rol,R.rol';
    		  $where = 'R.PEGE_MAIL = "'.$data['email'].'" OR R.PENG_EMAILINSTITUCIONAL = "'.$data['email'].'" AND R.status = "Activo" ORDER BY R.id DESC';
    		  $sql = sql_select($select, $from, $where);
    
    		  try {
    			$usuarios = array();
    			while ($row = sql_fetch($sql)) {
    				$usuarios[] = $row;
    			}
    		if (!empty($usuarios)) {
            		  $datosusuarios = array('Estudiantes' => array());
        			   foreach ($usuarios as $val) {
        			  $datosusuarios['Estudiantes'][] = array(
        				'id_estudiante' => $val['id_estudiante'],
        				'documento' => $val['documento'],
        				'nombres' => $val['nombres'],
        				'email' => $val['email'],
        				'programa' => $val['programa'],
        				'celular' => $val['celular'],
        				'id_rol' => $val['id_rol'],
        				'tipo' => $val['rol'],
        			    );
        			    return $datosusuarios['Estudiantes'][0];
        			   }
			   }else{
				    $datosusuariosd['Estudiantes'] = array(
    				'id_estudiante' => 0,
    				'documento' => '00000000',
    				'nombres' => 'No encotrado',
    				'email' => '',
    				'programa' => 'sin programa',
    				'celular' => '0000000',
    			   );
    			   return $datosusuariosd['Estudiantes'];
			   }
			 
    			
    			
    		  } catch (Exception $e) {
    			return json_encode(array('error' => $e->getMessage()));
    		  }
    		}	
    public function getUsuariosAuth0($data) {
		  $from = 'api_auteurs AS R';
		  $select = '*';
		  $where = 'R.email LIKE "%'.$data['email'].'%" AND R.status = "Activo" ORDER BY R.id_auteur DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$usuarios = array();
			while ($row = sql_fetch($sql)) {
				$usuarios[] = $row;
			}
			if (!empty($usuarios)) {
				      return $usuarios;
				}else{
				$datosusuarios['Usuarios'] = array(
        				'id_auteur' => 0,
        				'nombres' => '',
        				'email' =>'',
        				'login' => '',
        				'tipo' =>'',
        			  );
				  return $datosusuarios;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
	public function addAuteursAuth0($data){
		$new_pass = $data['email'];
		include_spip('inc/acces');
		include_spip('auth/sha256.inc');
		$htpass = generer_htpass($new_pass);
		$alea_actuel = creer_uniqid();
		$alea_futur = creer_uniqid();
		$pass = spip_sha256($alea_actuel . $new_pass);

        $chartic['id_auteur'] =$data['id_estudiante'];
		$chartic['nom'] =$data['nombres'];
		$chartic['bio'] ='';
		$chartic['email'] =$data['email'];
		$chartic['nom_site'] ='';
		$chartic['url_site'] ='';
		$chartic['login'] =$data['email'];
		$chartic['pass'] =$pass;
		$chartic['low_sec'] ='';
		$chartic['statut'] ='0minirezo';
		$chartic['webmestre'] ='';
		$chartic['maj'] ='';
		$chartic['pgp'] ='';
		$chartic['htpass'] =$htpass;
		$chartic['en_ligne'] ='';
		$chartic['alea_actuel'] =$alea_actuel;
		$chartic['alea_futur'] =$alea_futur;
		$chartic['prefs'] ='';
		$chartic['cookie_oubli'] ='';
		$chartic['source'] ='spip';
		$chartic['lang'] ='es';
		$chartic['imessage'] ='oui';
		$chartic['tipo'] =$data['tipo'];
		$chartic['entidad'] ='cb_1';
		$chartic['id_rol'] =$data['id_rol'];
		$chartic['status'] ='Activo';
		$chartic['clave'] =$new_pass;
		$chartic['tipo_usuario'] =$data['tipo'];
		$chartic['id_aspirante'] =$data['id_estudiante'];
	return $chartic;	
		
	}
	//$data['documento']
	//$data['id_estudiante']
	//$data['nombres']
	//$data['email']
	public function charticAuteursMobile($data){
		$new_pass = $data['documento'];
		include_spip('inc/acces');
		include_spip('auth/sha256.inc');
		$htpass = generer_htpass($new_pass);
		$alea_actuel = creer_uniqid();
		$alea_futur = creer_uniqid();
		$pass = spip_sha256($alea_actuel . $new_pass);

        $chartic['id_auteur'] =$data['id_estudiante'];
		$chartic['nom'] =$data['nombres'];
		$chartic['bio'] ='';
		$chartic['email'] =$data['email'];
		$chartic['nom_site'] ='';
		$chartic['url_site'] ='';
		$chartic['login'] =$data['documento'];
		$chartic['pass'] =$pass;
		$chartic['low_sec'] ='';
		$chartic['statut'] ='0minirezo';
		$chartic['webmestre'] ='';
		$chartic['maj'] ='';
		$chartic['pgp'] ='';
		$chartic['htpass'] =$htpass;
		$chartic['en_ligne'] ='';
		$chartic['alea_actuel'] =$alea_actuel;
		$chartic['alea_futur'] =$alea_futur;
		$chartic['prefs'] ='';
		$chartic['cookie_oubli'] ='';
		$chartic['source'] ='spip';
		$chartic['lang'] ='es';
		$chartic['imessage'] ='oui';
		$chartic['tipo'] =$data['tipo'];
		$chartic['entidad'] ='cb_1';
		$chartic['id_rol'] =$data['id_rol'];
		$chartic['status'] ='Activo';
		$chartic['clave'] =$new_pass;
		$chartic['tipo_usuario'] =$data['tipo'];
		$chartic['id_aspirante'] =$data['id_estudiante'];
	return $chartic;	
		
	}
    private function decryptData($encryptedData, $secretKey) {
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
    public function registrarUsuario($datos) {
            // Agregar estudiante
            $this->addEstudiante($datos);
            
            // Buscar email en la tabla de estudiantes
            $estudiante = $this->getLoginAuth0($datos);
            
            // Normalizar autores
            $auteur = $this->addAuteursAuth0($estudiante);
            
            // Registrar usuario en la plataforma
            $idAuteur = $this->usuarios_inserer($auteur);
            
            // Obtener ID del autor
            $idAuteur = $this->getIdAuteur($datos);
            
            // Generar llave secreta
            $apiKeyManager = new ApiKeyManager();
            $apiKeyManager->asignarSecretKey($idAuteur);
            
            // Generar API Key
            $loginService = new LoginService($auteur);
            $encryptedData = $loginService->addKey();
            
            // Generar menú
            $menuService = new MenuService();
            $menus = $menuService->getMenusUsuario($auteur);
            
            // Generar permisos
            $permisoService = new PermisoService($auteur);
            $permisos = $permisoService->getPermisos();
            
            // Normalizar array de envío
            $Auth['Auth']= array(
                    'Nom' => $auteur['nom'],
                    'Email' => $auteur['email'] ?? null,
                    'Rol' => $auteur['tipo_usuario'],
                    'status' => $auteur['status'],
                    'AppKey' => $encryptedData,
                
            );
            return array($Auth, $menus, $permisos);
    } 
    public function obtenerJornada() {
        
        $zona_horaria = new DateTimeZone('America/Bogota');
        $fecha_hora = new DateTime('now', $zona_horaria);
        $hora = $fecha_hora->format("H");
        if ($hora < 12) {
            return 1; // Mañana
        } elseif ($hora < 18) {
            return 2; // Tarde
        } else {
            return 3; // Noche
        }
    }
    public function updatePcs($data){
			if (!is_array($data)) {
				throw new Exception('El parámetro $data debe ser un array');
			}

			// Validar que el id_turno esté presente en el array
			if (!isset($data['id_pc'])) {
				throw new Exception('El parámetro id_turno es obligatorio');
			}

			// Crear el array de datos para actualizar
			$chartic = array();
			foreach ($data as $key => $value) {
				// Ignorar el id_turno ya que se utiliza para la condición de actualización
				if ($key !== 'id_pc') {
					$chartic[$key] = $value;
				}
			}
 
			try {
				
				$this->table_pcs->actualizarDatos($chartic, 'id_pc', $data['id_pc']);
			} catch (Exception $e) {
				$records['data'] = array('status' => 401, 'error' => $e->getMessage());
				header('Content-Type: application/json');
				http_response_code(401);
				echo json_encode($records);
				exit;
			}
		}
}