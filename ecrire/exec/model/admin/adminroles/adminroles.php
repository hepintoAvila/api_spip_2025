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
				$appRoles=new Apis('apis_roles');
				$appAudi=new Apis('apis_auditoria');
				$appRoles=new Apis('apis_roles');
				$appAutorizaciones=new Apis('apis_autorizaciones');
				$app_rolService = new RolService();	
				// array
				$chartic=array();
						 
			} catch (Exception $e) {
				$records['data'] = array('status'=>'401','error'=>$e->getMessage());  
				echo json_encode($records);
				exit;
			}
	 		
		
		switch ($opcion) {
			case 'consultar_roles_usuarios':
			$resultado =$app_rolService->consultarRolesAutorizaciones($var_auth);
			$var = var2js($resultado); 	
			 echo $var;							
			break;
			case 'update_roles_usuarios':
				$chartic['c']=$data['c'];
				$chartic['u']=$data['u'];
				$chartic['d']=$data['d'];
				$chartic['a']=$data['a'];
				$appAutorizaciones->actualizarDatos($chartic,'id',$data['id_autorizacion']);	
				$resultado =$app_rolService->consultarRolesAutorizaciones($var_auth);
				$var = var2js($resultado); 	
				echo $var;
			break;
			case 'add_roles_usuarios':
			$existe = $app_rolService->validarAutorizaciones($data);
			if($existe['existen']!=1){
				
				$chartic['idMenu']=$data['idMenu'];
				$chartic['idSubmenu']=$data['idSubmenu'];
				$chartic['idRol']=$data['idRol'];
				$chartic['c']=$data['c'];
				$chartic['u']=$data['u'];
				$chartic['d']=$data['d'];
				$chartic['a']=$data['a'];
				$appAutorizaciones->guardarDatos($chartic);
			}
				$resultado =$app_rolService->consultarRolesAutorizaciones($var_auth);
				$var = var2js($resultado); 			
				echo $var;
			break;
			case 'copiar_roles_usuarios':
			$resultado = $app_rolService->copyRoles($data);
			foreach ($resultado['Roles'] as $chartic) {
				$existe = $app_rolService->validarAutorizaciones($chartic);
				if($existe['existen']!=1){
				$appAutorizaciones->guardarDatos($chartic);
				}
			}
				$var = var2js($resultado); 			
				echo $var;
			break;
			case 'delete_roles':

					sql_delete("apis_roles","idRol=" . intval($data['idRol']));
					$resultado =$app_rolService->consultaRoles();
					$var = var2js($resultado); 	
					echo $var;				
			break;
			case 'add_roles':
					$chartic=array('tipo'=>$data['Rol']);
					$appRoles->guardarDatos($chartic);	
					$resultado =$app_rolService->consultaRoles();
					$var = var2js($resultado); 	
					echo $var;					
			break;
			case 'editar_roles':
					
					$chartic=array('tipo'=>$data['Rol']);
					$appRoles->actualizarDatos($chartic,'idRol',$data['idRol']);				
					$resultado =$app_rolService->consultaRoles();
					$var = var2js($resultado); 	
					echo $var;				
			break;
			case 'consulta_roles':
					$resultado =$app_rolService->consultaRoles();
					$var = var2js($resultado); 	
					echo $var;											
					 	
			 break;			
			
		}

													
?>
