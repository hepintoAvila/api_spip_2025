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
include_spip('inc/filtres_boites');
include_spip('inc/boutons');
include_spip('inc/pipelines_ecrire');
include_spip('inc/filtres_dates');
include_spip('base/connect_sql');

 abstract class PagesApis {
        public function __construct() {
		//$this->periodoacademico_id=$periodoacademico_id;		
        } 
		abstract function guardarAudi($menu,$submenu,$accion,$aut);
		abstract function consultadatos($query,$select,$campos);
		abstract function actualizarDatos($chartic,$id_nom,$id);
		abstract function guardarDatos($chartic);
		
		//abstract function mergeServicios($arrayA, $arrayB);
 }
 class General extends PagesApis
{
         public $table;
		 
		 public function __construct($table)
         {			
			$this->table=$table;
		 }
		 /**
		 * Retorno los parametros para verificar
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : guardarDatos()
		 * Parametros de entrada : $chartic
		 * Parametros de Salida: id
		 */		 
		 
			public function guardarDatos($chartic) {
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
		 * Retorno los parametros para guardar en una tabla
		 * Autor: HOSMMER EDUARDO PINTO ROJAS
		 * Nombre de la Funcion : general_gardar_registro()
		 * Parametros de entrada :$chartic=array(),$table
		 * Parametros de Salida: 
		 */ 
		public function guardarAudi($menu,$submenu,$accion,$aut){
				
											 	//AUDITORIA			
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
		
		public function actualizarDatos($chartic = array(), $id_nom, $id) {
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
		
}