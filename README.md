# API SPIP 2025

La siguiente documentación proporciona una guía detallada para implementar una API en SPIP utilizando PHP y MySQL. A continuación, se presenta una descripción general de cómo implementar la API:

### Requisitos

SPIP (Système de Publication pour l'Internet Partagé)
PHP 7.x o superior
MySQL 5.x o superior
Conocimientos básicos de PHP, MySQL y SPIP

### Estructura de la API

La API se estructurará de la siguiente manera:
DbHandler: Clase que maneja la conexión a la base de datos MySQL y proporciona métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar).
EnviarController: Clase que maneja las solicitudes de envío de datos a la API y proporciona métodos para validar la autenticación y autorización de los usuarios.
AuthorController: Clase que maneja la autenticación y autorización de los usuarios y proporciona métodos para validar la autenticación y autorización.
Implementación
Configuración de la base de datos: Configurar la base de datos MySQL y crear las tablas necesarias para la API.
Implementación de la clase DbHandler: Implementar la clase DbHandler para manejar la conexión a la base de datos y proporcionar métodos para realizar operaciones CRUD.
Implementación de la clase EnviarController: Implementar la clase EnviarController para manejar las solicitudes de envío de datos a la API y proporcionar métodos para validar la autenticación y autorización de los usuarios.
Implementación de la clase AuthorController: Implementar la clase AuthorController para manejar la autenticación y autorización de los usuarios y proporcionar métodos para validar la autenticación y autorización.
Implementación de la API: Implementar la API utilizando las clases DbHandler, EnviarController y AuthorController.
Endpoints

### La API tendrá los siguientes endpoints:
/api/usuarios: Endpoint para obtener la lista de autores.
/api/auteur/: Endpoint para obtener la información de inicio de sesion de un autor específico.
/api/roles: Endpoint para optener los roles.
/api/menu: Endpoint para optener el menu asignado.

### Seguridad
La API utilizará autenticación y autorización para garantizar la seguridad de los datos. La autenticación se realizará mediante tokens de acceso y la autorización se realizará mediante roles y permisos.

### Ventajas
La implementación de esta API en SPIP proporcionará las siguientes ventajas:
Acceso a los datos: La API proporcionará acceso a los datos de la base de datos de SPIP de manera segura y controlada.
Flexibilidad: La API permitirá la integración con otras aplicaciones y servicios.
Escalabilidad: La API estará diseñada para escalar según las necesidades de la aplicación.

### Envio de las variables desde el frontend:

En el siguiente funcion se envia los datos a la api del SPIP
### Nota: la url debe apuntar a: 
### url: "https://www.tu-hosting/api_spip_2025/apiv1/?"


 const config = {
	 
    API_URL_WEB: "https://www.tu-hosting/api_spip_2025/",
    API_URL: "https://www.tu-hosting/api_spip_2025/api2024/apiv1/?",
   // API_URL: "https://www.tu-hosting/api_spip_2025/api2024/plugins-dist/api2025/?",
	API_URL_AUTH : '/dashboard/tarjetas', 
	API_ACCION_AUTH  :  'auteur', 
	API_OPCION_AUTH  : 'login', 
	API_ACCION_ROLES:'roles',
	API_ACCION_USUARIOS:'usuarios',

	X_SICES_API_APIKEY:'zHqroBk5BILvT9Bdajol1A==::snR8ET+DxazKVH5Ywxx1Fg==',
	X_SICES_API_APITOKEN:'f37ef65fbc641054a3b508af0b52220916ff40eefa9a0a722b4d69cda96ef064',

	X_SICES_API_APIKEY_USER:'U9YbCFRmbhm9GjlHTAuKvgipgvku1ljhR9YbTkpMNtXmhMa/tsvlZS2eCOtGqe94vNJ+QDtM23pRQxRytrZR3TtXyPbozsxT4q0E/z/hY5o=::1hFXGPLErNPJxf8FuXdeLA==',
	X_SICES_API_APITOKEN_USER:'1eddeff45f94f4ab39b8c1f0ee840ed296b606608c3af799787f2dafa9e64320',
	X_SICES_API_USER:'X_SICES_API_USER un usuario especial de la tabla api_auteurs',
	X_SICES_API_PASS:'X_SICES_API_PASS el pass del usuario especial de la tabla api_auteurs',
};

export default config;


 const send = async (data: any) => {
    try {
      
      const response = await fetch(`${config.API_URL}${url}`, {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
           Authorization: `Basic ${btoa(`${config.X_SICES_API_USER}:${config.X_SICES_API_PASS}`)}`,
	        'X-SICES-API-USER': btoa(`${config.X_SICES_API_USER}`),
	        'X-SICES-API-Apikey': btoa(`${config.X_SICES_API_APIKEY_USER}`),
	        'X-SICES-API-ApiToken': btoa(`${config.X_SICES_API_APITOKEN_USER}`),
          'Content-Type': 'application/json',
        },
      });
	    if (!response.ok) {
        throw new Error(ErrorCodeMessages[response.status] || 'Unknown error');
      }
	 
      const contentType = response.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
         return response.json();
      } else {
        const text = await response.text();
        throw new Error(`La respuesta no es JSON válida: ${text}`);
      }
    } catch (error) {
      console.error(error);
      throw error;
    }
  };

--
-- Estructura de tabla para la tabla `api_auteurs`
--

```markdown

CREATE TABLE `api_auteurs` (
  `id_auteur` bigint(21) NOT NULL,
  `nom` text NOT NULL DEFAULT '',
  `bio` text NOT NULL DEFAULT '',
  `email` tinytext NOT NULL DEFAULT '',
  `nom_site` tinytext NOT NULL DEFAULT '',
  `url_site` text NOT NULL DEFAULT '',
  `login` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `pass` tinytext NOT NULL DEFAULT '',
  `low_sec` tinytext NOT NULL DEFAULT '',
  `statut` varchar(255) NOT NULL DEFAULT '0',
  `webmestre` varchar(3) NOT NULL DEFAULT 'non',
  `maj` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pgp` text NOT NULL DEFAULT '',
  `htpass` tinytext NOT NULL DEFAULT '',
  `en_ligne` datetime NOT NULL DEFAULT '2024-01-01 01:01:01',
  `alea_actuel` tinytext DEFAULT NULL,
  `alea_futur` tinytext DEFAULT NULL,
  `prefs` text DEFAULT NULL,
  `cookie_oubli` tinytext DEFAULT NULL,
  `source` varchar(10) NOT NULL DEFAULT 'spip',
  `lang` varchar(10) NOT NULL DEFAULT '',
  `imessage` varchar(3) NOT NULL DEFAULT '',
  `tipo` enum('Aspirante','Administrador','Docente','Admitido') NOT NULL,
  `entidad` varchar(8) NOT NULL DEFAULT 'cb_1',
  `id_rol` enum('1','2','3','4','5') NOT NULL,
  `status` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `clave` varchar(255) NOT NULL,
  `tipoEntrevista` varchar(45) NOT NULL,
  `id_aspirante` bigint(21) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
 
--
-- Datos para la tabla `api_auteurs`
--
INSERT INTO `api_auteurs` (`id_auteur`, `nom`, `bio`, `email`, `nom_site`, `url_site`, `login`, `pass`, `low_sec`, `statut`, `webmestre`, `maj`, `pgp`, `htpass`, `en_ligne`, `alea_actuel`, `alea_futur`, `prefs`, `cookie_oubli`, `source`, `lang`, `imessage`, `tipo`, `entidad`, `id_rol`, `status`, `clave`, `tipoEntrevista`, `id_aspirante`) VALUES
(1, 'evaluasoft', '', 'evaluasoft@gmail.com', '', '', 'X_SICES_API_USER', 'X_SICES_API_PASS', '', '0minirezo', 'non', '2025-08-06 20:39:03', '', '', '2025-08-06 15:39:03', '', '', '0minirezo', '', 'spip', 'es', 'oui', 'Administrador', 'cb_1', '', 'Activo', '', '', NULL),

### Descripción del inicio del end point

Este archivo index.php es el punto de entrada para la API. Maneja las solicitudes HTTP y las dirige a los controladores correspondientes.

### Configuración
Error Reporting: El archivo establece el nivel de reporte de errores en E_ALL y muestra los errores en pantalla.
Cabeceras HTTP: Establece las cabeceras HTTP para permitir el acceso a la API desde cualquier origen y especifica el tipo de contenido como JSON.
Manejo de Solicitudes
OPTIONS: Maneja las solicitudes OPTIONS y devuelve un código de estado 200 OK.
Autoload: Utiliza spl_autoload_register para cargar automáticamente las clases de los controladores.

### Funciones
getBaseUrl: Obtiene la URL base de la aplicación.

### Enrutamiento

Router: Utiliza la clase Router para definir y manejar las rutas de la API.
Rutas: Define varias rutas para la API, incluyendo:
POST /menu: Maneja las solicitudes POST para el menú.
POST /usuarios: Maneja las solicitudes POST para los usuarios.
POST /roles: Maneja las solicitudes POST para los roles.
GET /auteur: Maneja las solicitudes GET para los autores.

### Controladores
EnviarController: Maneja las solicitudes POST para el menú, usuarios y roles.
AuthorController: Maneja las solicitudes GET para los autores.

<?php
/**
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/

// Configuración de error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cabeceras HTTP
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json; charset=utf-8");

// Manejar solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

// Autoload de clases
spl_autoload_register(function ($class_name) {
    if (file_exists('controllers/' . $class_name . '.php')) {
        include 'controllers/' . $class_name . '.php';
    } elseif (file_exists($class_name . '.php')) {
        include $class_name . '.php';
    }
});

// Función para obtener la URL base
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = dirname($script);
    return $protocol . $host . $path;
}

$baseUrl = getBaseUrl() . '/apiv1/';

// Verificar si las cabeceras han sido enviadas
if (headers_sent()) {
    header('Location: ' . $baseUrl);
    exit;
}

// Incluir el enrutador
require_once 'Router.php';

// Inicializar el enrutador
$router = new Router();

// Definir rutas
$router->addRoute('POST', 'menu', ['EnviarController', 'handleRequest']);
$router->addRoute('POST', 'usuarios', ['EnviarController', 'handleRequest']);
$router->addRoute('POST', 'roles', ['EnviarController', 'handleRequest']);
$router->addRoute('GET', 'auteur', ['AuthorController', 'handleRequest']);

// Manejar la solicitud
$router->handleRequest();

?>

### Documentación de la Clase DbHandler

### Descripción
La clase DbHandler es una extensión de la clase PDO que proporciona métodos para interactuar con una base de datos MySQL. Proporciona métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) y validar usuarios.
### Métodos

__construct()
Constructor de la clase que inicializa la conexión a la base de datos.
Utiliza la clase Config para obtener los parámetros de conexión.
validarUsuario($login)
Valida si un usuario existe en la base de datos.

### Parámetros:

$login: El login del usuario a validar.
Retorno: true si el usuario existe, false en caso contrario.
metodoGet($query, $datos)
Realiza una consulta SELECT a la base de datos.

### Parámetros:
$query: La consulta SQL a ejecutar.
$datos: Los parámetros de la consulta.
Retorno: Un arreglo con los resultados de la consulta.
metodoSelect($query)
Realiza una consulta SELECT a la base de datos sin parámetros.

### Parámetros:

$query: La consulta SQL a ejecutar.
Retorno: Un arreglo con los resultados de la consulta.
metodoInsert($query, $datos)
Realiza una consulta INSERT a la base de datos.

### Parámetros:
$query: La consulta SQL a ejecutar.
$datos: Los parámetros de la consulta.
Retorno: El ID del registro insertado o false en caso de error.
metodoUpdate($query, $datos)
Realiza una consulta UPDATE a la base de datos.

### Parámetros:
$query: La consulta SQL a ejecutar.
$datos: Los parámetros de la consulta.
Retorno: El número de filas afectadas o false en caso de error.
metodoDelete($query, $datos)
Realiza una consulta DELETE a la base de datos.

### Parámetros:
$query: La consulta SQL a ejecutar.
$datos: Los parámetros de la consulta.
Retorno: El número de filas afectadas o false en caso de error.

### Funcionamiento
La clase DbHandler utiliza la clase Config para obtener los parámetros de conexión a la base de datos.
El constructor de la clase inicializa la conexión a la base de datos utilizando los parámetros de conexión.
Los métodos de la clase utilizan consultas preparadas para evitar inyecciones SQL y mejorar la seguridad.

```markdown

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

    // ... otros métodos ...
}


### Documentación de la Clase EnviarController

### Descripción
La clase EnviarController es responsable de manejar solicitudes de envío de datos a través de una API. Proporciona métodos para obtener datos de autenticación, parámetros de la URL y manejar solicitudes de envío de archivos.

### Métodos

	### handleRequest()
Maneja la solicitud de envío de datos y verifica la autenticación del usuario.
Obtiene los datos de autenticación y los parámetros de la URL.
Si se envía un archivo, lo procesa y prepara los datos para su envío.
Si no se envía un archivo, obtiene los datos del cuerpo de la solicitud.
Verifica la autenticación del usuario y envía los datos si es válida.

	### getAuthData()
Obtiene los datos de autenticación de la solicitud.
Verifica la presencia de los encabezados de autenticación y los decodifica si es necesario.
Retorna un arreglo con los datos de autenticación.

	### getUrlParams()
Obtiene los parámetros de la URL de la solicitud.
Verifica si se debe aplicar la codificación Base64 a los parámetros.
Retorna un arreglo con los parámetros de la URL.

	### Funcionamiento
### 1. La clase EnviarController utiliza la clase DbHandler para interactuar con la base de datos.
### 2. El método handleRequest maneja la solicitud de envío de datos y verifica la autenticación del usuario.
### 3. Si la autenticación es válida, envía los datos utilizando la función makeCurlRequest.

```markdown

class EnviarController {
    /**
     * Maneja la solicitud de envío de datos
     */
    public static function handleRequest() {
        // Obtener datos de autenticación
        $authData = self::getAuthData();
        
        // Obtener parámetros de la URL
        $urlParams = self::getUrlParams();
        
        // Verificar si se envió un archivo
        if (isset($_FILES['File'])) {
            // Procesar el archivo y preparar los datos
            $file = $_FILES['File'];
            $tmpName = $file['tmp_name'];
            $options = array(
                'head' => $_POST['head'],
                'delim' => $_POST['delim'],
                'enclos' => $_POST['enclos'],
                'len' => $_POST['len'],
                'charset_source' => $_POST['charset_source'],
            );
            $preparedData = inc_importer_csv_dist($tmpName, $options);
        } else {
            // Obtener los datos del cuerpo de la solicitud
            $preparedData = json_decode(file_get_contents('php://input'), true);
        }
        
        // Verificar la autenticación del usuario
        $db = new DbHandler();
        $query = "SELECT * FROM api_auteurs WHERE login = ?";
        $resultado = $db->metodoGet($query, array($authData['var_login']));
        
        if (!empty($resultado)) {
            // Verificar la contraseña
            if (verificarPassword($authData['password'], $resultado[0]['pass'], $resultado[0]['alea_actuel'])) {
                // Enviar los datos
                $authDatasend = ['var_login' => $resultado[0]['login'], 'password' => $authData['password'], 'email' => $resultado[0]['email']];
                echo makeCurlRequest($urlParams, $authDatasend);
            } else {
                // Devolver error de autenticación
                $records['data'] = array('status' => '401');
                echo json_encode($records);
                exit;
            }
        }
    }

    /**
     * Obtiene los datos de autenticación de la solicitud
     * @return array Datos de autenticación
     */
    private static function getAuthData() {
        // Obtener encabezados de autenticación
        $headers = getallheaders();
        $headerKeys = array_change_key_case($headers, CASE_UPPER);
        
        // Verificar la presencia de los encabezados de autenticación
        if (empty($headerKeys['X-SICES-API-APIKEY']) || empty($headerKeys['X-SICES-API-APITOKEN'])) {
            // Devolver error de autenticación
            error_log("Headers recibidos: " . print_r($headers, true));
            echo json_encode(['error' => 'Missing or invalid headers']);
            exit;
        }
        
        // Decodificar los encabezados de autenticación
        $encryptedData = base64_decode($headerKeys['X-SICES-API-APIKEY']);
        $secretKey = base64_decode($headerKeys['X-SICES-API-APITOKEN']);
        $var_login = base64_decode($headerKeys['X-SICES-API-USER']);
        $password = obtenerPass($encryptedData, $secretKey);
        
        // Retornar los datos de autenticación
        return [
            'var_login' => $var_login,
            'password' => $password
        ];
    }

    /**
     * Obtiene los parámetros de la URL de la solicitud
     * @return array Parámetros de la URL
     */
    private static function getUrlParams() {
        // Verificar si se debe aplicar la codificación Base64
        $applyBase64 = false;
        if (isset($_GET['header']) && $_GET['header'] === 'true') {
            $applyBase64 = true;
        } elseif (isset($_POST['header']) && $_POST['header'] === 'true') {
            $applyBase64 = true;
        }
        
        // Obtener parámetros de la URL
        if ($applyBase64) {
            $urlParams = [];
            foreach ($_REQUEST as $key => $value) {
                if ($key !== 'header') {
                    $urlParams[$key] = base64_encode($value);
                }
            }
        } else {
            $urlParams = $_REQUEST;
        }
        
        return $urlParams;
    }
}
