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
		 
		$login = $GLOBALS['visiteur_session']['login'];
		include_spip('inc/auth');
		$var_auth = auth_informer_login($login);
		 
				try {
				$variables = json_decode(urldecode($_GET['variables']), true);
				$opcion = base64_decode($variables['opcion']);
				$array =$variables['data'];				
				$data = json_decode($array, true);
				 	
				//INSTANCIAS INVOLUNCRADAS
				$appRoles=new Apis('apis_roles');
				$appAudi=new Apis('apis_auditoria');
				$appRoles=new Apis('apis_roles');
				$appAutorizaciones=new Apis('apis_autorizaciones');
				$app_rolService = new RolService();	
				// array
				$chartic=array();
						 
			} catch (Exception $e) {
				$records['data'] = array('status'=>401,'error'=>$e->getMessage());  
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
					$var = var2js(array('status'=>200,'type'=>'success','data'=>array('Roles'=>$resultado),'message'=>'Listado de Roles')); 	
					echo $var;				
			break;
			case 'add_roles':
					$chartic=array('tipo'=>$data['rol']);
					$appRoles->guardarDatos($chartic);
					$resultado =$app_rolService->consultaRoles();					
					$var = var2js(array('status'=>200,'type'=>'success','data'=>array('Roles'=>$resultado),'message'=>'Listado de Roles')); 	
					echo $var;					
			break;
			case 'editar_roles':
					
					$chartic=array('tipo'=>$data['rol']);
					$appRoles->actualizarDatos($chartic,'idRol',$data['idRol']);				
					$resultado =$app_rolService->consultaRoles();
					$var = var2js(array('status'=>200,'type'=>'success','data'=>array('Roles'=>$resultado),'message'=>'Listado de Roles')); 
					echo $var;				
			break;
			case 'consulta_roles':
					$resultado =$app_rolService->consultaRoles();
					$var = var2js(array('status'=>200,'type'=>'success','data'=>array('Roles'=>$resultado),'message'=>'Listado de Roles')); 	
					echo $var;											
					 	
			 break;			
			
		}

													
?>
