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
		
		
		
function admin_pcs_adminpcs_dist($opcion,$data=array(),$resdataCredencials=array()){
			  
				include_spip('ecrire/admin_pcs/pcsService');
				//INSTANCIAS INVOLUNCRADAS

				$app_pcService = new PcsService();	
 
		switch ($opcion) {
			case 'consulta_pcs':
				$resultado =$app_pcService->getPcs();					
				
				break;
			case 'add_pcs':
					$app_pcService->addPcs($data);
					$app_pcService->getPcs();					
					
				break;			
			case 'update_pcs_aguachica':
					$app_pcService->updatePcsAguachica($data);
					$app_pcService->getPcsAguachica();	
				break;
			case 'update_pcs':
		 
					$app_pcService->updatePcs($data);
					$app_pcService->getPcs();
				break;				
			case 'delete_pcs':
					$app_pcService->deletePcs($data);
					$app_pcService->getPcs();		
			    break;
			case 'consulta_pcs_aguachica':
				$app_pcService->getPcsAguachica();					
				break;			
			
		}

	
}