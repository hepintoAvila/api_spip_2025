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
		$apis->guardarDatos($chartic);
	}	
	public function updateRoles($chartic,$arg1,$arg2){
		$apis->actualizarDatos($chartic,$arg1,$arg2);
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