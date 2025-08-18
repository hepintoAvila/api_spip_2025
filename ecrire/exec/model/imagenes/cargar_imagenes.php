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
 
include_spip('inc/auth');
		
	$opcion = filter_input(INPUT_GET, 'opcion', FILTER_SANITIZE_STRING) ?? filter_input(INPUT_POST, 'opcion', FILTER_SANITIZE_STRING);
	
	if ($opcion !== null) {
		$opcion = base64_decode($opcion);
	}else{
		$opcion = $_POST["params"]["opcion"];
		$opcion = base64_decode($opcion);
	}
	
if($opcion !== null)
    {

    switch ($opcion)
		{
case 'cargar_imagenes':

    $dir = _ROOT_RACINE.''._NOM_PERMANENTS_ACCESSIBLES .'';
    $files = scandir($dir);
    if ($files === false) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'No se pudo leer el directorio']);
        return;
    }
    $images = [];
	$i=1;
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && is_file($dir . $file)) {
            $images[] = array(
                'url' => 'https://www.lacasadelbarbero.com.co/api2024/IMG/' . $file,
                'thumb' => 'https://www.lacasadelbarbero.com.co/api2024/IMG/' . $file,
                'name' => $file,
				'id' => $i,
				"type"=>"image"
            );
        }
		$i++;
    }
    header('Content-Type: application/json');
    echo json_encode($images, JSON_UNESCAPED_SLASHES);
    return;
    break;
 }
}else {
		// Maneja el caso cuando 'accion' no esté presente en GET ni POST
	   $records['data'] = array('status'=>404);
	   $var = var2js($records);
		echo $var; 
} 
?>