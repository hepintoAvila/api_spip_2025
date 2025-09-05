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
		
		
		
function admin_visitas_adminvisitas_dist($opcion=string,$data=array()){
				 
				include_spip('ecrire/admin_visitas/visitasService');
				//INSTANCIAS INVOLUNCRADAS

				$app_visitaService = new VisitaService();	
				// array
				$chartic=array();

		switch ($opcion) {
			case 'consulta_documento':
				$resultado =$app_visitaService->consultaDocumento($data['identificacion']);					
				$var = var2js($resultado); 	
				echo $var;
			
			break;
			case 'consulta_visitas':
				$resultado =$app_visitaService->getVisitas();					
				$var = var2js($resultado);
									
				echo $var;
				break;
			case 'add_visitas':
			
				$app_visitaService->addVisitas($data);				
				$resultado =$app_visitaService->getVisitas();					
				$var = var2js(array('status'=>200,'type'=>'success','data'=>$resultado,'message'=>'Listado de Visitas')); 	
				echo $var;
				break;			
			case 'update_visitas':
				
				$chartic['tipo_visita']=$app_visitaService->getIdsVisita('tipo_visita',$data['tipo_visita']);
				$chartic['ubicacion']=$app_visitaService->getIdsVisita('ubicacion',$data['ubicacion']);
				$chartic['programa']=$app_visitaService->getIdsVisita('programa',$data['programa']);			
				$app_visitaService->updateVisitas($chartic,'id_visita',$data['id_visita']);
				$resultado =$app_visitaService->getVisitas();					
				$var = var2js(array('status'=>200,'type'=>'success','data'=>$resultado,'message'=>'Listado de Visitas')); 	
				echo $var;
				break;			
			case 'delete_visitas':
					$app_visitaService->deleteVisitas($data['id_visita']);
					$resultado =$app_visitaService->getVisitas();					
					$var = var2js(array('status'=>200,'type'=>'success','data'=>$resultado,'message'=>'Listado de Visitas')); 	
					echo $var;				
			break;
		}

	
}