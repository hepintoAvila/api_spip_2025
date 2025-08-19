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
include_spip('inc/auth');
function authorization_credentials_dist() {
 				$headers = getallheaders();

				$authorization = $headers['Authorization'];
				$auth = explode(' ', $authorization);
				if ($auth[0] == 'Basic') {
					$credentials = base64_decode($auth[1]);
					list($username, $password) = explode(':', $credentials);

				}			 
				
				 
    				include_spip('authorization/Authenticator');
                    $authenticator = new Authenticator($username, $password);
    				$var_auth = $authenticator->authenticate();
					 
						// creer la session au besoin
					if (is_array($var_auth)) {
							/*
							spip_setcookie('spip_session', $_COOKIE['spip_session'], [
								'expires' => time() + 3600 * 24 * 14
							]);
							*/
						include_spip('inc/autoriser');
						if (!autoriser('loger', '', 0, $var_auth)) {
							return false;
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
						 //row = auth_mode();

						if (!$GLOBALS['connect_login']) {
							return auth_a_loger();
						}
				
					// Cas ou l'auteur a ete identifie mais on n'a pas d'info sur lui
					// C'est soit parce que la base est inutilisable,
					// soit parce que la table des auteurs a changee (restauration etc)
					// Pas la peine d'insister.
					// Renvoyer le nom fautif et une URL de remise a zero
					 
						return $var_auth;						
					}else{
						return '';
					}

	
}