<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class TurnosService {
    private $app;
    private $apis;
    private $apis_pcs;
    public $credencials;
    private $id_rol;

    public function __construct($credencials) {
          $this->credencials= $credencials;
          $this->apis= new General('upc_turnos');
          $this->apis_pcs= new General('upc_pcs');
          $this->id_rol= $credencials['id_rol'];
          
     }
public function addTurnos($data){
		
		$chartic=array();
		if (!is_array($data)) {
            throw new Exception('El parámetro $data debe ser un array');
        }
									
		$chartic=array(
		  'pc' => $data['pc'],
		  'documento' => $data['documento'],
		  'tipo_prestamo' => $data['tipo_prestamo'],
		  'ubicacion' =>intval($this->id_rol),
		);
	   
		try {
		
			 
			 
			$from = 'upc_pcs';
			$select = 'id_pc';
			$where = 'numero = "'.intval($data['pc']).'"';
			$sql = sql_select($select, $from, $where);
				  
			while ($row = sql_fetch($sql)) {
				$id_pc = $row['id_pc'];
			}				
			$chartic_pc=array(
		      'estado' => 'Ocupado',
		      'id_pc' => $id_pc
			);
			$this->updatePc($chartic_pc);
			$this->getTurnosEstudiante($data);
			$idPc =$this->apis->guardarDatos($chartic);			
			
		} catch (Exception $e) {
			$records['data'] = array('status' => 401, 'error' => $e->getMessage());
			header('Content-Type: application/json');
			http_response_code(401);
			echo json_encode($records);
			exit; // Agrega esta línea para detener la ejecución del script
		}
}
	public function updatePc($data){
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
				$this->apis_pcs->actualizarDatos($chartic, 'id_pc', $data['id_pc']);
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
		  upc_tipo_prestamos pres ON t.tipo_prestamo = pres.id
		  INNER JOIN
		  upc_estudiantes u ON t.documento = u.PEGE_DOCUMENTOIDENTIDAD
		  INNER JOIN
		  upc_tipo_ubicaciones tu ON t.ubicacion = tu.id_tipo';
		  $select = 't.id_turno,
			  pres.tipo AS tipo_prestamos,
			  p.numero AS numero_pc,
			  u.ESTUDIANTE AS nombre_estudiante,
			  t.fecha_creacion,
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
				'fecha_inicial' => $val['fecha_creacion'],
				'fecha_final' => $val['fecha_creacion'],
				'tipo_prestamo' => $val['tipo_prestamos'],
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
		
	public function countTurnos() {
    	$select = 'COUNT(*) as total'; 
    	$from = 'upc_resumen_turnos';	
    	$where = ' mes = MONTH(NEW.fecha_creacion)  AND dia = DAY(NEW.fecha_creacion)';    
         $sql = sql_select($select, $from, $where);
 		while ($row = sql_fetch($sql)) {
				return  $row['total'];
			}   
	}

                         
	public function getTurnos() {
		  $from = 'upc_turnos t 
		  INNER JOIN
		  upc_pcs p ON t.pc = p.numero
		  INNER JOIN
		  upc_tipo_prestamos pres ON t.tipo_prestamo = pres.id
		  INNER JOIN
		  upc_estudiantes u ON t.documento = u.PEGE_DOCUMENTOIDENTIDAD
		  INNER JOIN
		  upc_tipo_ubicaciones tu ON t.ubicacion = tu.id_tipo';
		  $select = 't.id_turno,
			  pres.tipo AS tipo_prestamos,
			  p.numero AS numero_pc,
			  u.ESTUDIANTE AS nombre_estudiante,
			  t.fecha_creacion,
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
				'fecha_inicial' => $val['fecha_creacion'],
				'fecha_final' => $val['fecha_creacion'],
				'sala' => $val['sala'],
				'tipo_prestamos' => $val['tipo_prestamos'],
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