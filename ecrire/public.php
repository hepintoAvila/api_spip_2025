<?php
define('_ESPACE_PRIVE', true);
if (!defined('_ECRIRE_INC_VERSION')) {
	include 'inc_version.php';
}
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
 * Chargement (et affichage) d'une page ou d'un appel public
 *
 * @package SPIP\Core\Affichage
 **/

// Distinguer une inclusion d'un appel initial
// (cette distinction est obsolete a present, on la garde provisoirement
// par souci de compatiilite).


/*
 * Documentation : https://www.spip.net/fr_article4200.html
 */



if (isset($GLOBALS['_INC_PUBLIC']) and $GLOBALS['_INC_PUBLIC']) {
	
	echo recuperer_fond($fond, $contexte_inclus, array(), _request('connect'));

} else {

	
	$GLOBALS['_INC_PUBLIC'] = 1;
	define('_PIPELINE_SUFFIX', test_espace_prive() ? '_prive' : '');
		/*
		 * Échappement xss referer
		 */
		if (isset($_SERVER['HTTP_REFERER'])) {
			$_SERVER['HTTP_REFERER'] = strtr($_SERVER['HTTP_REFERER'], '<>"\'', '[]##');
		}


		/*
		 * Echappement HTTP_X_FORWARDED_HOST
		 */
		if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
			$_SERVER['HTTP_X_FORWARDED_HOST'] = strtr($_SERVER['HTTP_X_FORWARDED_HOST'], "<>?\"\{\}\$'` \r\n", '____________');
		}
			
			$__request = array_merge($_POST, $_GET);
			//print_r($__request);
			$res = array();
			// Validación de seguridad
			$campos = array('exec', 'accion', 'opcion', '_SPIP_PAGE', 'action', 'var_ajax');
				$valido = true;
				$campos_decodificables = array('accion', 'opcion');

				foreach ($campos as $campo) {
					if (isset($__request['params'])) {
						$params = json_decode($__request['params'], true);
						$__request = array_merge($__request, $params);
						unset($__request['params']); // Elimina el campo params del arreglo
					}
					if (in_array($campo, $campos_decodificables) && preg_match(',^[a-zA-Z0-9+/=]+$,', $__request[$campo])) {
						$decoded_value = base64_decode($__request[$campo], true);
						if ($decoded_value === false) {
							$res[] = $campo . ' (decodificación fallida)';
							print_r($res);
							$valido = false;
							break;
						} else {
							if (!preg_match(',^[\w-]+$,', $decoded_value)) {
								$res[] = $campo . ' (valor decodificado inválido)';
								print_r($res);
								$valido = false;
								break;
							}
						}
					} else {
						if (!preg_match(',^[a-zA-Z0-9_-]+$,', $__request[$campo])) {
						$res[] = $campo . ' (valor inválido)';
						print_r($res);
						$valido = false;
						break;
						}
					}
				}
			// Verificar si se activó la seguridad
			if (!$valido) {
				$records['data'] = array(
					'status' => 400,
					'menssage' => 'Solicitud inválida',
					'error' => '1.ECRAN_SECURITE activado'
				);
				echo json_encode($records);
				return;
			}
 
	/**
	 * Version simplifiée de https://developer.wordpress.org/reference/functions/is_serialized/
	 */
	if (!function_exists('__ecran_test_if_serialized')) {
		function __ecran_test_if_serialized($data) {
			$data = trim($data);
			if ('N;' === $data) {return true;}
			if (strlen($data) < 4) {return false;}
			if (':' !== $data[1]) {return false;}
			$semicolon = strpos($data, ';');
			$brace = strpos($data, '}');
			// Either ; or } must exist.
			if (false === $semicolon && false === $brace) {return false;}
			// But neither must be in the first X characters.
			if (false !== $semicolon && $semicolon < 3) {return false;}
			if (false !== $brace && $brace < 4) {return false;}
			$token = $data[0];
			if (in_array($token, array('s', 'S', 'a', 'O', 'C', 'o', 'E'))) {
				if (in_array($token, array('s', 'S')) and false === strpos($data, '"')) {return false;}
				return (bool)preg_match("/^{$token}:[0-9]+:/s", $data);
			} elseif (in_array($token, array('b', 'i', 'd'))) {
				return (bool)preg_match("/^{$token}:[0-9.E+-]+;/", $data);
			}
			return false;
		}
	}		
		if (
			 !empty($__request['var_login'])
		) {
			foreach ($__request as $k => $v) {
				if (is_string($v)
				  and strpbrk($v, "&\"'<>") !== false
				  and preg_match(',^[abis]:\d+[:;],', $v)
				  and __ecran_test_if_serialized($v)
				) {
					$__request[$k] = $_REQUEST[$k] = htmlspecialchars($v, ENT_QUOTES);
					if (isset($_POST[$k])) $_POST[$k] = $__request[$k];
					if (isset($_GET[$k])) $_GET[$k] = $__request[$k];
				}
			}
		}
		/*
		 * Injection par connect [HTTP_CONNECTION] => keep-alive
		 */
		if (
			isset($GLOBALS['HTTP_CONNECTION'])
			// cas qui permettent de sortir d'un commentaire PHP
			and (
				strpos($GLOBALS['HTTP_CONNECTION'], "?") !== false
				or strpos($GLOBALS['HTTP_CONNECTION'], "<") !== false
				or strpos($GLOBALS['HTTP_CONNECTION'], ">") !== false
				or strpos($GLOBALS['HTTP_CONNECTION'], "\n") !== false
				or strpos($GLOBALS['HTTP_CONNECTION'], "\r") !== false
			)
		) {
					$records['data'] = array(
						'status' => 400,
						'menssage' => 'Solicitud inválida',
						'error' => '2.HTTP_CONNECTION activado'
					);
					echo json_encode($records);
					return;
		} 
		/*
		 * Réinjection des clés en html dans l'admin r19561
		 */
		if (
			(isset($_SERVER['REQUEST_URI']) and strpos($_SERVER['REQUEST_URI'], "ecrire/") !== false)
			or isset($__request['var_memotri'])
		) {
			$zzzz = implode("", array_keys($__request));
			if (strlen($zzzz) != strcspn($zzzz, '<>"\'')) {
				$ecran_securite_raison = 'Cle incorrecte en $__request';
			}
		}		
	// Faut-il initialiser SPIP ? (oui dans le cas general)
	if (!defined('_DIR_RESTREINT_ABS')) {
		if (defined('_DIR_RESTREINT')
			and @file_exists(_ROOT_RESTREINT . 'inc_version.php')
		) {
			include_once _ROOT_RESTREINT . 'inc_version.php';
		} else {
			die('inc_version absent ?');
		}
	
	} // $fond defini dans le fichier d'appel ?

	else {
		
		if (isset($fond) and !_request('fond')) {
			
		} // fond demande dans l'url par page=xxxx ?
		else {
			
			if (isset($_GET[_SPIP_PAGE])) {
				$fond = (string)$_GET[_SPIP_PAGE];
				
				// Securite
				if (strstr($fond, '/')
					and !(
						isset($GLOBALS['visiteur_session']) // pour eviter d'evaluer la suite pour les anonymes
						and include_spip('inc/autoriser')
						and autoriser('webmestre'))
				) {
					
					include_spip('inc/minipres');
					echo minipres();
					exit;
				}
				// l'argument Page a priorite sur l'argument action
				// le cas se presente a cause des RewriteRule d'Apache
				// qui permettent d'ajouter un argument dans la QueryString
				// mais pas d'en retirer un en conservant les autres.
				if (isset($_GET['action']) and $_GET['action'] === $fond) {
					unset($_GET['action']);
				}
				# sinon, fond par defaut
			} else {
				// sinon fond par defaut (cf. assembler.php)
				$fond = pipeline('detecter_fond_par_defaut', '');
			}
		}
	}

	$tableau_des_temps = array();
	if (isset($forcer_lang) and $forcer_lang and ($forcer_lang !== 'non')
		and !_request('action')
		and $_SERVER['REQUEST_METHOD'] != 'POST'
	) {
		include_spip('inc/lang');
		verifier_lang_url();
	}

	$lang = !isset($_GET['lang']) ? '' : lang_select($_GET['lang']);
	$exec = (string)_request('exec');
	// Charger l'aiguilleur des traitements derogatoires
	// (action en base SQL, formulaires CVT, AJax)
	if (_request('action') or _request('var_ajax') or _request('formulaire_action')) {
		if (defined('_DIRECT_CRON_FORCE')) {
			cron();
		}
		if ($lang) {
				lang_select();
		}
				 
			if (autoriser_sans_cookie($exec, false)) {
				
					if (!isset($reinstall)) {
						$reinstall = 'non';
						
					}
				 
				} else {
						$exec = $GLOBALS['_GET']['_SPIP_PAGE'];
			
						if ($var_f = tester_url_ecrire($exec)) {
							include_spip('inc/actions');
							$var_f = charger_fonction($var_f);
							$var_f($GLOBALS['_GET']);
							exit;		
						} else {
							$var_f = charger_fonction('404');
							$var_f($exec);
						}
				}	 

	}

/*
 * Fin sécurité
 */

}
