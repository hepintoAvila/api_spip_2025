<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class RolService {
	private $apis;
    public $data;

    public function __construct($data) {
     $this->data = $data;
     $this->apis = new General('apis_roles');
	}
	public function addRoles($chartic){
		$this->apis->guardarDatos($chartic);
	}
	public function updateRoles($data){
			if (!is_array($data)) {
				throw new Exception('El parámetro $data debe ser un array');
			}

			// Validar que el id_turno esté presente en el array
			if (!isset($data['idRol'])) {
				throw new Exception('El parámetro idRol es obligatorio');
			}

			// Crear el array de datos para actualizar
			$chartic = array();
			foreach ($data as $key => $value) {
				// Ignorar el id_turno ya que se utiliza para la condición de actualización
				if ($key !== 'idRol') {
					$chartic[$key] = $value;
				}
			}

			try {
				$this->apis->actualizarDatos($chartic, 'idRol', $data['idRol']);
			} catch (Exception $e) {
				$records['data'] = array('status' => 401, 'error' => $e->getMessage());
				header('Content-Type: application/json');
				http_response_code(401);
				echo json_encode($records);
				exit;
			}
		}	
	
	public function deleteRoles($arg1){
		sql_delete("apis_roles","idRol=" . intval($arg1));
	}
 
		public function getRoles() {
		  $from = 'apis_roles AS R';
		  $select = 'R.idRol,R.tipo,R.status';
		  $where = 'R.status = "Activo" ORDER BY R.idRol DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$roles = array();
			while ($row = sql_fetch($sql)) {
				$roles[] = $row;
			}
			$datosRoles = array('Roles' => array());
			foreach ($roles as $val) {
			  $datosRoles['Roles'][] = array(
				'idRol' => $val['idRol'],
				'tipo' => $val['tipo'],
				'status' => $val['status'],
			  );
			}
			if (!empty($datosRoles['Roles'])) {
					$records = array('status'=>200,'type'=>'success','data'=>$datosRoles, 'message'=>'Listodo de Roles');
				  return $records;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Roles');
				  return $records;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
		
}