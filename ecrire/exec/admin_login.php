<?php
/***************************************************************************\
 *  SPIP, Syst�me de publication pour l'internet                           *
 *                                                                         *
 *  Copyright � avec tendresse depuis 2001                                 *
 *  Arnaud Martin, Antoine Pitrou, Philippe Rivi�re, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribu� sous licence GNU/GPL.     *
 *  Pour plus de d�tails voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion de la recherche ajax du mini navigateur de rubriques
 *
 * Cette possibilit� de recherche appara�t s'il y a beaucoup de rubriques dans le site.
 *
 * @package SPIP\Core\Rechercher
 **/
use Spip\Chiffrer\SpipCles;

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
		include_spip('inc/actions');
		
function exec_admin_login_dist(){		
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
							'message' => 'Formato JSON inv�lido'
						]));
					}
				}
						 
			} catch (Exception $e) {
				$records['data'] = array('status'=>400,'error'=>$e->getMessage());  
				echo json_encode($records);
				exit;
			}
		
		
		
		 $credentials = charger_fonction('credentials', 'authorization');
		 $data = $credentials();
		if (!is_array($data)){
			 $records['data'] = array('status' =>401,'type' =>'error','menssage'=>'usuario o password incorrectos');
			 echo json_encode($records);
			 return;
		} 
		 $adminlogin = charger_fonction('adminlogin', 'admin_login');
		
		$json = $adminlogin($opcion,$data);
		
		print_r($json);
		if (!isset($json['status']) || !isset($json['type']) || !isset($json['data']) || !isset($json['message'])) {
			die(json_encode([
				'status' => 'error',
				'code' => 400,
				'message' => 'Estructura JSON inv�lida'
			]));
		}
		
}													
?>
