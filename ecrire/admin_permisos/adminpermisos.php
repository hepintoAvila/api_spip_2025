
<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2017                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
		include_spip('base/connect_sql');
		include_spip('inc/filtres_ecrire');
		include_spip('inc/filtres');
		include_spip('inc/utils');
		include_spip('inc/json');

function admin_permisos_adminpermisos_dist($opcion=string,$data=array(),$resdataCredencials=array()){	
				
				include_spip('ecrire/admin_permisos/permisosService');
				//INSTANCIAS INVOLUNCRADAS
				$app_permisosservice = new PermisoService($resdataCredencials);	
				// array
				$chartic=array();
		
		switch ($opcion) {

		case 'consultar_roles_usuarios':
				$resultado =$app_permisosservice->getPermisos();						
				$var = var2js($resultado); 	
				echo $var;				
		break;
		}	
			
}										
?>







