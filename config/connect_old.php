
<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
defined('_MYSQL_SET_SQL_MODE') || define('_MYSQL_SET_SQL_MODE',true);
$GLOBALS['spip_connect_version'] = 0.8;
$GLOBALS['spip_secretKey'] = 'evaluasoft';
spip_connect_db('localhost','','user','pass','nomBD','mysql', 'api','','utf8');
?>