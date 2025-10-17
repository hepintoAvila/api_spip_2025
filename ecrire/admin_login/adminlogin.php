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



if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
		
		include_spip('base/connect_sql');
		include_spip('inc/filtres_ecrire');
		include_spip('inc/filtres');
		include_spip('inc/utils');
		include_spip('inc/json');
		include_spip('inc/actions');
		
		
		
function admin_login_adminlogin_dist($get,$post){
        		
        		$opcion = base64_decode($get['opcion']);
        		$data =$post['data'];
        		
        		$session_login=$_SERVER['PHP_AUTH_USER'];
        		$session_password=$_SERVER['PHP_AUTH_PW'];
		
        		include_spip('inc/auth');
        		$aut = auth_identifier_login($session_login, $session_password);
				
				include_spip('ecrire/admin_login/loginService');
				include_spip('ecrire/admin_menu/menuService');
				include_spip('ecrire/admin_permisos/permisosService');
				include_spip('ecrire/admin_usuarios/usuariosService');
				
				//INSTANCIAS INVOLUNCRADAS  

				$app_loginService = new LoginService($aut);	
				$app_menuService = new MenuService();	
				$app_permisosService = new PermisoService($aut);
				$app_usuarioService = new UsuarioService();	
				
				
				// array
				$chartic=array();
		 
		switch ($opcion) {
			case 'admin_login_auth0':
               	
			    $Usuarios =$app_usuarioService->getUsuariosAuth0($data);
			    $auteur =$Usuarios[0];

			    if (isset($auteur) && intval($auteur['id_auteur']) > 0) {
			    
                	                    $app_loginServiceAuth0 = new LoginService($auteur);	
                			        	$encryptedData =$app_loginServiceAuth0->addKey();										
                        				$Menus = $app_menuService->getMenusUsuario($auteur);
                        			    $app_permisos= new PermisoService($auteur);
                        				$Permisos = $app_permisos->getPermisos();										
                        				$Auth['Auth']= array(
                        				'Nom' => $auteur['nom'].'',
                        				'Email' => $auteur['email'] ?? null,
                        				'Rol' => $auteur['tipo_usuario'],
                        				'status' => $auteur['status'],
                        				'AppKey' =>$encryptedData,
                        				);
                        				$records = array('status'=>200,'type' =>'success','message'=>'ok','message'=>'ok','data'=>array_merge($Auth,$Menus,$Permisos));
                			   		        
			    }else{
			   	                         $id_auteur=$auteur['id_auteur'];      
			   	                        
                                        /* BUSCAR EMAIL EN LA TABLA DE ESTUDIATES*/
                                         $Estudiantes = $app_usuarioService->getLoginAuth0($data);
                                         $id_auteur=$auteur['id_auteur']; 
                                         $id_estudiante=$Estudiantes['id_estudiante']; 
                                         
                                         if (intval($id_auteur) === 0 && intval($id_estudiante) === 0) {
                                             $Auth['Auth']= array(
                                				'Nom' => '',
                                				'Email' => $data['email'] ?? null,
                                				'Rol' => "",
                                				'status' => "Inactivo",
                                				'AppKey' =>'',
                                				);
                                			 $Menus=array('Menus'=>array());
                                			 $Permisos=array('Permisos'=>array());
                                             $records = array('status'=>200,'type' =>'success','message'=>'ok','message'=>'ok','data'=>array_merge($Auth,$Menus,$Permisos));  
                                             $var = var2js($records);
					                         echo $var;
                                             exit();
                                         }else{
                                         
                                         
                                         /* NORMALIZAMOS AUTEURS*/
                                         $auteurs = $app_usuarioService->addAuteursAuth0($Estudiantes);
                                         $id_auteur = $auteurs['id_auteur'];
                                         
                                         /*GENEREMOS YAVE SECRETA*/
                                         include_spip('ecrire/classes/security');
                                         $app_Servicekey = new ApiKeyManager();
                                         $app_Servicekey->asignarSecretKey($id_auteur);
                                       
                                          /*GENEREMOS APIKEY*/  
                                          $app_login = new LoginService($auteurs);
                                          $encryptedData =$app_login->addKey();
                                          
                                          /*GENEREMOS MENU*/   
                                          $Menus = $app_menuService->getMenusUsuario($auteurs);	
                                          
                                          /*GENEREMOS PERMISOS*/ 
                                          $app_permisos= new PermisoService($auteurs);
                                          $Permisos = $app_permisos->getPermisos();
                                          
                                           /*NORMALIZAMOS EL ARRAY DE ENVIO*/ 
                                          $Auth['Auth']= array(
                            				'Nom' => $auteurs['nom'].'',
                            				'Email' => $auteurs['email'] ?? null,
                            				'Rol' => $auteurs['tipo_usuario'],
                            				'status' => $auteurs['status'],
                            				'AppKey' =>$encryptedData,
                            				);
                            				
                            			  /*REGISTRAMOS EL USUARIO EN LA PLATAFORMA*/ 	
                                           $id=$app_usuarioService->addUsuarios($auteurs); 
                                           $records = array('status'=>200,'type' =>'success','message'=>'ok','message'=>'ok','data'=>array_merge($Auth,$Menus,$Permisos));
                                         }
			    }
				if (!is_null($records)) {
						$resp = $records;
					}else{
						$resp['data'] = array('status'=>402,'type' =>'error','message'=>'Error:: no existen datos');                           
					}
					$var = var2js($resp);
					echo $var;
		
			break;
			case 'login':
				 
				$encryptedData =$app_loginService->addKey();										
				$Menus = $app_menuService->getMenusUsuario($aut);										
				$Permisos = $app_permisosService->getPermisos();										
				$Auth['Auth']= array(
				'Nom' => $aut['nom'].'',
				'Email' => $aut['email'] ?? null,
				'Rol' => $aut['tipo_usuario'],
				'status' => $aut['status'],
				'AppKey' =>$encryptedData,
				);
				if (!is_null($aut)) {
						$records = array('status'=>200,'type' =>'success','message'=>'ok','message'=>'ok','data'=>array_merge($Auth,$Menus,$Permisos));
					}else{
						$records['data'] = array('status'=>402,'type' =>'error','message'=>'Error:: no existen datos');                           
					}
					$var = var2js($records);
					echo $var;

		break;
        case 'registrar_datos_personales':
            $programa = $app_usuarioService->getProgramas($data['programa']);
            $roles = $app_usuarioService->getRoles($data['rol']);
            $documento = $data['documento'];
            $email = $data['email'];
            $datos = array('programa' => $programa, 'tipo' => $roles, 'id_rol' => $data['rol'], 'documento' => $documento, 'email' => $email);
        
            try {
                [$auth, $menus, $permisos] = $app_usuarioService->registrarUsuario($datos);
            } catch (Exception $e) {
                spip_log("Error al registrar usuario: " . $e->getMessage(), _LOG_ERREUR);
                // Puedes intentar llamar al método nuevamente o devolver un mensaje de error
                try {
                    [$auth, $menus, $permisos] = $app_usuarioService->registrarUsuario($datos);
                } catch (Exception $e) {
                    spip_log("Error al registrar usuario (segunda llamada): " . $e->getMessage(), _LOG_ERREUR);
                    $records = array('status' => 500, 'type' => 'error', 'message' => 'Error al registrar usuario');
                    $var = var2js($records);
                    echo $var;
                    break;
                }
            }

            $records = array('status' => 200, 'type' => 'success', 'message' => 'ok', 'data' => array(
                $auth,
                'Menus' => [],
                'Permisos' => is_array($permisos) && isset($permisos['error']) ? [] : $permisos
            ));
            $var = var2js($records);
            echo $var;
        break;
        case 'registrar_datos_auth0_solicitud':
            
            $app_usuarioService = new UsuarioService();

            if (!isset($data['ubicacion']) || !isset($data['email'])) {
                echo "Error: No se proporcionaron los datos necesarios.";
                exit;
            }
            
            $ubicacion = $app_usuarioService->getUbicaciones($data['ubicacion']);
            
            $jornada = $app_usuarioService->obtenerJornada();
             
            $datosUser = $app_usuarioService->getLoginAuth0($data);
           
            if (!is_array($datosUser) || !isset($datosUser['documento']) || !isset($datosUser['programa'])) {
                echo "Error: No se pudo obtener la información del usuario.";
                exit;
            }
            
            $programa = $app_usuarioService->getProgramasId($datosUser['programa']);
            
            if($ubicacion==1){
            $chartic = array(
                'identificacion' => $datosUser['documento'],
                'tipo_visita' => $data['motivo'],
                'jornada' => $jornada,
                'ubicacion' => $ubicacion,
                'programa' => $programa
            );
                $app_usuarioService->addVisitas($chartic);
                
            }else{
                 
            $chartic = array(
                'pc' => $data['motivo']=='Otros'? 100 : $data['pc'],
                'documento' => $datosUser['documento'],
                'tipo_prestamo' => $data['motivo']=='Otros'? 12 : $data['motivo'],
                'ubicacion' => $ubicacion,
            );
                $app_usuarioService->addTurnos($chartic);
                
                if ($data['motivo'] != 'Otros') {
                $datosPc=array('id_pc'=>$data['pc'],'estado'=>'Ocupado');
                $app_usuarioService->updatePcs($datosPc);
                }
                
               include_spip('ecrire/admin_pcs/pcsService');
				//INSTANCIAS INVOLUNCRADAS

				$app_pcService = new PcsService();	
                $resultado =$app_pcService->getPcs();
            }
        break;
			case 'consulta_documento_usuario_solicitud':
			    
            if (!isset($data['documento'])) {
                echo "Error: No se proporcionaron los datos necesarios.";
                exit;
            }
            $app_usuarioService = new UsuarioService();
            $documento=$data['documento'];
            
            $id= $app_usuarioService->getDocumento($documento);
           
            //ESTA EN LA TABLA ESTUDIANTE
            if(intval($id)>0){
               
                //CONSULTE SI ESTA EN AUTEURS
                $aut= $app_usuarioService->getDocumentoId($id);
                
                if(intval($aut[0]['id_auteur'])>0){
                //SI EXISTE NO LO REGISTRE
                $encryptedData =$app_loginService->addKey();										
				$Menus['Menus'] = array();										
				$Permisos['Permisos'] = array();										
				
                $records = array('status' => 200, 'type' => 'success', 'message' => 'ok', 'data' => array(
	            'Auth' => array(
				'Nom' => $aut[0]['nom'],
				'Email' => $aut[0]['email'],
				'Rol' => $aut[0]['tipo'],
				'status' => $aut[0]['status'],
				'AppKey' =>$encryptedData,
				),
                'Menus' => [],
                'Permisos' =>  []
                )); 
                    
                }else{
                 //REGISTRELO DE UNA VEZ, YA QUE TIENE DATOS EN LA TABLA AUTEURS
                 $app_usuarioService = new UsuarioService();
                $documento = current($data['documento']);
                 
                        $datosPersonales= $app_usuarioService->getDocumentoEstudiantes($documento);
                        $email= $app_usuarioService->obtenerEmail($datosPersonales, $documento);
                        
                  	    $datos['documento']=$documento;
                    	$datos['id_estudiante']=$datosPersonales['id'];
                        $datos['nombres']=$documento;
                    	$datos['email'] = $email;
                    	$datos['tipo']=$datosPersonales['rol']??'Estudiante';
                    	$datos['id_rol']=$datosPersonales['id_rol']??'6';
                        $chartic= $app_usuarioService->charticAuteursMobile($datos);
                         $app_usuarioService->addUsuariosMobile($chartic); 
                      //CONSULTE SI ESTA EN AUTEURS
                        $aut= $app_usuarioService->getDocumentoId($datosPersonales['id']);
                        //SI EXISTE NO LO REGISTRE
                        $encryptedData =$app_loginService->addKey();										
        				$Menus['Menus'] = array();										
        				$Permisos['Permisos'] = array();										
                        $records = array('status' => 200, 'type' => 'success', 'message' => 'ok', 'data' => array(
        	            'Auth' => array(
        				'Nom' => $aut[0]['nom'],
        				'Email' => $aut[0]['email'],
        				'Rol' => $aut[0]['tipo'],
        				'status' => $aut[0]['status'],
        				'AppKey' =>$encryptedData,
        				),
                        'Menus' => [],
                        'Permisos' =>  []
                        )); 
                       
                }
                
            }else{
                $records = array('status' => 200, 'type' => 'success', 'message' => 'ok', 'data' => array(
	            'Auth' => array(
				'Nom' =>'',
				'Email' =>'',
				'Rol' =>'',
				'status' =>'Inactivo',
				'AppKey' =>'',
				),
                'Menus' => [],
                'Permisos' => []
               ));
                
            }
					$var = var2js($records);
					echo $var;              
           
	    break;
		}

}