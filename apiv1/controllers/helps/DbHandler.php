<?php
/**
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/
require_once 'config.php';
 
class DbHandler extends PDO {
    /**
     * Constructor de la clase que inicializa la conexión a la base de datos
     */
    function __construct() {
        // Obtener parámetros de conexión
        $this->dbtype = "mysql";
        $this->host = Config::DB_HOST;
        $this->dbname = Config::DB_NAME;
        $this->dbcharset = "utf8";
        $this->user = Config::DB_USER;
        $this->password = Config::DB_PASSWORD;
        
        // Construir la cadena de conexión
        $dsn = $this->dbtype . ":host=" . $this->host . ";dbname=" . $this->dbname . ";charset=" . $this->dbcharset;
        
        // Definir opciones de conexión
        $arrOptions = array(
            PDO::ATTR_EMULATE_PREPARES => FALSE,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_PERSISTENT => TRUE
        );
        
        // Inicializar la conexión
        parent::__construct($dsn, $this->user, $this->password, $arrOptions);
    }

    /**
     * Valida si un usuario existe en la base de datos
     * @param string $login El login del usuario a validar
     * @return bool true si el usuario existe, false en caso contrario
     */
    function validarUsuario($login) {
        $query = "SELECT * FROM api_auteurs WHERE login = ?";
        $resultado = $this->metodoGet($query, array($login));
        if (empty($resultado)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Realiza una consulta SELECT a la base de datos
     * @param string $query La consulta SQL a ejecutar
     * @param array $datos Los parámetros de la consulta
     * @return array Un arreglo con los resultados de la consulta
     */
    function metodoGet($query, $datos) {
        try {
            $sentencia = $this->prepare($query);
            $sentencia->execute($datos);
            $sentencia->setFetchMode(PDO::FETCH_ASSOC);
            $resultado = $sentencia->fetchAll();
            return $resultado;
        } catch (PDOException $e) {
            die("Error: " . $e);
        }
    }
	function metodoSelect($query) {
			try {
				$sentencia = $this->prepare($query);
				$sentencia->execute();
				return $sentencia->fetchAll(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				error_log("Error al seleccionar datos: " . $e->getMessage());
				return array();
			}
	}
}
?>