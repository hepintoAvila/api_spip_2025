<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class VisitaService {
	private $apis;
    public function __construct() {
     $this->apis = new General('upc_libros_visita');
	}
	public function addVisitas($chartic){
		$apis->guardarDatos($chartic);
	}	
	public function updateVisitas($chartic,$arg1,$arg2){
		$apis->actualizarDatos($chartic,$arg1,$arg2);
	}
	public function deleteVisitas($arg1){
		sql_delete("upc_libros_visita","id_visita=" . intval($arg1));
	}
	
		public function consultaDocumento($identificacion){
		  $usuario = array();
		  $from = 'upc_usuarios_biblioteca_koha AS R';
		  $select = 'R.id,R.prog_nombre AS programa,R.nombres,R.apellidos,R.email,R.celular';
		  $where = 'R.identificacion = "'.$identificacion.'"';
		   try {
				$sql = sql_select($select, $from, $where);			
				while ($row = sql_fetch($sql)) {
					$usuario[] = $row;
				}
				
				$datosUsuario = array('Usuario' => array());
				foreach ($usuario as $val) {
				  $datosUsuario['Usuario'][]= array(
					'id' => $val['id'],
					'identificacion' => $identificacion,
					'apellidos' => $val['apellidos'],
					'nombres' => $val['nombres'],
					'telefono' => $val['celular'],
					'programa' => $val['programa'],
					'email' => $val['email']
				  );
				}
				
				if (!empty($datosUsuario['Usuario'])) {
					$records = array('status'=>202,'type'=>'success','data'=>$datosUsuario, 'message'=>'Registro del Usuario');
				  return $records;
				} else {
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros del Usuario');
				  return $records;
				}
			} catch (Exception $e) {
				return json_encode(array('error' => $e->getMessage()));
		  }
		}
		public function getIdJornada($fecha_creacion) {

				$datetime = new DateTime($fecha_creacion);
				$hora = $datetime->format('H');
				$minutos = $datetime->format('i');
				$jornada = ($hora < 12) ? 'Mañana' : 'Tarde';
				
				$from = 'upc_tipo_jornada AS R';
				$select = 'R.id_tipo AS jornada';
				$where = 'R.titulo = "'.$jornada.'"';				
				$sql = sql_select($select, $from, $where);			
				while ($row = sql_fetch($sql)) {
					return $row['jornada'];
				}			
		}
		public function getIdsVisita($tipo,$titulo) {

						switch($tipo) {
						case "tipo_visita":
							$from = 'upc_tipos_visitas AS R';
							$select = 'R.id_tipo AS id';
							$where = 'R.titulo = "'.$titulo.'"';				
							$sql = sql_select($select, $from, $where);								 
						break;
						case "programa":
							$from = 'upc_tipo_programas AS R';
							$select = 'R.id_programa AS id';
							$where = 'R.nombre_programa = "'.$titulo.'"';				
							$sql = sql_select($select, $from, $where);								 
						break;
						case "ubicacion":
							$from = 'upc_tipo_ubicaciones AS R';
							$select = 'R.id_tipo AS id';
							$where = 'R.titulo = "'.$titulo.'"';				
							$sql = sql_select($select, $from, $where);								 
						break;						
						}
				
						
				while ($row = sql_fetch($sql)) {
					return $row['id'];
				}			
		}
		public function getVisitas() {
		  $from = 'upc_libros_visita AS LV
					  INNER JOIN upc_tipos_visitas AS TV ON LV.tipo_visita = TV.id_tipo
					  INNER JOIN upc_tipo_jornada AS J ON LV.jornada = J.id_tipo
					  INNER JOIN upc_tipo_ubicaciones AS U ON LV.ubicacion = U.id_tipo
					  INNER JOIN upc_tipo_programas AS P ON LV.programa = P.id_programa';
		  $select = 'LV.id_visita,
					  LV.identificacion,
					  TV.titulo AS tipo_visita,
					  J.titulo AS jornada,
					  U.titulo AS ubicacion,
					  P.nombre_programa AS programa,
					  LV.fecha_creacion';
		  $where = 'LV.statut = "Activo" ORDER BY LV.id_visita DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$visitas = array();
			while ($row = sql_fetch($sql)) {
				$visitas[] = $row;
			}
			$datosVisitas = array('Visitas' => array());
			foreach ($visitas as $val) {
			  $datosVisitas['Visitas'][] = array(
				'id_visita' => $val['id_visita'],
				'identificacion' => $val['identificacion'],
				'tipo_visita' => $val['tipo_visita'],
				'jornada' => $val['jornada'],
				'ubicacion' => $val['ubicacion'],
				'programa' => $val['programa'],
				'fecha_creacion' => $val['fecha_creacion']
			  );
			}
			if (!empty($datosVisitas['Visitas'])) {
					$records = array('status'=>200,'type'=>'success','data'=>$datosVisitas, 'message'=>'Listodo de Visitas');
				  return $records;
				} else {
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Visitas');
				  return $records;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
		
}