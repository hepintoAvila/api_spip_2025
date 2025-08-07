
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
/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/ 
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
		include_spip('base/connect_sql');
		include_spip('inc/filtres_ecrire');
		include_spip('inc/filtres');
		include_spip('inc/utils');
		include_spip('inc/json');
 	   
	 
	   $login = $GLOBALS['visiteur_session']['login'];
		$session_password = $GLOBALS['visiteur_session']['pass'];
		include_spip('inc/auth');
		$aut = auth_informer_login($login);
		$opcion = isset($_GET['opcion']) ? base64_decode($_GET['opcion']) : base64_decode($_POST['opcion']);
 
		switch ($opcion) {
		case 'consultar':
			$IdMenu = isset($_GET['IdMenu']) ? base64_decode($_GET['IdMenu']) : base64_decode($_POST['IdMenu']);
 
			$menus=array();
				$res = sql_select("*", "sena_roles", "tipo=" . sql_quote($IdMenu));
				while ($r = sql_fetch($res)) {
					$idTipo=$r['idRol'];
					$entidad=$r['entidad'];
				}	
				$res = sql_select("*", "apis_roles", "entidad ='".$aut['entidad']."' AND tipo=" . sql_quote($aut['tipo']));
				while ($r = sql_fetch($res)) {
					$idTipo=$r['idRol'];
					$entidad=$r['entidad'];
				}	
				$app=new Apis('apis_menu AS M,apis_submenus AS S, apis_autorizaciones AS A,apis_roles as R');
				$select='A.id as idAutorizacion,
				M.key AS menu,S.url AS submenu,
				R.tipo AS rol,
				A.c as c,
				A.a as a,
				A.u as u,
				A.d as d';
				$query='
				R.idRol="'.$idTipo.'"
				AND R.idRol= A.idRol
				AND M.idMenu= A.idMenu 
				AND S.idSubmenu = A.idSubmenu  
				AND A.idSubmenu=S.idSubmenu 
				AND  S.status="Active" 
				AND M.entidad="'.$entidad.'"
				AND S.entidad="'.$entidad.'"
				AND A.entidad="'.$entidad.'"
				 AND R.entidad="'.$entidad.'"
				ORDER BY S.idSubmenu ASC';
				
				$roles=$app->consultadatos($query,$select,'apis_menu AS M,apis_submenus AS S, apis_autorizaciones AS A,apis_roles as R');
				foreach($roles as $a => $row){
					$pos = strrpos($row['submenu'], '/');
					$submenu = substr($row['submenu'], $pos + 1);
					$menus['Permisos'][]= array('query'=>$row['c'],'add'=>$row['a'],'update'=>$row['u'],'delete'=>$row['d'],'menu'=>$row['menu'],'submenu'=>$submenu);	
				}				
				 
				if($aut['id_auteur']==1){
					$ouput = var2js($menus);
					echo $ouput; 
				}else{
					$records['status'] = array('status'=>'404');
					$var = var2js($records);	
					echo $var;	 
				}
				break;
 			case 'configurar':
			echo 'No registrado';
			break;
			
		}	
			
										
?>







