<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class TurnosService {
    private $app;
    private $apis;
    private $apis_pcs;

    public function __construct() {
          $this->apis= new General('upc_turnos');
          $this->apis_pcs= new General('upc_pcs');
     }
	public function addTurnos($data){
		
		$chartic=array();
		if (!is_array($data)) {
            throw new Exception('El parámetro $data debe ser un array');
        }
					
					
					$chartic=array(
					  'pc' => $data['pc'],
					  'documento' => $data['documento'],
					  'fecha_inicial' => $data['fecha_inicial'],
					  'fecha_final' => $data['fecha_final'],
					  'ubicacion' => $data['ubicacion'],
					);

					try {
						$idPc =$this->apis->guardarDatos($chartic);
							  
							  $from = 'upc_pcs';
							  $select = 'id_pc';
							  $where = 'numero = "'.intval($data['pc']).'"';
							  $sql = sql_select($select, $from, $where);
							  
						while ($row = sql_fetch($sql)) {
							$id_pc = $row['id_pc'];
						}				
						$chartic_pc=array(
					      'estado' => 'Ocupado',
						);
						sql_updateq('upc_pcs', $chartic_pc, "id_pc = '" . sql_quote($id_pc) . "'");
					  
					  return $idPc;
					} catch (Exception $e) {
						$records['data'] = array('status' => 401, 'error' => $e->getMessage());
					  header('Content-Type: application/json');
					  http_response_code(401);
					  echo json_encode($records);
					  exit;
					}
	}	
	public function updateTurnos($data){
			if (!is_array($data)) {
				throw new Exception('El parámetro $data debe ser un array');
			}

			// Validar que el id_turno esté presente en el array
			if (!isset($data['id_turno'])) {
				throw new Exception('El parámetro id_turno es obligatorio');
			}

			// Crear el array de datos para actualizar
			$chartic = array();
			foreach ($data as $key => $value) {
				// Ignorar el id_turno ya que se utiliza para la condición de actualización
				if ($key !== 'id_turno') {
					$chartic[$key] = $value;
				}
			}

			try {
				$this->apis->actualizarDatos($chartic, 'id_turno', $data['id_turno']);
			} catch (Exception $e) {
				$records['data'] = array('status' => 401, 'error' => $e->getMessage());
				header('Content-Type: application/json');
				http_response_code(401);
				echo json_encode($records);
				exit;
			}
		}
	public function deleteTurnos($data){
		sql_delete("upc_turnos","id_turno=" . intval($data['id_turno']));
	}
	public function getTurnosEstudiante($data) {
		  $from = 'upc_turnos t 
		  INNER JOIN
		  upc_pcs p ON t.pc = p.numero
		  INNER JOIN
		  upc_estudiantes u ON t.documento = u.PEGE_DOCUMENTOIDENTIDAD
		  INNER JOIN
		  upc_tipo_ubicaciones tu ON t.ubicacion = tu.id_tipo';
		  $select = 't.id_turno,
			  p.numero AS numero_pc,
			  u.ESTUDIANTE AS nombre_estudiante,
			  t.fecha_inicial,
			  t.fecha_final,
			  tu.titulo AS sala,
			  t.statut';
		  $where = 't.documento = "'.$data['documento'].'" AND t.statut = "Activo" ORDER BY t.fecha_creacion DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$turnos = array();
			while ($row = sql_fetch($sql)) {
				$turnos[] = $row;
			}
			
			$datosTurnos = array('Turno' => array());
			foreach ($turnos as $val) {
			  $datosTurnos['Turno'][] = array(
				'id_turno' => $val['id_turno'],
				'numero' => $val['numero_pc'],
				'nombre_estudiante' => $val['nombre_estudiante'],
				'fecha_inicial' => $val['fecha_inicial'],
				'fecha_final' => $val['fecha_final'],
				'sala' => $val['sala'],
				'statut' => $val['statut'],
			  );
			}
			if (!empty($datosTurnos['Turno'])) {
				    $var = var2js(array('status'=>200,'type'=>'success','data'=>$datosTurnos,'message'=>'Listado de Turno')); 	
					echo $var;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Turno');
				  $var = var2js($records);
				  echo $var;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
	public function getTurnos() {
		  $from = 'upc_turnos t 
		  INNER JOIN
		  upc_pcs p ON t.pc = p.numero
		  INNER JOIN
		  upc_estudiantes u ON t.documento = u.PEGE_DOCUMENTOIDENTIDAD
		  INNER JOIN
		  upc_tipo_ubicaciones tu ON t.ubicacion = tu.id_tipo';
		  $select = 't.id_turno,
			  p.numero AS numero_pc,
			  u.ESTUDIANTE AS nombre_estudiante,
			  t.fecha_inicial,
			  t.fecha_final,
			  tu.titulo AS sala,
			  t.statut';
		  $where = 't.statut = "Activo" ORDER BY t.fecha_creacion DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$turnos = array();
			while ($row = sql_fetch($sql)) {
				$turnos[] = $row;
			}
			$datosTurnos = array('Turnos' => array());
			foreach ($turnos as $val) {
			  $datosTurnos['Turnos'][] = array(
				'id_turno' => $val['id_turno'],
				'numero' => $val['numero_pc'],
				'nombre_estudiante' => $val['nombre_estudiante'],
				'apellido_estudiante' => $val['apellido_estudiante'],
				'fecha_inicial' => $val['fecha_inicial'],
				'fecha_final' => $val['fecha_final'],
				'sala' => $val['sala'],
				'statut' => $val['statut'],
			  );
			}
			if (!empty($datosTurnos['Turnos'])) {
				    $var = var2js(array('status'=>200,'type'=>'success','data'=>$datosTurnos,'message'=>'Listado de Turnos')); 	
					echo $var;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Turno');
				  $var = var2js($records);
				  echo $var;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
		
}