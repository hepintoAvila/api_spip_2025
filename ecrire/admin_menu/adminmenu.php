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
		
		
		
function admin_menu_adminmenu_dist($opcion=string,$data=array(),$resdataCredencials=array()){
				 
				include_spip('ecrire/admin_menu/menuService');
				//INSTANCIAS INVOLUNCRADAS

				$app_menuService = new MenuService($resdataCredencials);	
				// array
				$chartic=array();

		switch ($opcion) {
			case 'consultar_menu':
				$resultado =$app_menuService->getMenus();					
				$var = var2js($resultado);
				echo $var;
				break;
			case 'add_menu':
				/*
				$chartic['identificacion']=$data['identificacion'];
				$chartic['tipo_visita']=$app_menuService->getIdsVisita('tipo_visita',$data['tipo_visita']);
				$chartic['jornada']=$app_menuService->getIdJornada($data['fecha_creacion']);
				$chartic['ubicacion']=$app_menuService->getIdsVisita('ubicacion',$data['ubicacion']);
				$chartic['programa']=$app_menuService->getIdsVisita('programa',$data['programa']);
				$app_menuService->addVisitas($chartic);				
				*/
				$resultado =$app_menuService->getMenus();					
				$var = var2js(array('status'=>200,'type'=>'success','data'=>$resultado,'message'=>'Listado de Visitas')); 	
				echo $var;
				break;			
			case 'update_menu':
				/*
				$chartic['tipo_visita']=$app_menuService->getIdsVisita('tipo_visita',$data['tipo_visita']);
				$chartic['ubicacion']=$app_menuService->getIdsVisita('ubicacion',$data['ubicacion']);
				$chartic['programa']=$app_menuService->getIdsVisita('programa',$data['programa']);			
				$app_menuService->updateVisitas($chartic,'id_visita',$data['id_visita']);
				*/
				$resultado =$app_menuService->getMenus();					
				$var = var2js(array('status'=>200,'type'=>'success','data'=>$resultado,'message'=>'Listado de Visitas')); 	
				echo $var;
				break;			
			case 'delete_menu':
					//$app_menuService->deleteMenu($data['id_visita']);
					$resultado =$app_menuService->getMenus();					
					$var = var2js(array('status'=>200,'type'=>'success','data'=>$resultado,'message'=>'Listado de Visitas')); 	
					echo $var;				
			break;
		}

	
}