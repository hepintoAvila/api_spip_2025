<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
		include_spip('exec/model/apis/claseapi');
	/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/ 	
class RolService {
  private $apis;
  private $autorizaciones;
 
    public function __construct() {
    $this->apis = new Apis('api_auteurs');
    $this->autorizaciones = new Apis('apis_autorizaciones');
	}
	 
	 public function validarAutorizaciones($data) {
		  $select = 'c,u,d,a';
		  $query = 'idMenu = "'.$data['idMenu'].'" AND idSubmenu="'.$data['idSubmenu'].'" AND idRol="'.$data['idRol'].'"'; 
		   try {
			$existe= $this->autorizaciones->consultaExiste($query,$select,'apis_autorizaciones');
			return $existe;
		} catch (Exception $e) {
				return array('status' => '500','error' => $e->getMessage());
		}
	 }
	 public function consultarRolesAutorizaciones($var_auth) {
		try {
		  $datosRol = $this->getRoles();
		  $auteurs = $this->apis->generarCredenciales($var_auth);
		  $resultado = array('status' => '202', 'data' => $datosRol, 'Usuarios' => $auteurs);
			return $resultado;
		} catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		}
	  }	
		public function consultaRoles() {
		  $roles = array();
		  $from = 'apis_roles AS R';
		  $select = 'R.tipo AS rol,R.idRol';
		  $where = 'R.status = "Activo" ORDER BY R.idRol ASC';
		   try {
				$sql = sql_select($select, $from, $where);			
				while ($row = sql_fetch($sql)) {
					$roles[] = $row;
				}
				return $roles;
			} catch (Exception $e) {
				return json_encode(array('error' => $e->getMessage()));
		  }
		}
		
		public function getRoles() {
		  $from = 'apis_menu AS M, apis_submenus AS S, apis_autorizaciones AS A, apis_roles AS R';
		  $select = 'R.idRol,A.id as id_autorizacion, M.label AS menu, S.label AS submenu, R.tipo AS rol, A.c as c, A.u as u,A.d as d,A.a as a,S.idSubmenu, M.idMenu, R.idRol';
		  $where = 'R.idRol = A.idRol AND M.idMenu = A.idMenu AND S.idSubmenu = A.idSubmenu AND A.idSubmenu = S.idSubmenu AND S.status = "Active" ORDER BY S.idSubmenu ASC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$roles = array();
			while ($row = sql_fetch($sql)) {
				$roles[] = $row;
			}
			//print_r($roles);
			$datosRol = array('Roles' => array());
			foreach ($roles as $val) {
			  $datosRol['Roles'][] = array(
				'id' => $val['id_autorizacion'],
				'menu' => $val['menu'],
				'submenu' => $val['submenu'],
				'rol' => $val['rol'],
				'c' => $val['c'],
				'u' => $val['u'],
				'd' => $val['d'],
				'a' => $val['a'],
				'items' => array(
				  'idRol' => $val['idRol'],
				  'id_autorizacion' => $val['id_autorizacion'],
				  'idSubmenu' => $val['idSubmenu'],
				  'idMenu' => $val['idMenu'],
				  'menu' => $val['menu'],
				  'submenu' => $val['submenu'],
				),
			  );
			}
			if (!empty($datosRol['Roles'])) {
			  
			  return $datosRol;
			} else {
			  $records = array('status' => '404');
			  return $records;
			}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
		public function copyRoles($data) {
			
		  $from = 'apis_menu AS M, apis_submenus AS S, apis_autorizaciones AS A, apis_roles AS R';
		  $select = 'R.idRol,A.id as id_autorizacion, M.label AS menu, S.label AS submenu, R.tipo AS rol, A.c as c, A.u as u,A.d as d,A.a as a,S.idSubmenu, M.idMenu, R.idRol';
		  $where = 'R.idRol ="'.intval($data['idRol_copy']).'" AND R.idRol = A.idRol AND M.idMenu = A.idMenu AND S.idSubmenu = A.idSubmenu AND A.idSubmenu = S.idSubmenu AND S.status = "Active" ORDER BY S.idSubmenu ASC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$roles = array();
			while ($row = sql_fetch($sql)) {
				$roles[] = $row;
			}
			$datosRol = array('Roles' => array());
			foreach ($roles as $val) {
			  $datosRol['Roles'][] = array(
				'idMenu' => $val['idMenu'],
				'idSubmenu' => $val['idSubmenu'],
				'idRol' => $data['idRol'],
				'c' => $val['c'],
				'u' => $val['u'],
				'd' => $val['d'],
				'a' => $val['a'],
			  );
			}
			if (!empty($datosRol['Roles'])) {
			  
			  return $datosRol;
			} else {
			  $records = array('status' => '404');
			  return $records;
			}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}		
}