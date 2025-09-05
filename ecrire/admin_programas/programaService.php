<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class ProgramaService {
    private $app;

    public function __construct() {

        $this->apis= new General('upc_programas');
     }
	public function addPrograma($data){
		$chartic=array();
		if (!is_array($data)) {
            throw new Exception('El parámetro $data debe ser un array');
        }
			$chartic=array(
					  'id' => $data['id'],
					  'nombre_programa' => $data['programa']
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
	public function updatePrograma($data){
			if (!is_array($data)) {
				throw new Exception('El parámetro $data debe ser un array');
			}

			// Validar que el id_turno esté presente en el array
			if (!isset($data['id_programa'])) {
				throw new Exception('El parámetro id_turno es obligatorio');
			}

			// Crear el array de datos para actualizar
			$chartic = array();
			foreach ($data as $key => $value) {
				// Ignorar el id_turno ya que se utiliza para la condición de actualización
				if ($key !== 'id_programa') {
					$chartic[$key] = $value;
				}
			}
 
			try {
				
				$this->apis->actualizarDatos($chartic, 'id_programa', $data['id']);
			} catch (Exception $e) {
				$records['data'] = array('status' => 401, 'error' => $e->getMessage());
				header('Content-Type: application/json');
				http_response_code(401);
				echo json_encode($records);
				exit;
			}
		}
	public function deletePrograma($data){
		sql_delete("upc_tipo_programas","id_programa=" . intval($data['id']));
	}
	public function getPrograma() {
		  $from = 'upc_tipo_programas AS R';
		  $select = 'R.id_programa,R.nombre_programa';
		  $where = 'R.status = "Activo" ORDER BY R.maj ASC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$pcs = array();
			while ($row = sql_fetch($sql)) {
				$pcs[] = $row;
			}
			$datosPcs = array('Programas' => array());
			foreach ($pcs as $val) {
			  $datosPcs['Programas'][] = array(
				'id' => $val['id_programa'],
				'programa' => $val['nombre_programa'],
			  );
			}
			if (!empty($datosPcs['Programas'])) {
				    $var = var2js(array('status'=>200,'type'=>'success','data'=>$datosPcs,'message'=>'Listado de Programas')); 	
					echo $var;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Programas');
				  $var = var2js($records);
				  echo $var;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
		
}