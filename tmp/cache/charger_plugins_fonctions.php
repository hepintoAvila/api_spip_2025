<?php

if (defined('_ECRIRE_INC_VERSION')) {
if (!function_exists('boutons_plugins')) {
function boutons_plugins(){return defined('_UPDATED_boutons_plugins')?unserialize(_UPDATED_boutons_plugins):unserialize('a:0:{}');}
function md5_boutons_plugins(){return defined('_UPDATED_md5_boutons_plugins')?_UPDATED_md5_boutons_plugins:'40cd750bba9870f18aada2478b24840a';}
}
if (!function_exists('onglets_plugins')) {
function onglets_plugins(){return defined('_UPDATED_onglets_plugins')?unserialize(_UPDATED_onglets_plugins):unserialize('a:0:{}');}
function md5_onglets_plugins(){return defined('_UPDATED_md5_onglets_plugins')?_UPDATED_md5_onglets_plugins:'40cd750bba9870f18aada2478b24840a';}
}
}
?>