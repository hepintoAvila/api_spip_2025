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
		include_spip('inc/autoriser');
		include_spip('exec/model/sena/claseapi');
		include_spip('inc/auth');		
		 
		$opcion = isset($_GET['opcion']) ? base64_decode($_GET['opcion']) : base64_decode($_POST['opcion']);
		$entidad = isset($_GET['entidad']) ? base64_decode($_GET['entidad']) : base64_decode($_POST['entidad']);

		switch ($opcion) {
			case 'listaUsuarios':
		
				$DatosAuteurs=array();
				$select='*';
				$set = array();	
				
				$app=new Apis('api_auteurs');
				$auteurs=$app->consultadatos('entidad="'.$entidad.'" AND id_auteur NOT IN (1,3)',$select);				
				foreach($auteurs as $a => $value){
					$DatosAuteurs['auteurs'][] = array(
                    'id'=>$value['id_auteur'],
					'login'=>$value['login'],
                    'email'=>$value['email'],
                    'rol'=>$value['tipo']
					);
					}
					
				//ROLES
				$app_roles=new Apis('apis_roles');
				$roles=$app_roles->consultadatos('entidad="'.$entidad.'"',$select);				
				foreach($roles as $a => $val){
					$DatosRoles['roles'][] = array(
                    'value'=>$val['tipo'],
                    'label'=>$val['tipo']
					);
					}
					$data = array("data"=>array_merge($DatosAuteurs,$DatosRoles));
					$var = var2js($data);
					echo $var;						
			break;
			case 'changePassword':
				$app=new Apis('api_auteurs');
				$erreurs = array();
				$msg = array();
				$session_password = isset($_GET['palabraclave']) ? base64_decode($_GET['palabraclave']) : base64_decode($_POST['palabraclave']);
				$new_pass = unicode2charset(utf_8_to_unicode($session_password), 'iso-8859-1');
				$id_auteur = isset($_GET['idUsuario']) ? base64_decode($_GET['idUsuario']) : base64_decode($_POST['idUsuario']);
 
				$variablesAVerificar = [
					'password' => $session_password,
					'new_pass' => $new_pass,
					'id_auteur' => $id_auteur,
					'entidad' => $entidad,
					];
				 
				$mensajeError = $app->verificarVariables($variablesAVerificar);
				if ($mensajeError !== null){
				 $arrayMensage=array('id'=>1,'message'=>'::ERROR-001:: '.$mensajeError.'','status'=>'404');
				}else{	
					//AUDITORIA
					$appAudi=new Apis('auditoria');
					$appAudi->guardar('AdminUsuarios','Usuarios','changePassword');		
					//FIN AUDITORIA	

					$cpass = array();
					include_spip('inc/acces');
					include_spip('auth/sha256.inc');
					$htpass = generer_htpass($new_pass);
					$alea_actuel = creer_uniqid();
					$alea_futur = creer_uniqid();
					$pass = spip_sha256($alea_actuel . $new_pass);
					$cpass['pass'] = $pass;
					$cpass['htpass'] = $htpass;
					$cpass['alea_actuel'] = $alea_actuel;
					$cpass['alea_futur'] = $alea_futur;
					$cpass['low_sec'] = '';
				
					include_spip('action/editer_auteur');
					auteur_modifier($id_auteur, $cpass, true); //	
					$arrayMensage= array('message'=>'Clave actualizada con exito!!','status' => '200');
					}
				$var = var2js($arrayMensage);	
				echo $var;		
			break;	
			case 'add':
					$app=new Apis('api_auteurs');
					$variablesAVerificar=array();
					$desc=array();
					$id_ou_options=0;
					$idUsuario = isset($_GET['idUsuario']) ? base64_decode($_GET['idUsuario']) : base64_decode($_POST['idUsuario']);
					$email = isset($_GET['email']) ? base64_decode($_GET['email']) : base64_decode($_POST['email']);
					$login = isset($_GET['login']) ? base64_decode($_GET['login']) : base64_decode($_POST['login']);
					$rol = isset($_GET['rol']) ? base64_decode($_GET['rol']) : base64_decode($_POST['rol']);
					$nombres = isset($_GET['nombres']) ? base64_decode($_GET['nombres']) : base64_decode($_POST['nombres']);
					$apellidos = isset($_GET['apellidos']) ? base64_decode($_GET['apellidos']) : base64_decode($_POST['apellidos']);
					$identificacion = isset($_GET['identificacion']) ? base64_decode($_GET['identificacion']) : base64_decode($_POST['identificacion']);
					$telefono = isset($_GET['telefono']) ? base64_decode($_GET['telefono']) : base64_decode($_POST['telefono']);
 						// Crea un array con las variables que deseas verificar
						$variablesAVerificar = [
							'idUsuario' => $idUsuario,
							'email' => $email,
							'login' => $login,
							'nombres' => $nombres,
							'apellidos' => $apellidos,
							'identificacion' => $identificacion,
							'telefono' => $telefono,
							'rol' => $rol,
							'entidad' => $entidad,
						];
		 
						$mensajeError = $app->verificarVariables($variablesAVerificar);
						if ($mensajeError !== null) {
						$arrayMensage[]=array('id'=>1,'message'=>'::ERROR-001:: '.$mensajeError.'','status'=>'404');
						}else{
								//$res = sql_select("statut, id_auteur, login, email", "api_auteurs", "entidad='".$entidad."' AND email=" . sql_quote($email));
								
								$chartic=array(); 
								if (!$r = email_valide($email)) {
									$msg[] = 'WARNING. email no tiene el formato de correo';
								}else{
									$options=array('tipo'=>$rol,'entidad'=>$entidad);
									$inscrire_auteur = charger_fonction('inscrire_auteur', 'action');
									$desc = $inscrire_auteur('0minirezo', $email, $login, $options);
								if (!is_null($desc)) {
										if($desc['pass']=='I'){
											$msg[] = 'WARNING. El Usuario no se pudo guardar!';
										
										}else{
												if(($rol=='Apoyo') || ($rol=='Coordinador')){
													$table ='sena_directivo';		
													$chartic['id_auteur'] ="".$desc['id_auteur']."";
													$chartic['identificacion'] ="".$identificacion."";
													$chartic['nombres'] =$nombres;
													$chartic['apellidos'] =$apellidos;
													$chartic['correo'] =$email;
													$chartic['celular'] =$telefono;
													$chartic['rol'] =$rol;
													$chartic = pipeline('pre_insertion',
														array(
															'args' => array(
															'table' => ''.$table.'',
														),
														'data' => $chartic
														)
													);							
													$id_auteur=@sql_insertq(''.$table.'',$chartic);
													pipeline('post_insertion',
													array(
														'args' => array(
														'table' =>''.$table.'',
														'id_objet' => $id_auteur
														),
														'data' => $chartic
														)
													);
												}
												if($rol=='Instructor'){
													$tableins ='sena_instructor';		
													$instructor['id_auteur'] ="".$desc['id_auteur']."";
													$instructor['identificacion'] ="".$identificacion."";
													$instructor['nombres'] =$nombres;
													$instructor['apellidos'] =$apellidos;
													$instructor['telefono'] =$telefono;
													$instructor['correo'] =$email;
													$instructor['rol'] =$rol;
													$instructor = pipeline('pre_insertion',
														array(
															'args' => array(
															'table' => ''.$tableins.'',
														),
														'data' => $instructor
														)
													);							
													$idInstructor=@sql_insertq(''.$tableins.'',$instructor);
													pipeline('post_insertion',
													array(
														'args' => array(
														'table' =>''.$tableins.'',
														'id_objet' => $idInstructor
														),
														'data' => $instructor
														)
													);
												}
												if($rol=='Aprendiz'){
													$tableapre ='sena_aprendiz';		
													$apre['nombres'] =$nombres;
													$apre['apellidos'] =$apellidos;
													$apre['identificacion'] ="".$identificacion."";
													$apre['telefono'] =$telefono;
													$apre['correo'] =$email;
													$apre = pipeline('pre_insertion',
														array(
															'args' => array(
															'table' => ''.$tableapre.'',
														),
														'data' => $tableapre
														)
													);							
													$idAprendiz=@sql_insertq(''.$tableapre.'',$apre);
													pipeline('post_insertion',
													array(
														'args' => array(
														'table' =>''.$tableins.'',
														'id_objet' => $idAprendiz
														),
														'data' => $apre
														)
													);
												}			
											//AUDITORIA
											$appAudi=new Apis('auditoria');
											$appAudi->guardar('AdminUsuarios','Usuarios','add');		
											//FIN AUDITORIA	
											$msg[] = 'Usuario guardado con exito! Su password es:'.$desc['pass'].', y el usuario: '.$desc['login'].'';
											};
									  }else{
										     $msg[] ='¡WARNING!. El Usuario no se pudo guardar!';
									}	
								}
								$arrayMensage[] = array('message'=>'¡OK!.'.implode(',',$msg).'','status' => '202');
						}	
				
				$resp = var2js($arrayMensage);
				echo $resp;
			break;
			case 'update':
				$apps=new Apis('api_auteurs');
				$login = isset($_GET['login']) ? base64_decode($_GET['login']) : base64_decode($_POST['login']);
				$rol = isset($_GET['rol']) ? base64_decode($_GET['rol']) : base64_decode($_POST['rol']);
				$id = isset($_GET['id']) ? base64_decode($_GET['id']) : base64_decode($_POST['id']);
				$nombres = isset($_GET['nombres']) ? base64_decode($_GET['nombres']) : base64_decode($_POST['nombres']);
					$chartic=array();
			
						$apps=new Apis('api_auteurs','Entidad="'.$entidad.'"');
    					$chartic['login']=$login;
    					$chartic['tipo']=$rol;
						$apps->actualizar($chartic,'id_auteur',$id);
						$msg[] = array('menssage'=>'OK. El Usuarios: '.$id.'-'.$nombres.' fue actualizado correctamente!','status' => '200');
						//AUDITORIA
						$appAudi=new Apis('auditoria');
						$appAudi->guardar('AdminUsuarios','Usuarios','update');		
						//FIN AUDITORIA					
						$var = var2js($msg);	
						echo $var;				
			
			break;
			case 'delete':
				$id = isset($_GET['id']) ? base64_decode($_GET['id']) : base64_decode($_POST['id']);
					sql_delete("api_auteurs","id_auteur=" . intval($id));
					
					$res = sql_select("statut, id_auteur, login, email", "api_auteurs", "id_auteur=" . intval($id));
					if ($res){
					$msg[] = array('menssage'=>'OK. El registro '.$id.' fue eliminado correctamente!','status' => '200');
					}	
					//AUDITORIA
					$appAudi=new Apis('auditoria');
					$appAudi->guardar('AdminUsuarios','Usuarios','delete');		
					//FIN AUDITORIA				
					$var = var2js($msg);	
					echo $var;					
			break;
			case 'update_rol':
				$apps=new Apis('apis_autorizaciones');
				$c = isset($_GET['c']) ? base64_decode($_GET['c']) : base64_decode($_POST['c']);
				$a = isset($_GET['a']) ? base64_decode($_GET['a']) : base64_decode($_POST['a']);
				$u = isset($_GET['u']) ? base64_decode($_GET['u']) : base64_decode($_POST['u']);
				$d = isset($_GET['d']) ? base64_decode($_GET['d']) : base64_decode($_POST['d']);
				$id = isset($_GET['id']) ? base64_decode($_GET['id']) : base64_decode($_POST['id']);
					$variablesAVerificar = [
						'c' => $c,
						'a' => $a,
						'u' => $u,
						'd' => $d,
						'id' => $id,
					];

					//print_r($variablesAVerificar);
					$mensajeError = $apps->verificarVariables($variablesAVerificar);
					if ($mensajeError !== null) {
					$arrayMensage[]=array('id'=>1,'message'=>'::ERROR-001:: '.$mensajeError.'','status'=>'404');
					}else{
						$chartic=array();
		
						$chartic['c']=$c;
    					$chartic['u']=$u;
    					$chartic['d']=$d;
    					$chartic['a']=$d;
						$apps->actualizar($chartic,'id',$id);
						//AUDITORIA
						$appAudi=new Apis('auditoria');
						$appAudi->guardar('AdminUsuarios','Usuarios','update_rol');		
						//FIN AUDITORIA
						$msg[] = array('menssage'=>'OK. Los permisos fueron actualizado correctamente!','status' => '200');
						$var = var2js($msg);	
						echo $var;						
					}

				

			break;
			
		}										
?>
