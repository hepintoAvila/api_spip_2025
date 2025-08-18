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
		include_spip('inc/autoriser');
		include_spip('exec/model/admin/claseapi');
		include_spip('exec/model/admin/gestionmenu/MenuService');
		include_spip('exec/model/admin/gestionmenu/PermisosService');
		include_spip('inc/auth');
 
		
			try {
				$variables = json_decode(urldecode($_GET['variables']), true);
				$opcion = base64_decode($variables['opcion']);
				$array = isset($variables['data']) ? $variables['data'] : '';
				$data = json_decode($array, true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					$data = array();
				}
				$aut = auth_identifier_login($variables['var_login'],$variables['password']);
 			 
			} catch (Exception $e) {
				$records['data'] = array('status'=>402,'error'=>$e->getMessage());  
				echo json_encode($records);
				exit;
			}
			 
			switch ($opcion) {
			case 'login':
				//validamos usuarios y contrasea var_login

				$appk=new Apis('');
			    $secretKey=$appk->generateSecretKey();
				$encryptedData=$appk->encryptData($aut['pass'], $secretKey);
				include_spip('inc/cookie');
				spip_setcookie('ApiSecretKey', $secretKey, [
					'expires' => time() + 3600 * 24 * 14
				]);
				$Auth['Auth']= array(
				'Nom' => $aut['nom'].'',
				'IdUsuario' =>$appk->encryptData($aut['id_auteur'], $secretKey),
				'Usuario' => $aut['login'],
				'Email' => ($aut['status'] ?? '') === 'Activo' ? ($aut['email'] ?? '') : null,
				'status' => $aut['status'],
				'Rol' => $aut['tipo'],
				'AppKey' =>$encryptedData,
				'AppToken' =>$secretKey,
				'alea_actuel' =>$aut['alea_actuel'],
				);
				 
				//MOSTRAR MENU DEL USUARIO
					$menuService = new MenuService();
					$permisosService = new PermisosService();
					$Menus = $menuService->getMenu($aut);
					$Permisos = $permisosService->getPermisos($aut);

							if (!is_null($Auth)) {
								//AUDITORIA
								$appAudi=new Apis('apis_auditoria');
								$aut = $appAudi->guardarAudi('AdminUsuarios','Usuarios','Inicio Sesion',$aut);		
								//FIN AUDITORIA	
								$records = array('status'=>200,'data'=>array_merge($Auth,$Menus,$Permisos));
							}else{
								$records['data'] = array('status'=>402);                           
							}
							$var = var2js($records);
							echo $var;								 					
			break;
		}
	 
											
?>
