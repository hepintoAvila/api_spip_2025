<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class AulaService {
    private $app;

    public function __construct() {

        $this->apis= new General('upc_aulas');
     }
	public function addAula($data){
		$chartic=array();
		if (!is_array($data)) {
            throw new Exception('El parámetro $data debe ser un array');
        }
			$chartic=array(
					  'id' => $data['id'],
					  'title' => $data['title'],
					  'className' => $data['className'],
					  'textClass' => $data['textClass']
					);
					try {
					  $idPc =$this->apis->guardarDatos($chartic);
					 return $idPc;
					} catch (Exception $e) {
						$records['data'] = array('status' => 401, 'error' => $e->getMessage());
					  header('Content-Type: application/json');
					  http_response_code(401);
					  echo json_encode($records);
					  exit;
					}
	}	
	public function updateAula($data){
			if (!is_array($data)) {
				throw new Exception('El parámetro $data debe ser un array');
			}

			// Validar que el id_turno esté presente en el array
			if (!isset($data['id'])) {
				throw new Exception('El parámetro id_turno es obligatorio');
			}

			// Crear el array de datos para actualizar
			$chartic = array();
			foreach ($data as $key => $value) {
				// Ignorar el id_turno ya que se utiliza para la condición de actualización
				if ($key !== 'id') {
					$chartic[$key] = $value;
				}
			}
 
			try {
				
				$this->apis->actualizarDatos($chartic, 'id', $data['id']);
			} catch (Exception $e) {
				$records['data'] = array('status' => 401, 'error' => $e->getMessage());
				header('Content-Type: application/json');
				http_response_code(401);
				echo json_encode($records);
				exit;
			}
		}
	public function deleteAula($data){
		sql_delete("upc_aulas","id=" . intval($data['id']));
	}
	public function getAula() {
		  $from = 'upc_aulas AS R';
		  $select = 'R.id,R.title,R.textClass,R.className';
		  $where = 'R.statut = "Activo" ORDER BY R.maj ASC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$pcs = array();
			while ($row = sql_fetch($sql)) {
				$pcs[] = $row;
			}
			$datosPcs = array('Aulas' => array());
			foreach ($pcs as $val) {
			  $datosPcs['Aulas'][] = array(
					  'id' => $val['id'],
					  'title' => $val['title'],
					  'className' => $val['className'],
					  'textClass' => $val['textClass']
			  );
			}
			
			if (!empty($datosPcs['Aulas'])) {
					$prestamo=$this->getTurnosAulas();
				if (!is_array($prestamo)) {
					$prestamo['Prestamos']=array();
				}	
					$result = array_merge($datosPcs, $prestamo);
				    $var = var2js(array('status'=>200,'type'=>'success','data'=>$result,'message'=>'Listado de Aulas y prestamos')); 	
					echo $var;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Aulas');
				  $var = var2js($records);
				  echo $var;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
		
	private function getclassName($title) {
		  $from = 'upc_aulas AS R';
		  $select = 'R.className';
		  $where = 'R.title = "'.$title.'"';
		  $sql = sql_select($select, $from, $where);
			$pcs = array();
			while ($row = sql_fetch($sql)) {
				return $row['className'];
			}
	}		
	private function getTurnosAulas() {
		  $from = 'upc_aulas_prestamos';
		  $select = 'id,title,start,end,documento,className';
		  $where = 'statut = "Activo" ORDER BY maj DESC';
		  $sql = sql_select($select, $from, $where);
		  try {
			$turnos = array();
			while ($row = sql_fetch($sql)) {
				$turnos[] = $row;
			}
			$datosTurnos = array('Prestamos' => array());
			foreach ($turnos as $val) {
			  $datosTurnos['Prestamos'][] = array(
				'idPrestamo' => $val['id'],
				'title' => $val['title'],
				'start' => $val['start'],
				'end' => $val['end'],
				'documento' => $val['documento'],
				'className' => $val['className']
			  );
			}
			if (!empty($datosTurnos['Prestamos'])) {
				    return $datosTurnos;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Aulas');
				  $var = var2js($records);
				  echo $var;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}		
}