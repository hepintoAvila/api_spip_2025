<?php
/***************************************************************************\
 *  SPIP, Système de publication pour l'internet                           *
 *                                                                         *
 *  Copyright © avec tendresse depuis 2001                                 *
 *  Arnaud Martin, Antoine Pitrou, Philippe Rivière, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribué sous licence GNU/GPL.     *
 *  Pour plus de détails voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion de la recherche ajax du mini navigateur de rubriques
 *
 * Cette possibilité de recherche apparaît s'il y a beaucoup de rubriques dans le site.
 *
 * @package SPIP\Core\Rechercher
 **/



if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
		
		include_spip('base/connect_sql');
		include_spip('inc/filtres_ecrire');
		include_spip('inc/filtres');
		include_spip('inc/utils');
		include_spip('inc/json');
		include_spip('inc/actions');
		
		
		
function admin_login_adminlogin_dist($get){
		$opcion = base64_decode($get['opcion']);
		
		$session_login=$_SERVER['PHP_AUTH_USER'];
		$session_password=$_SERVER['PHP_AUTH_PW'];
		
		include_spip('inc/auth');
		$aut = auth_identifier_login($session_login, $session_password);
		
	 
 			
				include_spip('ecrire/admin_login/loginService');
				include_spip('ecrire/admin_menu/menuService');
				include_spip('ecrire/admin_permisos/permisosService');
				//INSTANCIAS INVOLUNCRADAS

				$app_loginService = new LoginService($aut);	
				$app_menuService = new MenuService();	
				$app_permisosService = new PermisoService($aut);	
				// array
				$chartic=array();
		 
		switch ($opcion) {
			case 'login_auth':

			print_r($opcion);
				/*
			$auth0 = new Auth0([
				'domain' => $config['domain'],
				'client_id' => $config['client_id'],
				'client_secret' => $config['client_secret'],
				'redirect_uri' => $config['redirect_uri'],
			]);
			;
		
			$config = new SdkConfiguration(
			  strategy: SdkConfiguration::STRATEGY_API,
			  domain: 'https://dev-873fuqcjuqhamk5d.us.auth0.com',
			  audience: ['https://lacasadelbarbero.com.co/api2025/']
			);

			$auth0 = new Auth0($config);

			$auth0->login();
			//$token = $auth0->getAccessToken();
			//$token = $auth0->decode($token);
			
			//$userInfo = $auth0->getUser();
			*/
			break;
			case 'login':
				 
				$encryptedData =$app_loginService->addKey();										
				$Menus = $app_menuService->getMenusUsuario($aut);										
				$Permisos = $app_permisosService->getPermisos();										
				$Auth['Auth']= array(
				'Nom' => $aut['nom'].'',
				'Email' => $aut['email'] ?? null,
				'rol' => $aut['tipo'],
				'AppKey' =>$encryptedData,
				);
				if (!is_null($aut)) {
						$records = array('status'=>200,'type' =>'success','message'=>'ok','message'=>'ok','data'=>array_merge($Auth,$Menus,$Permisos));
					}else{
						$records['data'] = array('status'=>402,'type' =>'error','message'=>'Error:: no existen datos');                           
					}
					$var = var2js($records);
					echo $var;

		break;
				 
		}

	
}