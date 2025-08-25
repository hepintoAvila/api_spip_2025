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
 		include_spip('exec/model/classAuthenticator');

		
				$session_login = _request('var_login');
				$session_password = _request('password');
				try {
				  
				  $login = $GLOBALS['visiteur_session']['login'];
				  $authenticator = new Authenticator($session_login, $session_password);
				  $var_auth = $authenticator->authenticate();
				  $accion = filter_input(INPUT_GET, 'accion', FILTER_SANITIZE_STRING) ?? filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_STRING);
					
					if ($accion !== null) {
						$accion =$accion;
					}else{
						$accion = $_POST["params"]["opcion"];
					}
				$accion = str_replace('%0A', '', $accion);
				$accion = base64_decode($accion);
						 
			} catch (Exception $e) {
				$records['data'] = array('status'=>'401','error'=>$e->getMessage());  
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
			case "inscripciones":
			case "registrar_prueba":
			case "consultar_documento":
			case "regitrar_oportunidades":
			case "marcar_oportunidades":
				include_spip('exec/model/inscripciones/inscripciones');	
			break;
			case "prueba_aspirantes":
				include_spip('exec/model/inscripciones/prueba_aspirantes/prueba_aspirantes');	
			break;
			case "usuarios":
				include_spip('exec/model/admin/usuarios/usuarios');		    
			break;
			case "Evaluaciones":
			case "guardar_pregunta":
			case "consulta_preguntas":
			case "eliminar_pregunta":
			case "editar_pregunta":
			case "guardar_respuesta":
			case "guardar_seccion":
			case "consultar_seccion":
			case "editar_respuesta":
				include_spip('exec/model/Evaluaciones/evaluaciones');		    
			break;
			case "cargacademica":
			case "importar_estudiantes":
				  include_spip('exec/model/cargacademica/cargacademica');	
			break;
			case "importar":
				include_spip('exec/model/Administrador/importar/importar');	
			break;
			case "imagenes":
				include_spip('exec/model/imagenes/cargar_imagenes');	
			break;
			case "LibroVisitas":
				include_spip('exec/model/LibroVisitas/libro_visitas');	
			break;	
		}
	 
		
}
?>