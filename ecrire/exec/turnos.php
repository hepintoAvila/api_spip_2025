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
		include_spip('inc/actions');
		
function exec_turnos_dist(){		
				try {
				$params = json_decode(urldecode($_GET['params']), true);				
				$opcion = base64_decode($params['opcion']);
				$array =$params['data'];				
				$data = json_decode($array, true);
					if (json_last_error() !== JSON_ERROR_NONE) {
					die(json_encode([
						'status' => 'error',
						'code' => 400,
						'message' => 'Formato JSON inválido'
						]));
						}
				//INSTANCIAS INVOLUNCRADAS
				//$appTurno=new Apis('upc_libros_visita');
				//$app_turnoService = new TurnosService();	
				 
				// array
				$chartic=array();
						 
			} catch (Exception $e) {
				$records['data'] = array('status'=>'401','error'=>$e->getMessage());  
				echo json_encode($records);
				exit;
			}
		
		 $credentials = charger_fonction('credentials', 'authorization');
		 $res = $credentials();
		if (!is_array($res)){
			 $records['data'] = array('status' =>401,'menssage'=>'usuario o password incorrectos');
			 echo json_encode($records);
			 return;
		}
		print_r($res);
                 		
		
}													
?>
