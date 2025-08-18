<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2017                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
		include_spip('base/connect_sql');
		include_spip('inc/filtres_ecrire');
		include_spip('inc/filtres');
		include_spip('inc/utils');
		include_spip('inc/json');
		include_spip('exec/model/apis/claseapi');
		include_spip('exec/model/admin/adminroles/rolService');
		include_spip('exec/model/admin/gestionmenu/MenuService');

				try {
				$variables = json_decode(urldecode($_GET['variables']), true);
				$opcion = base64_decode($variables['opcion']);
				$array =$variables['data'];				
				$data = json_decode($array, true);
				
				//INSTANCIAS INVOLUNCRADAS
				$appAudi=new Apis('apis_auditoria');
				$app_Menu=new Apis('apis_menu');
				$app_SubMenus=new Apis('apis_submenus');
				$menuService = new MenuService();	
				// array
				$chartic=array();
						 
			} catch (Exception $e) {
				$records['data'] = array('status'=>'401','error'=>$e->getMessage());  
				echo json_encode($records);
				exit;
			}
	 		
	switch ($opcion) {
			case 'consultar_menu':
					$menus=$menuService->getMenus();	
					$data = array("data"=>array('Menu'=>$menus));
					$var = var2js($data);
					echo $var;						
			break;
			case 'add_menu':

				$chartic=array(
					  '`key`' => $data['key'],
					  'label' => $data['label'],
					  'isTitle' => $data['isTitle'],
					  'icon' => $data['icon']
					);
					
					try {
					  $idMenu = $app_Menu->guardarDatos($chartic);
						if(intval($idMenu>0)){
							$menus=$menuService->getMenus();	
							$data = array("data"=>array('Menu'=>$menus));
							$var = var2js($data);
							echo $var;							
						}else{
							$records['data'] = array('status'=>'401','error'=>'No fue posible registrar');  
							$var = var2js($records);
							echo $var;
						}
					} catch (Exception $e) {
						$records['data'] = array('status' => '401', 'error' => $e->getMessage());
					  header('Content-Type: application/json');
					  http_response_code(401);
					  echo json_encode($records);
					  exit;
					}
			break;
			case 'update_menu':
				$chartic=array(
					  '`key`' => $data['key'],
					  'label' => $data['label'],
					  'icon' => $data['icon']
					);
			
				try {
				  $app_Menu->actualizarDatos($chartic,'idMenu',$data['idMenu']);
					$menus=$menuService->getMenus();	
					$data = array("data"=>array('Menu'=>$menus));
					$var = var2js($data);
					echo $var;	
				} catch (Exception $e) {
					  $records['data'] = array('status' => '401', 'error' => $e->getMessage());
					  header('Content-Type: application/json');
					  http_response_code(401);
					  echo json_encode($records);
					  exit;
				} 	 			 
			
			break;
			case 'delete_menu':
					sql_delete("apis_menu","idMenu=" . intval($data['idMenu']));
					$menus=$menuService->getMenus();	
					$data = array("data"=>array('Menu'=>$menus));
					$var = var2js($data);
					echo $var;					
							
			break;
			case 'update_SubMenu':
				$chartic=array(
					  'label' => $data['label'],
					  'url' => $data['url'],
					  'icon' => $data['icon']
					);
			
				try {
				  $app_SubMenus->actualizarDatos($chartic,'idSubmenu',$data['idSubmenu']);
					$menus=$menuService->getMenus();	
					$data = array("data"=>array('Menu'=>$menus));
					$var = var2js($data);
					echo $var;	
				} catch (Exception $e) {
					  $records['data'] = array('status' => '401', 'error' => $e->getMessage());
					  header('Content-Type: application/json');
					  http_response_code(401);
					  echo json_encode($records);
					  exit;
				} ;
			break;
			case 'add_SubMenu':
				$chartic=array(
					  'idMenu' => $data['idMenu'],
					  '`key`' => $data['key'],
					  '`parentKey`' => $data['parentKey'],
					  'label' => $data['label'],
					  'url' => $data['url'],
					  'icon' => $data['icon']
					);
					try {
					  $idMenu = $app_SubMenus->guardarDatos($chartic);
						if(intval($idMenu>0)){
							$menus=$menuService->getMenus();	
							$data = array("data"=>array('Menu'=>$menus));
							$var = var2js($data);
							echo $var;							
						}else{
							$records['data'] = array('status'=>'401','error'=>'No fue posible registrar');  
							$var = var2js($records);
							echo $var;
						}
					} catch (Exception $e) {
						$records['data'] = array('status' => '401', 'error' => $e->getMessage());
					  header('Content-Type: application/json');
					  http_response_code(401);
					  echo json_encode($records);
					  exit;
					}
			break;
			case 'delete_subMenu':
			
					sql_delete("apis_submenus","idSubmenu=" . intval($data['idSubmenu']));
					$menus=$menuService->getMenus();	
					$data = array("data"=>array('Menu'=>$menus));
					$var = var2js($data);
					echo $var;				
			
			break;
			
			
		}										
?>
