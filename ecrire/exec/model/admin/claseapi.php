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

/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/ 

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/filtres_boites');
include_spip('inc/boutons');
include_spip('inc/pipelines_ecrire');
include_spip('inc/filtres_dates');
include_spip('base/connect_sql');

 abstract class PagesApis {
        public function __construct() {
		//$this->periodoacademico_id=$periodoacademico_id;		
        } 
		abstract function guardarAudi($menu,$submenu,$accion);
		abstract function consultadatos($query,$select,$campos);
		abstract function actualizarDatos($chartic,$id_nom,$id);
		abstract function consultamenu($query,$select,$table);
		abstract function encryptData($data, $secretKey);
		abstract function generateSecretKey();
		abstract function verificarVariables($variable);
		abstract function eliminarUltimoPunto($cadena);
		abstract function formatSave($arrayA, $arrayB) ;
		abstract function formatBase64Decode($arrayA, $arrayB) ;
		abstract function guardarDatos($chartic);
		abstract function respuestaDatos($app, $auth, $select, $camposConsulta, $indiceDatos);
		abstract function obtenerValoresReferencia($arrayA, $arrayB);
		abstract function consultaExiste($query,$select,$table);
		abstract function generarCredenciales($var_auth);
 
	abstract function verificarVariablesOmitir($variables, $omitir = []);
	abstract function consultapermmisos($query,$select,$campos);
		
		//abstract function mergeServicios($arrayA, $arrayB);
 }
 class Apis extends PagesApis
{
         public $table;
		 
		 public function __construct($table)
         {			
			$this->table=$table;
		 }
		
		public function consultapermmisos($query, $select, $campos = null) {
			$sql = sql_select($select, $this->table, $query);
			$datos = array();
			
			if ($sql) {
				// Definir los campos esperados basados en tu SELECT
				$expectedFields = [
					'id_autorizacion',
					'menu',
					'submenu',
					'rol',
					'c',
					'a',
					'u',
					'd'
				];
				
				while ($rawRow = sql_fetch($sql)) {
					$row = array();
					
					// Mapear los campos según su posición esperada
					$i = 0;
					foreach ($expectedFields as $field) {
						$row[$field] = array_values($rawRow)[$i] ?? null;
						$i++;
					}
					
					$datos[] = $row;
				}
			}
			
			return $datos;
			}
 
			public function consultaExiste($query, $select, $table) {
				try {
					$datos = array();
					$existen = false;
					$sql = sql_select($select, $table, $query);
					while ($row = sql_fetch($sql)) {
						$existen = true;
						$datos[] = $row;
					}
					return array('datos' => $datos, 'existen' => !empty($datos));
				} catch (Exception $e) {
					return array('error' => $e->getMessage(), 'existen' => false);
				}
			}	 
		 /**
		 * Retorno los parametros para verificar
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : respuestaDatos()
		 * Parametros de entrada : $app, $auth, $select, $camposConsulta, $indiceDatos
		 * Parametros de Salida: resultado
		 */	
			function respuestaDatos($app, $auth, $select, $camposConsulta, $indiceDatos) {
				$resp = $app->consultadatos('statut="Activo"', $select, $camposConsulta);
				if (!is_null($resp) && count($resp) > 0) {
					$data[$indiceDatos] = $resp;
					return array('status' => 202, 'message' => '::OK:: listado de ' . $indiceDatos, 'data' => $data);
				} else {
					return array('data' => array('status' => 404, 'message' => '::ERROR-003:: aún no existen registros en la BD'));
				}
			}
 
 		 
		function formatBase64Decode($arrayA, $arrayB) {
			// Eliminar elementos no deseados de $arrayA
			$arrayA = array_diff_key($arrayA, array_flip(['accion', 'opcion', 'bonjour', 'var_login']));
		  
			// Crear un nuevo arreglo con los valores de $arrayA correspondientes a $arrayB
			$resultado = [];
			foreach ($arrayB as $key => $value) {
			  if ($key === 'id' || $key === 'maj') {
				continue;
			  }
			  switch ($key) {
				case 'idRol':
				case 'password':
				  $resultado[$key] = '';
				  break;
				default:
				  $resultado[$key] = isset($arrayA[$key]) ? base64_decode($arrayA[$key]) : '';
				  break;
			  }
		  
			}
		  
			// Asignar estatus por defecto
			$resultado['status'] = 'Activo';
		  
			// Reordenar el arreglo según la estructura de $arrayB
			$resultado = array_merge(array_flip(array_filter(array_keys($arrayB), function($key) {
			  return $key !== 'id' && $key !== 'maj';
			})), $resultado);
		  
			return $resultado;
		  }
		/**
		 * Retorno los parametros para verificar
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : obtenerValoresReferencia()
		 * Parametros de entrada : $arrayA, $arrayB
		 * Parametros de Salida: valores
		 */			
		function obtenerValoresReferencia($arrayA, $arrayB) {
		  $valores = [];
		  foreach ($arrayA as $clave) {
			$valores[$clave] = $arrayB[$clave];
		  }
		  return $valores;
		}
		/**
		 * Retorno los parametros para verificar
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : eliminarElementos()
		 * Parametros de entrada : $arrayA, $arrayB,$indicesAdicionales
		 * Parametros de Salida: arrayB
		 */	
		function eliminarElementos($arrayB, $arrayA, $indicesAdicionales) {
		  foreach ($arrayA as $clave) {
			unset($arrayB[$clave]);
		  }
		  foreach ($indicesAdicionales as $indice) {
			unset($arrayB[$indice]);
		  }
		  return $arrayB;
		}		
		/**
		 * Retorno los parametros para verificar
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : formatSave()
		 * Parametros de entrada : $arrayA, $arrayB
		 * Parametros de Salida: resultado
		 */	
		 
		function formatSave($arrayA, $arrayB) {
			 
			// Eliminar elementos no deseados de $arrayA
			unset($arrayA['accion'], $arrayA['opcion'], $arrayA['bonjour'], $arrayA['var_login']);
		
			// Crear un nuevo arreglo con los valores de $arrayA correspondientes a $arrayB
			$resultado = array();
			foreach ($arrayB as $key => $value) {
				if ($key !== 'id' && $key !== 'maj') {
					if (($key==='idRol') or ($key==='password')){
						$resultado[$key]=''; 
					} elseif (isset($arrayA[$key])) {
						$resultado[$key] = $arrayA[$key];
					} else {
						$resultado[$key] = '';
					}
				}
			}
			 
			// Asignar estatus por defecto
			$resultado['status'] = 'Activo';
		   // Reordenar el arreglo según la estructura de $arrayB
		   $estructura = array_keys($arrayB);
		   $estructura = array_filter($estructura, function($key) {
			   return $key !== 'id' && $key !== 'maj';
		   });
		   $resultado = array_merge(array_flip($estructura), $resultado);
			return $resultado;
		}		
		
		 /**
		 * Retorno los parametros para verificar
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : guardarDatos()
		 * Parametros de entrada : $chartic
		 * Parametros de Salida: id
		 */		 
		 
			function guardarDatos($chartic) {
			  try {
				// Validación de entrada
				if (!is_array($chartic)) {
				  throw new Exception('Los datos deben ser un arreglo');
				}

				// Pre-inserción
				$chartic = pipeline('pre_insertion', array(
				  'args' => array('table' => $this->table),
				  'data' => $chartic
				));

				// Inserción en la base de datos
				$id = @sql_insertq($this->table, $chartic);
				if (!$id) {
				  throw new Exception('Error al insertar los datos');
				}

				// Post-inserción
				pipeline('post_insertion', array(
				  'args' => array('table' => $this->table, 'id_objet' => $id),
				  'data' => $chartic
				));

				return $id;
			  } catch (Exception $e) {
				// Manejo de errores
				error_log($e->getMessage());
				throw $e;
			  }
			} 

			 /**
		 * Retorno los parametros para verificar
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : verificarVariables()
		 * Parametros de entrada : $variables
		 * Parametros de Salida: cadena
		 */		 
		 	 
		 function verificarVariables($variables) {
			foreach ($variables as $nombre => $valor) {
				if (empty($valor) OR ($valor === 'undefined')) {
					return "La variable $nombre está vacía.";
				}
			}
					return null; // Todas las variables están llenas
		  }
		function verificarVariablesOmitir($variables, $omitir = []) {
		  foreach ($variables as $nombre => $valor) {
			if (in_array($nombre, $omitir)) {
			  continue; // Omite la validación para este campo
			}

			if (empty($valor) || ($valor === 'undefined')) {
			  return "La variable $nombre está vacía.";
			}
		  }
		  return null; // Todas las variables están llenas
		}		
		public function eliminarUltimoPunto($cadena) {
			$longitud = strlen($cadena);
			if ($longitud > 0 && $cadena[$longitud - 1] === '.') {
				$cadena = substr($cadena, 0, $longitud - 1);
			}
			return $cadena;
		}
		 		// Funciones de cifrado y descifrado
		function encryptData($data, $secretKey) {
					$method = 'AES-256-CBC';
					$ivLength = openssl_cipher_iv_length($method);
					$iv = openssl_random_pseudo_bytes($ivLength);
					$encryptedData = openssl_encrypt($data, $method, $secretKey, 0, $iv);
					return $encryptedData . '::' . base64_encode($iv);
				}
		function generateSecretKey() {
					// Genera 32 bytes de datos aleatorios
					$key = random_bytes(32);
					// Convierte los bytes en una cadena hexadecimal
					return bin2hex($key);
		}
		 
		/**
		 * Retorno los parametros para consultar el valor id maximo
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : obtenerPrimerosCuatroCaracteres()
		 * Parametros de entrada : $cadena
		 * Parametros de Salida: primerosCuatro
		 */
		function obtenerPrimerosCuatroCaracteres($cadena) {
			$primerosCuatro = substr($cadena, 0, 4);
			//$sinRepetidos = implode("", array_unique(str_split($primerosCuatro)));
			return $primerosCuatro;
		}	 
		/**
		 * Retorno los parametros para consultar el valor id maximo
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : eliminarElementosRepetidos()
		 * Parametros de entrada : $inputArray
		 * Parametros de Salida: uniqueArray
		 */		 
		 
		 function eliminarElementosRepetidos($inputArray) {
			 $uniqueArray = array_unique($inputArray, SORT_REGULAR);
				
				usort($uniqueArray, function($a, $b) {
					return strcmp($a['Periodo'], $b['Periodo']);
				});
				
				return $uniqueArray;
		}
		 		 
		/**
		 * Retorno los parametros para actualizar en una tabla
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : datosusuario()
		 * Parametros de entrada : query:
		 * Parametros de Salida:  row
		 */			
		public function consultamenu($query,$select,$table){
			$sql = sql_select(''.$select.'',''.$table.'',''.$query.'');
				while ($row = sql_fetch($sql)) {	
					  $datos[]=$row; 
				}
			 	return $datos;
		}			 
		 	
		/**
		 * Retorno los parametros para guardar en una tabla
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : general_gardar_registro()
		 * Parametros de entrada :$chartic=array(),$table
		 * Parametros de Salida: 
		 */ 
		public function guardarAudi($menu,$submenu,$accion){
				
											 	//AUDITORIA			
	
												 $session_login =_request('var_login');
												 $session_password = _request('password');
												 if (!empty($session_login) && !empty($session_password)) {
												 include_spip('inc/auth');
												 $aut = auth_identifier_login($session_login, $session_password);
												 }else{
													 $idUsuario =base64_decode(_request('idUsuario'));
													 $sql = sql_select('*','api_auteurs','id_auteur="'.$idUsuario.'"');
													 while ($row = sql_fetch($sql)) {	
														   $aut=$row; 
													 }
												 }
												if($accion !='consulta'){
													$audi=array();
													$audi['id_auteur']=$aut['id_auteur'];
													$audi['usuario']=$aut['nom'];
													$audi['rol']=$aut['tipo'];
													$audi['menu']=$menu;
													$audi['submenu']=$submenu;
													$audi['accion']=$accion;
													$audi = pipeline('pre_insertion',
													array(
														'args' => array(
														'table' => 'apis_auditoria',
													),
													'data' => $audi
													)
												);							
												$idAuditoria=@sql_insertq('apis_auditoria',$audi);
												pipeline('post_insertion',
												array(
													'args' => array(
													'table' =>'apis_auditoria',
													'id_objet' => $idAuditoria
													),
													'data' => $audi
													)
												);
												}

											 return $aut;
		}
				/**
		 * Retorno los parametros para actualizar en una tabla
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : actualizar()
		 * Parametros de entrada :$chartic=array(),$id_nom,$id
		 * Parametros de Salida: 
		 */ 		
		
		function actualizarDatos($chartic = array(), $id_nom, $id) {
		  try {
			// Validación de entrada
			if (!is_array($chartic)) {
			  throw new Exception('Los datos deben ser un arreglo');
			}
			if (empty($id_nom) || empty($id)) {
			  throw new Exception('El nombre del campo ID y el valor del ID son requeridos');
			}

			// Pre-inserción
			$chartic = pipeline('pre_update', array(
			  'args' => array('table' => $this->table),
			  'data' => $chartic
			));

			// Actualización en la base de datos
			sql_updateq($this->table, $chartic, "$id_nom = '" . sql_quote($id) . "'");

			// Post-inserción
			pipeline('post_update', array(
			  'args' => array('table' => $this->table, 'id_objet' => $id),
			  'data' => $chartic
			));

			return $id;
		  } catch (Exception $e) {
			// Manejo de errores
			error_log($e->getMessage());
			throw $e;
		  }
		}
		/**
		 * Retorno los parametros para actualizar en una tabla
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : datosusuario()
		 * Parametros de entrada : query:
		 * Parametros de Salida:  row
		 */			
		public function consultadatos($query, $select, $campos) {
			$sql = sql_select('' . $select . '', '' . $this->table . '', '' . $query . '');
			$datos = array();
			while ($row = sql_fetch($sql)) {
				$fila = array();
				foreach ($campos as $campo) {
					if (array_key_exists($campo, $row)) {
						$fila[$campo] = $row[$campo];
					} else {
						$fila[$campo] = null;
					}
				}
				$datos[] = $fila;
			}
			return $datos;
		}
		
		public function generarCredenciales($var_auth) {
			
			$query='id_auteur="'.$var_auth['id_auteur'].'"';
			$select='nom,login,email,status,tipo';
			
			$usuario=$this->consultaExiste($query,$select,$this->table);
			$users=$usuario['datos'][0];
			/// print_r($usuario['datos']);
			$secretKey = $this->generateSecretKey();
			$encryptedData = $this->encryptData('evaluasoft', $secretKey);

			spip_setcookie('ApiSecretKey', $secretKey, [
				'expires' => time() + 3600 * 24 * 14
			]);
				$credenciales = [
				'Nom' => $users['nom'] ?? '',
				'Idsuario' =>$var_auth['id_auteur'] ?? 0,
				'Usuario' => $users['nom'] ?? '',
				'Email' => $users['email'] ?? '',
				'status' => $users['status'],
				'Rol' => $users['tipo'],
				'AppKey' => $encryptedData,
				'AppToken' => $secretKey,
			];

			return $credenciales;
		}
}