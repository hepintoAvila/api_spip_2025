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
/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/ 

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
		include_spip('exec/model/classAuthenticator');
		
				$session_login = _request('var_login');
				$session_password = _request('password');
				try {
					$login = $GLOBALS['visiteur_session']['login'];
				  $authenticator = new Authenticator($session_login, $session_password);
				  $var_auth = $authenticator->authenticate();
					$data = json_decode($_POST['data'], true);
					if (json_last_error() !== JSON_ERROR_NONE) {
					die(json_encode([
						'status' => 'error',
						'code' => 400,
						'message' => 'Formato JSON inválido'
						]));
						}
				$opcion = isset($_GET['opcion']) ? base64_decode($_GET['opcion']) : base64_decode($_POST['opcion']);
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
