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
function admin_aulas_adminaulas_dist($opcion,$data=array(),$resdataCredencials=array()){

				include_spip('ecrire/admin_aulas/aulaService');
				include_spip('ecrire/admin_turnos/turnosService');
				//INSTANCIAS INVOLUNCRADAS
				$app_aulaService = new AulaService();
				$app_turnosService = new TurnosService($resdataCredencials);
		
		switch ($opcion) {
			case 'consulta_aulas':
				$resultado =$app_aulaService->getAula();					
				break;
			case 'add_aulas':
					$app_aulaService->addAula($data);
					$app_aulaService->getAula();					
				break;			
			case 'update_aulas':
					$app_aulaService->updateAula($data);
					$app_aulaService->getAula();
				break;			
			case 'delete_aulas':
					$app_aulaService->deleteAula($data);
					$app_aulaService->getAula();		
			break;
			case 'consulta_turno_aulas':
				$resultado =$app_aulaService->getAula();
				break;
			case 'add_turno_aulas':
				$app_turnosService->addTurnosAulas($data);
				$resultado =$app_aulaService->getAula();
				break;
			case 'update_turno_aulas':
				$app_turnosService->updateTurnosAulas($data);
				$resultado =$app_aulaService->getAula();
				break;
			case 'delete_turno_aulas':
				$app_turnosService->deleteTurnosAulas($data);
				$resultado =$app_aulaService->getAula();
				break;			
		}
 
	
}