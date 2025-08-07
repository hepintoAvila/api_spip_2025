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
		include_spip('inc/autoriser');
		include_spip('exec/model/admin/claseapi');
		include_spip('exec/model/admin/gestionmenu/MenuService');
		include_spip('exec/model/admin/gestionmenu/PermisosService');
		include_spip('inc/auth');
 
		
		$campos = $GLOBALS['tables_principales']['apis_roles']['field'];
		$select = implode(',',array_keys($campos));
		
			$opcion = filter_input(INPUT_GET, 'opcion', FILTER_SANITIZE_STRING) ?? filter_input(INPUT_POST, 'opcion', FILTER_SANITIZE_STRING);
			$opcion = base64_decode($opcion);
			
			
			if ($opcion !== null) {
			switch ($opcion) {
			case 'login':
				//validamos usuarios y contrasea var_login
				$session_login =_request('var_login');
				$session_password = _request('password');
				$aut = auth_identifier_login($session_login, $session_password);
				if($aut['statut']=='0minirezo'){
					$statut='Administrador';
				}else{
					$statut=$aut['statut'];
				}
				 
				$appk=new Apis('');
			    $secretKey=$appk->generateSecretKey();
				$encryptedData=$appk->encryptData($session_password, $secretKey);
				include_spip('inc/cookie');
				spip_setcookie('ApiSecretKey', $secretKey, [
					'expires' => time() + 3600 * 24 * 14
				]);
				$Auth['Auth']= array(
				'Nom' => $aut['nom'].'',
				'Idsuario' => $aut['id_auteur'],
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
								$aut = $appAudi->guardarAudi('AdminUsuarios','Usuarios','Inicio Sesion');		
								//FIN AUDITORIA	
								$records = array('status'=>'202','data'=>array_merge($Auth,$Menus,$Permisos));
							}else{
								$records['data'] = array('status'=>'404');                           
							}

							$var = var2js($records);
							echo $var;								 					
			break;
		}
	} else {
		// Maneja el caso cuando 'accion' no esté presente en GET ni POST
		$records['data'] = array('status'=>'404');
		$var = var2js($records);
		echo $var; 
	}
											
?>
