<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class MenuService {
    private $apis;
    private $SubMenu;
    private $idRol;
    public $data;

    public function __construct($data) {
        $this->data = $data;
        $this->apis = new General('apis_menu');
        $this->SubMenu = new General('apis_submenus');
        $this->idRol = $this->getIdRol($this->data['tipo']);
    }
      private function getIdRol($arg1) {
        $sql = sql_select('idRol', 'apis_roles', 'tipo = ' . sql_quote($arg1));
        if (!$sql) {
          throw new Exception('Error al consultar roles');
        }
        $row = sql_fetch($sql);
        if (!$row) {
          throw new Exception('No se encontraron roles para el usuario');
        }
        return $row['idRol'];
      }
    public function addMenus($data) {
		$chartic=array();
		if (!is_array($data)) {
            throw new Exception('El parámetro $data debe ser un array');
        }
			$chartic=array(
					  '`key`' => $data['key'],
					  'label' => $data['label'],
					  'isTitle' => $data['isTitle'],
					  'icon' => $data['icon']
					);
					try {
					  $idMenu =$this->apis->guardarDatos($chartic);
					 return $idMenu;
					} catch (Exception $e) {
						$records['data'] = array('status' => '401', 'error' => $e->getMessage());
					  header('Content-Type: application/json');
					  http_response_code(401);
					  echo json_encode($records);
					  exit;
					}
    }

    public function updateMenu($data) {
        if (!is_array($data)) {
            throw new Exception('El parámetro $data debe ser un array');
        }
		$chartic=array(
					  '`key`' => $data['key'],
					  'label' => $data['label'],
					  'isTitle' => $data['isTitle'],
					  'icon' => $data['icon']
					);	
        $this->apis->actualizarDatos($chartic,'idMenu',$data['idMenu']);
    }
	
	public function addSubMenu($data) {
		$chartic=array();
		if (!is_array($data)) {
            throw new Exception('El parámetro $data debe ser un array');
        }
			$chartic=array(
					  'idMenu' => $data['idMenu'],
					  'idSubmenu' => $data['idSubmenu'],
					  '`key`' => $data['key'],
					  'parentKey' => $data['parentKey'],
					  'label' => $data['label'],
					  'url' => $data['url'],
					  'icon' => $data['icon'],
					);
					try {
					  $idSubMenu =$this->SubMenu->guardarDatos($chartic);
					 return $idSubMenu;
					} catch (Exception $e) {
						$records['data'] = array('status' => 401, 'error' => $e->getMessage());
					  header('Content-Type: application/json');
					  http_response_code(401);
					  echo json_encode($records);
					  exit;
					}
    }
	public function updateSubMenu($data) {
        if (!is_array($data)) {
            throw new Exception('El parámetro $data debe ser un array');
        }
		$chartic=array(
					  'label' => $data['label'],
					  'url' => $data['isTitle'],
					  'icon' => $data['icon']
					);	
        $this->SubMenu->actualizarDatos($chartic,'idSubmenu',$data['idSubmenu']);
    }
   public function deleteSubMenu($data) {
        sql_delete("apis_submenus", "idSubmenu=" . intval($data['idSubmenu']));
    }
	
    public function deleteMenus($data) {
        sql_delete("apis_menu", "idMenu=" . intval($data['idMenu']));
    }

    public function getMenu() {
        try {
            $menusMain = $this->getMenusMain();
            return array('Menus' => $menusMain);
        } catch (Exception $e) {
            return array('error' => $e->getMessage());
        }
    }	
 	
	public function getMenusUser($idUser) {
		  $from = 'apis_menu AS R';
		  $select = 'R.idMenu,R.key,R.label,R.isTitle,R.icon,R.status';
		  $where = 'R.status = "Active" ORDER BY R.idMenu DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$menus = array();
			while ($row = sql_fetch($sql)) {
				$menus[] = $row;
			}
			$datosMenus = array('Menus' => array());
			foreach ($menus as $val) {
			  $datosMenus['Menus'][] = array(
				'idMenu' => $val['idMenu'],
				'key' => $val['key'],
				'label' => $val['label'],
				'isTitle' => $val['isTitle'],
				'icon' => $val['icon'],
				'status' => $val['status'],
			  );
			}
			if (!empty($datosMenus['Menus'])) {
					$records = array('status'=>200,'type'=>'success','data'=>$datosMenus, 'message'=>'Listodo de Menus');
				  return $records;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Menus');
				  return $records;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
	
	public function getMenus() {
		  $from = 'apis_menu AS R';
		  $select = 'R.idMenu,R.key,R.label,R.isTitle,R.icon,R.status';
		  $where = 'R.status = "Active" ORDER BY R.idMenu DESC';
		  $sql = sql_select($select, $from, $where);

		  try {
			$menus = array();
			while ($row = sql_fetch($sql)) {
				$menus[] = $row;
			}
			$datosMenus = array('Menus' => array());
			foreach ($menus as $val) {
			  $datosMenus['Menus'][] = array(
				'idMenu' => $val['idMenu'],
				'key' => $val['key'],
				'label' => $val['label'],
				'isTitle' => $val['isTitle'],
				'icon' => $val['icon'],
				'status' => $val['status'],
			  );
			}
			if (!empty($datosMenus['Menus'])) {
					$records = array('status'=>200,'type'=>'success','data'=>$datosMenus, 'message'=>'Listodo de Menus');
				  return $records;
				} else{
					$records = array('status'=>404,'type'=>'error','data'=>array(),'message'=>'No existen registros de Menus');
				  return $records;
				}
		  } catch (Exception $e) {
			return json_encode(array('error' => $e->getMessage()));
		  }
		}
	
	private function getMenusMain() {
    $sql = sql_select("DISTINCT M.idMenu,M.key,M.label,M.isTitle,M.icon",
      'apis_autorizaciones as A,apis_roles as R,apis_menu AS M',
      "A.idRol='" . intval($this->idRol) . "' 
      AND R.idRol=A.idRol 
      AND NULLIF(A.c,'')='S' AND M.status='Active' ORDER BY M.idMenu");
    $menusMain = array();
    while ($r = sql_fetch($sql)) {
      $children = $this->getChildren($r['idMenu']);
      $menusMain[] = array(
        'key' => $r['key'],
        'label' => $r['label'],
        'isTitle' => false,
        'icon' => $r['icon'],
        'badge' => array('variant' => 'error', 'text' => count($children)),
        'children' => $children
      );
    }
    return $menusMain;
  }	
	
	private function getSubMenu($idMenu) {
    $sql = sql_select('S.key,S.label,S.icon,S.url,M.key AS parentKey,S.idSubmenu',
      'apis_menu AS M,apis_submenus AS S',
      "M.idMenu= S.idMenu 
      AND M.idMenu='" . intval($idMenu) . "'  
      AND S.status='Active' ORDER BY S.idSubmenu ASC");
    $children = array();
    while ($ch = sql_fetch($sql)) {
      $children[] = array(
        'idSubmenu' => $ch['idSubmenu'],
        'key' => $ch['key'],
        'label' => $ch['label'],
        'url' => $ch['url'],
        'parentKey' => $ch['parentKey'],
        'icon' => $ch['icon']
      );
    }
    return $children;
  }	
    
	private function getChildren($idMenu) {
    $sql = sql_select('DISTINCT S.key,S.label,S.icon,S.url,M.key AS parentKey',
      'apis_menu AS M,apis_submenus AS S, apis_autorizaciones AS A,apis_roles as R',
      "R.idRol='" . intval($this->idRol) . "' 
      AND A.idRol= R.idRol 
      AND M.idMenu= A.idMenu 
      AND A.idMenu='" . intval($idMenu) . "'  
      AND A.c='S' 
      AND A.idSubmenu=S.idSubmenu 
      AND S.status='Active' ORDER BY S.idSubmenu ASC");
    $children = array();
    while ($ch = sql_fetch($sql)) {
      $children[] = array(
        'key' => $ch['key'],
        'label' => $ch['label'],
        'url' => $ch['url'],
        'parentKey' => $ch['parentKey'],
        'icon' => $ch['icon']
      );
    }
    return $children;
  }
	
	
}