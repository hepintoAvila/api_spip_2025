<?php
/**
 *
 * @About:      Database connection manager class
 * @File:       Database.php
 * @Date:       $Date:$ Nov-2015
 * @Version:    $Rev:$ 1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/
require_once 'config.php';
 
class DbHandler extends PDO {
    private $dbtype;
    private $host;
    private $dbname;
    private $dbcharset;
    private $user;
    private $password;


    function __construct() {
		$this->dbtype = "mysql";
        $this->host = Config::DB_HOST;
        $this->dbname = Config::DB_NAME;
        $this->dbcharset = "utf8";
        $this->user = Config::DB_USER;
        $this->password = Config::DB_PASSWORD;
        $dsn = $this->dbtype . ":host=" . $this->host . ";dbname=" . $this->dbname . ";charset=" . $this->dbcharset;
        $arrOptions = array(
            PDO::ATTR_EMULATE_PREPARES => FALSE,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_PERSISTENT => TRUE
        );
        parent::__construct($dsn, $this->user, $this->password, $arrOptions);
    }
	function validarUsuario($login) {
			$query = "SELECT * FROM api_auteurs WHERE login = ?";
			$resultado = $this->metodoGet($query, array($login));
			if (empty($resultado)) {
				return false;
			} else {
				return true; // El cliente ya existe
			}
		}
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
	function metodoInsert($query, $datos) {
		try {
			$sentencia = $this->prepare($query);
			$sentencia->execute($datos);
			return $this->lastInsertId();
		} catch (PDOException $e) {
			error_log("Error al insertar datos: " . $e->getMessage());
			return false;
		}
	}
	function metodoUpdate($query, $datos) {
		try {
			$sentencia = $this->prepare($query);
			$sentencia->execute($datos);
			return $sentencia->rowCount();
		} catch (PDOException $e) {
			error_log("Error al actualizar datos: " . $e->getMessage());
			return false;
		}
	}

	function metodoDelete($query, $datos) {
		try {
			$sentencia = $this->prepare($query);
			$sentencia->execute($datos);
			return $sentencia->rowCount();
		} catch (PDOException $e) {
			error_log("Error al eliminar datos: " . $e->getMessage());
			return false;
		}
	}	
}
?>