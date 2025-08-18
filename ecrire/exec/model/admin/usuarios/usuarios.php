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
		include_spip('exec/model/admin/claseapi');
		include_spip('inc/auth');
				
				$variables = json_decode(urldecode($_GET['variables']), true);
				$opcion = base64_decode($variables['opcion']);
				$array =$variables['data'];				
				$data = json_decode($array, true);
				try {
					$campos = $GLOBALS['tables_principales']['spip_auteurs']['field'];
					
					$select = implode(',',array_keys($campos));
		 
				//INSTANCIAS INVOLUNCRADAS
					$camposConsulta = array(
						'id_auteur',
						'nom',
						'email',
						'login',
					 );  
				 $appAudi=new Apis('apis_auditoria');
				 $app_auteurs=new Apis('spip_auteurs');
				 $app_aspirantes=new Apis('unal_aspirantes');
				// array
				$chartic=array();
				$cpass = array();		 
				$champs = array();		 
			} catch (Exception $e) {
				$records['data'] = array('status'=>'401','error'=>$e->getMessage());  
				echo json_encode($records);
				exit;
			}

	switch ($opcion) {		
        case 'update':	
			$email = unicode2charset(utf_8_to_unicode($email), 'iso-8859-1');
			if (!$r = email_valide($data['email'])){
				$records['data'] =array('error'=>'::ERROR-003:: email no tiene el formato de correo','status'=>'404');
				$var = var2js($records); 	
				echo $var;
				return;				
			} 
					
					$champs['nom']=$data['nom'];
					$champs['tipo']=$data['tipo'];
					$champs['email']=$data['email'];
					$champs['id_rol'] = sql_getfetsel("idRol","apis_roles", 'tipo=' . sql_quote($data['tipo']));
					sql_updateq('spip_auteurs',$champs, 'id_auteur=' .$data['id_auteur']);
					
			 	$campos_auteurs= $GLOBALS['tables_principales']['spip_auteurs']['field'];
				$select = implode(',',array_keys($campos_auteurs));
				$campos = array('id_auteur','nom','email','login','tipo');
				$records=$app_auteurs->consultadatos('status="Activo" AND id_auteur!=1',$select,$campos);	
				$usuarios = array('data'=>array('Auteurs'=>$records));
				$var = var2js($usuarios); 	
                echo $var;					
		break;
        case 'guardar':
			$email=$data['email'];
			$tipo=$data['tipo'];
			$new_pass=$data['pass'];
			$login=$data['login'];
			
			$options=array('tipo'=>$tipo,'entidad'=>'cb_1','clave'=>$new_pass,'login'=>$login);
			$email = unicode2charset(utf_8_to_unicode($email), 'iso-8859-1');
			if (!$r = email_valide($email)){
				$records['data'] =array('error'=>'::ERROR-003:: email no tiene el formato de correo','status'=>'404');
				$var = var2js($records); 	
				echo $var;
				return;				
			}else{
				$inscrire_auteur = charger_fonction('inscrire_auteur', 'action');
				$desc = $inscrire_auteur('0minirezo', $email, $login, $options);
				if (is_null($desc)) {
					$records['data'] = array('status'=>'404','error'=>'::ERROR-004:: usuario no tiene valores correctos');  
					$var = var2js($records); 	
					echo $var;
					return;
				}
					
					$champs['id_rol'] = sql_getfetsel("idRol","apis_roles", 'tipo=' . sql_quote($data['tipo']));
					sql_updateq('spip_auteurs',$champs, 'id_auteur=' .$desc['id_auteur']);
					
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
					auteur_modifier($desc['id_auteur'], $cpass, true); 			
				 
			}
			 	$campos_auteurs= $GLOBALS['tables_principales']['spip_auteurs']['field'];
				$select = implode(',',array_keys($campos_auteurs));
				$campos = array('id_auteur','nom','email','login','tipo');
				$records=$app_auteurs->consultadatos('status="Activo" AND id_auteur!=1',$select,$campos);	
				$usuarios = array('data'=>array('Auteurs'=>$records));
				$var = var2js($usuarios); 	
                echo $var;
            break;
			case 'actualizapass':
			$id_auteur=$data['id_auteur'];
			
			if (!$data['id_auteur'] = intval($id_auteur)) {
				return false;
			}
			if (isset($data['login']) and strlen($data['login'])) {
				$champs['login'] = $data['login'];
			}
			if (isset($data['pass']) and strlen($data['pass'])) {
				$champs['pass'] = $data['pass'];
			}
				$statut = sql_getfetsel('statut', 'spip_auteurs', 'id_auteur=' . intval($id_auteur));
				$champs['statut']=$statut;
				$flag_ecrire_acces = false;
			// commencer par traiter les cas particuliers des logins et pass
			// avant les autres ecritures en base
					if (isset($champs['login']) or isset($champs['pass'])) {
						$auth_methode = sql_getfetsel('source', 'spip_auteurs', 'id_auteur=' . intval($id_auteur));
						include_spip('inc/auth');
						if (isset($champs['login']) and strlen($champs['login'])) {
							if (!auth_modifier_login($auth_methode, $champs['login'], $id_auteur)) {
								$erreurs[] = 'ecrire:impossible_modifier_login_auteur';
							}
						}
						if (isset($champs['pass']) and strlen($champs['pass'])) {
							$champs['login'] = sql_getfetsel('login', 'spip_auteurs', 'id_auteur=' . intval($id_auteur));
							if (!auth_modifier_pass($auth_methode, $champs['login'], $champs['pass'], $id_auteur)) {
								$erreurs[] = 'ecrire:impossible_modifier_pass_auteur';
							}
						}
						unset($champs['login']);
						unset($champs['pass']);
						$flag_ecrire_acces = true;
					}

					if (!count($champs)) {
						return implode(' ', array_map('_T', $erreurs));
					}
					sql_updateq('spip_auteurs', $champs, 'id_auteur=' . $id_auteur);

					// .. mettre a jour les fichiers .htpasswd et .htpasswd-admin
					if ($flag_ecrire_acces
						or isset($champs['statut'])
					) {
						include_spip('inc/acces');
						ecrire_acces();
					}
			 	$campos_auteurs= $GLOBALS['tables_principales']['spip_auteurs']['field'];
				$select = implode(',',array_keys($campos_auteurs));
				$campos = array('id_auteur','nom','email','login','tipo');
				$records=$app_auteurs->consultadatos('status="Activo" AND id_auteur!=1',$select,$campos);	
				$usuarios = array('data'=>array('Auteurs'=>$records),'status'=>'202','menssage'=>'Password actualizado correctamente');
				$var = var2js($usuarios); 	
                echo $var;
			break;
            case 'consulta':
				$records=array();
 			
				$campos_aspirantes = $GLOBALS['tables_principales']['unal_aspirantes']['field'];
				$select = implode(',',array_keys($campos_aspirantes));
				$campos = array('id_aspirante','primer_nombre','segundo_nombre','primer_apellido','segundo_apellido','documento','tipo_documento','email,celular','pais','colegio','grado','discapacitado','statut','tipo');
				
				$records=$app_aspirantes->consultadatos('statut="Activo"',$select,$campos);	
				$var = var2js(array('status'=>202,'type'=>'success','data'=>array('Usuarios'=>$records))); 	
                echo $var;	
                break;
                case 'delete': 
					$id_auteur=$data['id_auteur'];
					if (!$data['id_auteur'] = intval($id_auteur)) {
						return false;
					}
					$champs['status']='Inactivo';
					sql_updateq('spip_auteurs', $champs, 'id_auteur=' . $id_auteur);
				
					$campos_auteurs= $GLOBALS['tables_principales']['spip_auteurs']['field'];
					$select = implode(',',array_keys($campos_auteurs));
					$campos = array('id_auteur','nom','email','login','tipo');
					$records=$app_auteurs->consultadatos('status="Activo" AND id_auteur!=1',$select,$campos);	
					$usuarios = array('data'=>array('Auteurs'=>$records),'status'=>'202','menssage'=>'Password actualizado correctamente');
					$var = var2js($usuarios); 	
					echo $var;
                break;
		}
	           
