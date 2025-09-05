<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class UsuarioService {
	private $apis;
	private $estudiantes;
    public function __construct() {
     $this->apis = new General('api_auteurs');
     $this->estudiantes = new General('upc_estudiantes');
	}
	public function addUsuarios($chartic){
		$this->apis->guardarDatos($chartic);
	}
	private function getDocumento($documento) {
        $sql = sql_select('id', 'upc_estudiantes', 'PEGE_DOCUMENTOIDENTIDAD = ' . sql_quote($documento));
        $row = sql_fetch($sql);
        if (!$row) {
           return '0';
        }
        return $row['id'];
    }	
	public function addEstudiante($data){
			if (!is_array($data)) {
            throw new Exception('El parámetro $chartic debe ser un array');
        }	
				$id = $this->getDocumento($data['documento']);
				if(intval($id)==0){
				$chartic['MATE_CODIGOMATERIA']='';
				$chartic['GRUP_NOMBRE']='';
				$chartic['PEGE_DOCUMENTOIDENTIDAD']=$data['documento'];
				$chartic['ESTUDIANTE']=$data['documento'];
				$chartic['MATE_NOMBRE']='';
				$chartic['PEGE_MAIL']='';
				$chartic['PROG_NOMBRE']=$data['programa'];
				$chartic['PEGE_TELEFONOCELULAR']='';
				$chartic['PEGE_TELEFONO']='';
				$chartic['PENG_EMAILINSTITUCIONAL']='';
				$this->estudiantes->guardarDatos($chartic);
				}
	}
	public function updateUsuarios($data){
			if (!is_array($data)) {
				throw new Exception('El parámetro $data debe ser un array');
			}

			// Validar que el id_turno esté presente en el array
			if (!isset($data['id_auteur'])) {
				throw new Exception('El parámetro id_auteur es obligatorio');
			}

			// Crear el array de datos para actualizar
			$chartic = array();
			foreach ($data as $key => $value) {
				// Ignorar el id_turno ya que se utiliza para la condición de actualización
				if ($key !== 'id_auteur') {
					$chartic[$key] = $value;
				}
			}

			try {
				$this->apis->actualizarDatos($chartic, 'id_auteur', $data['id_auteur']);
			} catch (Exception $e) {
				$records['data'] = array('status' => 401, 'error' => $e->getMessage());
				header('Content-Type: application/json');
				http_response_code(401);
				echo json_encode($records);
				exit;
			}
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
		public function getEstudiantes($data) {
			
		  $from = 'upc_estudiantes AS R';
		  $select = 'R.id as id_estudiante, R.PEGE_DOCUMENTOIDENTIDAD AS documento,
		  R.ESTUDIANTE AS nombres,R.PENG_EMAILINSTITUCIONAL as email,R.PROG_NOMBRE as programa,R.PEGE_TELEFONOCELULAR as celular';
		  $where = 'R.PEGE_DOCUMENTOIDENTIDAD = "'.intval($data['documento']).'" AND R.status = "Activo" ORDER BY R.id DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$usuarios = array();
			while ($row = sql_fetch($sql)) {
				$usuarios[] = $row;
			}
			$datosusuarios = array('Estudiantes' => array());
			foreach ($usuarios as $val) {
			  $datosusuarios['Estudiantes'][] = array(
				'id_estudiante' => $val['id_estudiante'],
				'documento' => $val['documento'],
				'nombres' => $val['nombres'],
				'email' => $val['email'],
				'programa' => $val['programa'],
				'celular' => $val['celular'],
			  );
			}
			if (!empty($datosusuarios['Estudiantes'])) {
					$records = array('status'=>200,'type'=>'success','data'=>$datosusuarios, 'message'=>'Listodo de Estudiantes');
				  return $records;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Estudiantes');
				  return $records;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
		
}