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
		 
		
function exec_admin_visitas_dist(){
			$credentials = charger_fonction('credentials', 'authorization');
			$resdataCredencials = $credentials();
	
		try {
				$opcion = base64_decode($_GET['opcion']);
				$array = $_GET['data'] ?? null;
				if ($array === null) {
					$data = array();
				} else {
					$data = json_decode($array, true);
					if (json_last_error() !== JSON_ERROR_NONE) {
						die(json_encode([
							'status' => 'error',
							'code' => 400,
							'message' => 'Formato JSON inválido'
						]));
					}
				}
						 
			} catch (Exception $e) {
				$records['data'] = array('status'=>401,'error'=>$e->getMessage());  
				echo json_encode($records);
				exit;
			}
				include_spip('ecrire/classes/security');
				$apisKey = new ApiKeyManager();
				
				if (!$apisKey->valideApiKey($resdataCredencials)){
				 $records['data'] = array('status' =>401,'type'=>'error','menssage'=>'Credenciales de su APIKEY es incorrecto');
				 echo json_encode($records);
				 return;
				}		
		 
		  
		if (!is_array($resdataCredencials)){
			 $records['data'] = array('status' =>401,'menssage'=>'usuario o password incorrectos');
			 echo json_encode($records);
			 return;
		}
		
		$adminvisitas = charger_fonction('adminvisitas', 'admin_visitas');
		$json = $adminvisitas($opcion,$data);
		if (!isset($json['status']) || !isset($json['type']) || !isset($json['data']) || !isset($json['message'])) {
			die(json_encode([
				'status' => 'error',
				'code' => 400,
				'message' => 'Estructura JSON inválida'
			]));
		}

}													
?>
