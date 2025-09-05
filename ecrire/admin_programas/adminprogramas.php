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
		
		
		
function admin_programas_adminprogramas_dist($opcion,$data=array(),$resdataCredencials=array()){
	

	
				include_spip('ecrire/admin_programas/programaService');
				//INSTANCIAS INVOLUNCRADAS

				$app_progrService = new ProgramaService();
		
		switch ($opcion) {
			case 'consultar_programas':
				$resultado =$app_progrService->getPrograma();					
				break;
			case 'add_programas':
					$app_progrService->addPrograma($data);
					$app_progrService->getPrograma();					
				break;			
			case 'update_programas':
		 
					$app_progrService->updatePrograma($data);
					$app_progrService->getPrograma();
				break;			
			case 'delete_programas':
					$app_progrService->deletePrograma($data);
					$app_progrService->getPrograma();		
			break;
		}
 
	
}