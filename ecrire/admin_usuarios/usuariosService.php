<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class UsuarioService {
	private $apis;
    public function __construct() {
     $this->apis = new General('api_auteurs');
	}
	public function addUsuarios($chartic){
		$apis->guardarDatos($chartic);
	}	
	public function updateUsuarios($chartic,$arg1,$arg2){
		$apis->actualizarDatos($chartic,$arg1,$arg2);
	}
	public function deleteUsuarios($arg1){
		sql_delete("api_auteurs","id_auteur=" . intval($arg1));
	}
 
		public function getUsuarios() {
		  $from = 'api_auteurs AS R';
		  $select = 'R.id_auteur,R.nom AS nombres,R.email,R.login,R.tipo';
		  $where = 'R.status = "Activo" ORDER BY R.id_auteur DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$usuarios = array();
			while ($row = sql_fetch($sql)) {
				$usuarios[] = $row;
			}
			$datosusuarios = array('Usuarios' => array());
			foreach ($usuarios as $val) {
			  $datosusuarios['Usuarios'][] = array(
				'id_auteur' => $val['id_auteur'],
				'nombres' => $val['nombres'],
				'email' => $val['email'],
				'login' => $val['login'],
				'tipo' => $val['tipo'],
			  );
			}
			if (!empty($datosusuarios['Usuarios'])) {
					$records = array('status'=>200,'type'=>'success','data'=>$datosusuarios, 'message'=>'Listodo de Usuarios');
				  return $records;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Usuarios');
				  return $records;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
		
}