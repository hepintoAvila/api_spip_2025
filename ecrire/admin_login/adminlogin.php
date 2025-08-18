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
use Spip\Chiffrer\SpipCles;

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
		
		include_spip('base/connect_sql');
		include_spip('inc/filtres_ecrire');
		include_spip('inc/filtres');
		include_spip('inc/utils');
		include_spip('inc/json');
		include_spip('inc/actions');
		
		
		
function admin_login_adminlogin_dist($opcion=string,$data=array()){
				  
				include_spip('ecrire/admin_login/loginService');
				include_spip('ecrire/admin_menu/menuService');
				include_spip('ecrire/admin_permisos/permisosService');
				//INSTANCIAS INVOLUNCRADAS

				$app_loginService = new LoginService($data);	
				$app_menuService = new MenuService($data);	
				$app_permisosService = new PermisoService($data);	
				// array
				$chartic=array();

		switch ($opcion) {
			case 'login':
				
				$encryptedData =$app_loginService->addKey();										
				$Menus = $app_menuService->getMenu();										
				$Permisos = $app_permisosService->getPermisos();										
				$Auth['Auth']= array(
				'Nom' => $data['nom'].'',
				'Email' => $data['email'] ?? null,
				'rol' => $data['tipo'],
				'AppKey' =>$encryptedData,
				);
				if (!is_null($data)) {
						$records = array('status'=>200,'type' =>'success','message'=>'ok','message'=>'ok','data'=>array_merge($Auth,$Menus,$Permisos));
					}else{
						$records['data'] = array('status'=>402,'type' =>'error','message'=>'Error:: no existen datos');                           
					}
					$var = var2js($records);
					echo $var;	
				break;
		}

	
}