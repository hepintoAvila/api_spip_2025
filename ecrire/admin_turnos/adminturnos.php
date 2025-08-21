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
		
		
		
function admin_turnos_adminturnos_dist($opcion,$data=array(),$resdataCredencials=array()){
				include_spip('ecrire/admin_turnos/turnosService');
				//INSTANCIAS INVOLUNCRADAS
//print_r($opcion);
				$app_turnosService = new TurnosService();	

		switch ($opcion) {
			case 'consulta_turnos':
				$app_turnosService->getTurnos();						
				break;
			case 'add_turno':
					$app_turnosService->addTurnos($data);
					$app_turnosService->getTurnos();					
					
				break;			
			case 'update_turno':
					$app_turnosService->updateTurnos($data);
					$app_turnosService->getTurnos();
				break;			
			case 'delete_turnos':
					$app_turnosService->deleteTurnos($data);
					$app_turnosService->getTurnos();		
			break;
		}

	
}