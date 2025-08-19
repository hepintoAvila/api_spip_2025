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
include_spip('inc/autoriser');
include_spip('exec/model/admin/claseapi');
include_spip('inc/auth');

function exec_apis_dist(){

				$headers = getallheaders();
				$authorization = $headers['Authorization'];
				$auth = explode(' ', $authorization);
				if ($auth[0] == 'Basic') {
					$credentials = base64_decode($auth[1]);
					list($username, $password) = explode(':', $credentials);

				}
			
				$variables = json_decode(urldecode($_GET['variables']), true);
				$accion = base64_decode($variables['accion']); 
				 
				
				try {
    				include_spip('exec/model/classAuthenticator');
                    $authenticator = new Authenticator($username, $password);
    				$var_auth = $authenticator->authenticate();
					if (is_array($var_auth)) {
						spip_setcookie('spip_session', $_COOKIE['spip_session'], [
							'expires' => time() + 3600 * 24 * 14
						]);
					}
					$GLOBALS['connect_id_auteur'] = $var_auth['id_auteur'];
                	$GLOBALS['connect_login'] = $var_auth['login'];
                	$GLOBALS['connect_statut'] = $var_auth['statut'];
                	$GLOBALS['visiteur_session'] = array_merge((array)$GLOBALS['visiteur_session'], $var_auth);
				    
				
				// creer la session au besoin
                	if (!isset($_COOKIE['spip_session'])) {
                		$session = charger_fonction('session', 'inc');
                		$spip_session = $session($var_auth);
                	}
			    	$GLOBALS['visiteur_session'] = pipeline(
                		'preparer_visiteur_session',
                		array('args' => array('row' => $var_auth),
                		'data' => $GLOBALS['visiteur_session'])
                	);
						 
			} catch (Exception $e) {
				$records['data'] = array('status'=>401,'error'=>$e->getMessage());  
				echo json_encode($records);
				exit;
			}
			 
		switch($accion) {
			case "roles":
				include_spip('exec/model/admin/adminroles/adminroles');
			break;
			case "menu":
				include_spip('exec/model/admin/gestionmenu/gestionmenu');
			break;			
			case "auteur":
				include_spip('exec/model/admin/login/login');		    
			break;
			case "usuarios":
				include_spip('exec/model/admin/usuarios/usuarios');		    
			break;
			case "importar":
				include_spip('exec/model/Administrador/importar/importar');	
			break;
			case "imagenes":
				include_spip('exec/model/imagenes/cargar_imagenes');	
			break;
			case "librovisitas":
				include_spip('exec/model/LibroVisitas/libro_visitas');	
			break;
			case "turnos":
				include_spip('inc/headers');
				//include_spip('inc/acces');
				 
				redirige_par_entete("turnos", 'variables=' . $variables);
				//include_spip('exec/model/Turnos/turnos');	
			break;	
		}
	 
		
}
?>