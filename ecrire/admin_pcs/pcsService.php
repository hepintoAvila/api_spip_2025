<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class PcsService {
    private $app;

    public function __construct() {
        //$this->data = $data;
        //$this->resdataCredencials = $resdataCredencials;
        $this->apis= new General('upc_pcs');
     }
	public function addPcs($data){
		$chartic=array();
		if (!is_array($data)) {
            throw new Exception('El parámetro $data debe ser un array');
        }
			$chartic=array(
					  'numero' => $data['numero'],
					  'ip' => $data['ip'],
					  'estado' => $data['estado']
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
				
				$this->apis->actualizarDatos($chartic, 'id_pc', $data['id_pc']);
			} catch (Exception $e) {
				$records['data'] = array('status' => 401, 'error' => $e->getMessage());
				header('Content-Type: application/json');
				http_response_code(401);
				echo json_encode($records);
				exit;
			}
		}
	public function deletePcs($data){
		sql_delete("upc_pcs","id_pc=" . intval($data['id_pc']));
	}
	public function getPcs() {
		  $from = 'upc_pcs AS R';
		  $select = 'R.id_pc,R.numero,R.ip,R.estado';
		  $where = 'R.status = "Activo" ORDER BY R.maj DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$pcs = array();
			while ($row = sql_fetch($sql)) {
				$pcs[] = $row;
			}
			$datosPcs = array('Pc' => array());
			foreach ($pcs as $val) {
			  $datosPcs['Pc'][] = array(
				'id_pc' => $val['id_pc'],
				'numero' => $val['numero'],
				'ip' => $val['ip'],
				'estado' => $val['estado'],
			  );
			}
			if (!empty($datosPcs['Pc'])) {
				    $var = var2js(array('status'=>200,'type'=>'success','data'=>$datosPcs,'message'=>'Listado de Pcs')); 	
					echo $var;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de PCs');
				  $var = var2js($records);
				  echo $var;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
		
}