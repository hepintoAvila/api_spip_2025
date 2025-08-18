<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	include_spip('ecrire/classes/classgeneral');
	
class MenuService {
    private $apis;
    private $idRol;
    public $data;

    public function __construct($data) {
        $this->data = $data;
        $this->apis = new General('apis_menu');
        $this->idRol = $this->getIdRol($this->data['tipo']);
    }

    public function addMenus($chartic) {
        if (!is_array($chartic)) {
            throw new Exception('El parámetro $chartic debe ser un array');
        }
        $this->apis->guardarDatos($chartic);
    }

    public function updateMenus($chartic, $arg1, $arg2) {
        if (!is_array($chartic)) {
            throw new Exception('El parámetro $chartic debe ser un array');
        }
        $this->apis->actualizarDatos($chartic, $arg1, $arg2);
    }

    public function deleteMenus($arg1) {
        sql_delete("apis_menu", "idMenu=" . intval($arg1));
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
	
	private function getIdRol($tipo) {
    $sql = sql_select('idRol', 'apis_roles', 'tipo=' . sql_quote($tipo));
    $row = sql_fetch($sql);
    if (!$row) {
      throw new Exception('No se encontró el rol');
    }
    return $row['idRol']; 
  }
}