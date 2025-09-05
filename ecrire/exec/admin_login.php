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


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
		include_spip('inc/actions');
		
function exec_admin_login_dist($get,$post){	
		  $adminlogin = charger_fonction('adminlogin', 'admin_login');
		 $json = $adminlogin($get);	
		 if (!isset($json['status']) || !isset($json['type']) || !isset($json['data']) || !isset($json['message'])) {
			die(json_encode([
				'status' => 'error',
				'code' => 400,
				'message' => 'Estructura JSON inv�lida'
			]));
		}
		 
}													
?>
