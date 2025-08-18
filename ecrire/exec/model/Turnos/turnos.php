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
		include_spip('exec/model/apis/claseapi');
		include_spip('exec/model/classAuthenticator');
		include_spip('exec/model/Turnos/turnosService');
		
function exec_turnos_dist(){		
				try {
				$variables = json_decode(urldecode($_GET['variables']), true);				
				$opcion = base64_decode($variables['opcion']);
				$array =$variables['data'];				
				$data = json_decode($array, true);
					if (json_last_error() !== JSON_ERROR_NONE) {
					die(json_encode([
						'status' => 'error',
						'code' => 400,
						'message' => 'Formato JSON inválido'
						]));
						}
				//INSTANCIAS INVOLUNCRADAS
				$appTurno=new Apis('upc_libros_visita');
				$app_turnoService = new TurnosService();	
				 
				// array
				$chartic=array();
						 
			} catch (Exception $e) {
				$records['data'] = array('status'=>'401','error'=>$e->getMessage());  
				echo json_encode($records);
				exit;
			}
		 
		switch ($opcion) {
			case 'consulta_documento':
				$resultado =$app_turnoService->consultaDocumento($data['identificacion']);					
				$var = var2js($resultado); 	
				echo $var;
			
			break;
			case 'consulta_turnos':
				$resultado =$app_turnoService->getTurnos();					
				$var = var2js($resultado); 	
				echo $var;
				break;
			case 'add_turnos':
				
				$chartic['identificacion']=$data['identificacion'];
				$chartic['tipo_visita']=$app_turnoService->getIdsVisita('tipo_visita',$data['tipo_visita']);
				$chartic['jornada']=$app_turnoService->getIdJornada($data['fecha_creacion']);
				$chartic['ubicacion']=$app_turnoService->getIdsVisita('ubicacion',$data['ubicacion']);
				$chartic['programa']=$app_turnoService->getIdsVisita('programa',$data['programa']);
				///$appTurno->guardarDatos($chartic);				
				$resultado =$app_turnoService->getVisitas();					
				$var = var2js(array('status'=>200,'type'=>'success','data'=>$resultado,'message'=>'Listado de Visitas')); 	
				echo $var;
				break;			
			case 'update_turnos':
				
				$chartic['tipo_visita']=$app_turnoService->getIdsVisita('tipo_visita',$data['tipo_visita']);
				$chartic['ubicacion']=$app_turnoService->getIdsVisita('ubicacion',$data['ubicacion']);
				$chartic['programa']=$app_turnoService->getIdsVisita('programa',$data['programa']);			
				//$appTurno->actualizarDatos($chartic,'id_visita',$data['id_visita']);
				$resultado =$app_turnoService->getVisitas();					
				$var = var2js(array('status'=>200,'type'=>'success','data'=>$resultado,'message'=>'Listado de Visitas')); 	
				echo $var;
				break;			
			case 'delete_turnos':

					//sql_delete("upc_libros_visita","id_visita=" . intval($data['id_visita']));
					$resultado =$app_turnoService->getVisitas();					
					$var = var2js(array('status'=>200,'type'=>'success','data'=>$resultado,'message'=>'Listado de Visitas')); 	
					echo $var;				
			break;
		}

}													
?>
