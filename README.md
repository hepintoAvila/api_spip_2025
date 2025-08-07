# API SPIP 2025

## Descripción

La siguiente documentación proporciona una guía detallada para implementar una API en SPIP utilizando PHP y MySQL. A continuación, se presenta una descripción general de cómo implementar la API.

## Requisitos

- **SPIP** (Système de Publication pour l'Internet Partagé)
- **PHP 7.x** o superior
- **MySQL 5.x** o superior
- Conocimientos básicos de PHP, MySQL y SPIP

## Estructura de la API

La API se estructurará de la siguiente manera:

- **DbHandler**: Clase que maneja la conexión a la base de datos MySQL y proporciona métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar).
- **EnviarController**: Clase que maneja las solicitudes de envío de datos a la API y proporciona métodos para validar la autenticación y autorización de los usuarios.
- **AuthorController**: Clase que maneja la autenticación y autorización de los usuarios y proporciona métodos para validar la autenticación y autorización.

## Endpoints

La API tendrá los siguientes endpoints:

- `/api/usuarios`: Endpoint para obtener la lista de autores.
- `/api/auteur`: Endpoint para obtener la información de inicio de sesión de un autor específico.
- `/api/roles`: Endpoint para obtener los roles.
- `/api/menu`: Endpoint para obtener el menú asignado.

## Seguridad

La API utilizará autenticación y autorización para garantizar la seguridad de los datos.  
La autenticación se realizará mediante tokens de acceso y la autorización se realizará mediante roles y permisos.

## Ventajas

La implementación de esta API en SPIP proporcionará las siguientes ventajas:

- **Acceso a los datos**: La API proporcionará acceso a los datos de la base de datos de SPIP de manera segura y controlada.
- **Flexibilidad**: La API permitirá la integración con otras aplicaciones y servicios.
- **Escalabilidad**: La API estará diseñada para escalar según las necesidades de la aplicación.

## Envío de las variables desde el frontend

En la siguiente función se envían los datos a la API del SPIP.  
**Nota**: la URL debe apuntar a:  
`url: "https://www.tu-hosting/api_spip_2025/apiv1/?"`

### JavaScript

```javascript
const config = {
    API_URL_WEB: "https://www.tu-hosting/api_spip_2025/",
    API_URL: "https://www.tu-hosting/api_spip_2025/api2024/apiv1/?",
    API_URL_AUTH: '/dashboard/tarjetas',
    API_ACCION_AUTH: 'auteur',
    API_OPCION_AUTH: 'login',
    API_ACCION_ROLES: 'roles',
    API_ACCION_USUARIOS: 'usuarios',
    X_SICES_API_APIKEY: 'zHqroBk5BILvT9Bdajol1A==::snR8ET+DxazKVH5Ywxx1Fg==',
    X_SICES_API_APITOKEN: 'f37ef65fbc641054a3b508af0b52220916ff40eefa9a0a722b4d69cda96ef064',
    X_SICES_API_APIKEY_USER: 'U9YbCFRmbhm9GjlHTAuKvgipgvku1ljhR9YbTkpMNtXmhMa/tsvlZS2eCOtGqe94vNJ+QDtM23pRQxRytrZR3TtXyPbozsxT4q0E/z/hY5o=::1hFXGPLErNPJxf8FuXdeLA==',
    X_SICES_API_APITOKEN_USER: '1eddeff45f94f4ab39b8c1f0ee840ed296b606608c3af799787f2dafa9e64320',
    X_SICES_API_USER: 'X_SICES_API_USER un usuario especial de la tabla api_auteurs',
    X_SICES_API_PASS: 'X_SICES_API_PASS el pass del usuario especial de la tabla api_auteurs',
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
```

## Estructura de la tabla `api_auteurs`

### SQL

```sql
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
```

### Datos para la tabla `api_auteurs`

```sql
INSERT INTO `api_auteurs` (`id_auteur`, `nom`, `bio`, `email`, `nom_site`, `url_site`, `login`, `pass`, `low_sec`, `statut`, `webmestre`, `maj`, `pgp`, `htpass`, `en_ligne`, `alea_actuel`, `alea_futur`, `prefs`, `cookie_oubli`, `source`, `lang`, `imessage`, `tipo`, `entidad`, `id_rol`, `status`, `clave`, `tipoEntrevista`, `id_aspirante`) VALUES
(1, 'evaluasoft', '', 'evaluasoft@gmail.com', '', '', 'X_SICES_API_USER', 'X_SICES_API_PASS', '', '0minirezo', 'non', '2025-08-06 20:39:03', '', '', '2025-08-06 15:39:03', '', '', '0minirezo', '', 'spip', 'es', 'oui', 'Administrador', 'cb_1', '', 'Activo', '', '', NULL);
```

## Documentación de la Clase DbHandler

### Descripción

La clase DbHandler es una extensión de la clase PDO que proporciona métodos para interactuar con una base de datos MySQL. Proporciona métodos para realizar operaciones CRUD (Crear, Leer, Actualizar, Eliminar) y validar usuarios.

### Métodos

- `__construct()`: Constructor de la clase que inicializa la conexión a la base de datos.
- `validarUsuario($login)`: Valida si un usuario existe en la base de datos.
- `metodoGet($query, $datos)`: Realiza una consulta SELECT a la base de datos.
- `metodoSelect($query)`: Realiza una consulta SELECT a la base de datos sin parámetros.
- `metodoInsert($query, $datos)`: Realiza una consulta INSERT a la base de datos.
- `metodoUpdate($query, $datos)`: Realiza una consulta UPDATE a la base de datos.
- `metodoDelete($query, $datos)`: Realiza una consulta DELETE a la base de datos.

## Documentación de la Clase EnviarController

### Descripción

La clase EnviarController es responsable de manejar solicitudes de envío de datos a través de una API. Proporciona métodos para obtener datos de autenticación, parámetros de la URL y manejar solicitudes de envío de archivos.

### Métodos

- `handleRequest()`: Maneja la solicitud de envío de datos y verifica la autenticación del usuario.
- `getAuthData()`: Obtiene los datos de autenticación de la solicitud.
- `getUrlParams()`: Obtiene los parámetros de la URL de la solicitud.

## Documentación de la Clase AuthorController

### Descripción

La clase AuthorController es responsable de manejar la autenticación y autorización de usuarios en una aplicación API. Proporciona métodos para registrar intentos fallidos de inicio de sesión, obtener el número de intentos fallidos por IP o usuario, y manejar solicitudes de autenticación.

### Métodos

- `registrarIntentoFallido($login, $ip)`: Registra un intento fallido de inicio de sesión en la base de datos.
- `obtenerIntentosFallidosPorIp($ip)`: Obtiene el número de intentos fallidos de inicio de sesión desde una dirección IP específica en los últimos 30 minutos.
- `obtenerIntentosFallidos($login)`: Obtiene el número de intentos fallidos de inicio de sesión para un usuario específico en los últimos 30 minutos.
- `incrementarIntentosFallidos($login, $db)`: Incrementa el contador de intentos fallidos de inicio de sesión para un usuario específico.
- `obtenerTiempoBloqueo($login)`: Obtiene el tiempo de bloqueo para un usuario específico.
- `actualizarTiempoBloqueo($login, $tiempoBloqueo)`: Actualiza el tiempo de bloqueo para un usuario específico.
- `reiniciarIntentosFallidos($login)`: Reinicia el contador de intentos fallidos de inicio de sesión para un usuario específico.
- `handleRequest()`: Maneja la solicitud de autenticación y autorización de un usuario.
# Documentación de la Clase EnviarController

## Descripción

La clase **EnviarController** es responsable de manejar solicitudes de envío de datos a través de una API. Proporciona métodos para obtener datos de autenticación, parámetros de la URL y manejar solicitudes de envío de archivos.

## Métodos

### handleRequest()
- Maneja la solicitud de envío de datos y verifica la autenticación del usuario.
- Obtiene los datos de autenticación y los parámetros de la URL.
- Si se envía un archivo, lo procesa y prepara los datos para su envío.
- Si no se envía un archivo, obtiene los datos del cuerpo de la solicitud.
- Verifica la autenticación del usuario y envía los datos si es válida.

### getAuthData()
- Obtiene los datos de autenticación de la solicitud.
- Verifica la presencia de los encabezados de autenticación y los decodifica si es necesario.
- Retorna un arreglo con los datos de autenticación.

### getUrlParams()
- Obtiene los parámetros de la URL de la solicitud.
- Verifica si se debe aplicar la codificación Base64 a los parámetros.
- Retorna un arreglo con los parámetros de la URL.

## Funcionamiento

1. La clase **EnviarController** utiliza la clase **DbHandler** para interactuar con la base de datos.
2. El método `handleRequest` maneja la solicitud de envío de datos y verifica la autenticación del usuario.
3. Si la autenticación es válida, envía los datos utilizando la función `makeCurlRequest`.

```php
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
```

# Documentación de la Clase AuthorController

## Descripción

La clase **AuthorController** es responsable de manejar la autenticación y autorización de usuarios en una aplicación API. Proporciona métodos para registrar intentos fallidos de inicio de sesión, obtener el número de intentos fallidos por IP o usuario, y manejar solicitudes de autenticación.

## Métodos

### registrarIntentoFallido($login, $ip)
Registra un intento fallido de inicio de sesión en la base de datos.

- **Parámetros:**
  - `$login`: El nombre de usuario que intentó iniciar sesión.
  - `$ip`: La dirección IP del usuario que intentó iniciar sesión.

### obtenerIntentosFallidosPorIp($ip)
Obtiene el número de intentos fallidos de inicio de sesión desde una dirección IP específica en los últimos 30 minutos.

- **Parámetros:**
  - `$ip`: La dirección IP para la cual se desean obtener los intentos fallidos.
- **Retorno:** El número de intentos fallidos.

### obtenerIntentosFallidos($login)
Obtiene el número de intentos fallidos de inicio de sesión para un usuario específico en los últimos 30 minutos.

- **Parámetros:**
  - `$login`: El nombre de usuario para el cual se desean obtener los intentos fallidos.
- **Retorno:** El número de intentos fallidos.

### incrementarIntentosFallidos($login, $db)
Incrementa el contador de intentos fallidos de inicio de sesión para un usuario específico.

- **Parámetros:**
  - `$login`: El nombre de usuario para el cual se desea incrementar el contador de intentos fallidos.
  - `$db`: La conexión a la base de datos.

### obtenerTiempoBloqueo($login)
Obtiene el tiempo de bloqueo para un usuario específico.

- **Parámetros:**
  - `$login`: El nombre de usuario para el cual se desea obtener el tiempo de bloqueo.
- **Retorno:** El tiempo de bloqueo en segundos.

### actualizarTiempoBloqueo($login, $tiempoBloqueo)
Actualiza el tiempo de bloqueo para un usuario específico.

- **Parámetros:**
  - `$login`: El nombre de usuario para el cual se desea actualizar el tiempo de bloqueo.
  - `$tiempoBloqueo`: El nuevo tiempo de bloqueo en segundos.

### reiniciarIntentosFallidos($login)
Reinicia el contador de intentos fallidos de inicio de sesión para un usuario específico.

- **Parámetros:**
  - `$login`: El nombre de usuario para el cual se desea reiniciar el contador de intentos fallidos.

### handleRequest()
Maneja la solicitud de autenticación y autorización de un usuario.

- Verifica el número de intentos fallidos y bloquea la cuenta si es necesario.
- Autentica al usuario y devuelve un token de acceso si la autenticación es exitosa.

## Funcionamiento

1. La clase **AuthorController** utiliza la clase **DbHandler** para interactuar con la base de datos.
2. El método `handleRequest` maneja la solicitud de autenticación y autorización de un usuario.
3. Verifica el número de intentos fallidos y bloquea la cuenta si es necesario.
4. Autentica al usuario y devuelve un token de acceso si la autenticación es exitosa.

## Código con Comentarios

```php
class AuthorController {
    /**
    * Registra un intento fallido de inicio de sesión
    * @param string $login El nombre de usuario que intentó iniciar sesión
    * @param string $ip La dirección IP del usuario que intentó iniciar sesión
    */
    public static function registrarIntentoFallido($login, $ip) {
        $db = new DbHandler();
        $query = "INSERT INTO api_intentos_fallidos_login (login, ip) VALUES (?, ?)";
        $db->metodoInsert($query, array($login, $ip));
    }

    /**
    * Obtiene el número de intentos fallidos de inicio de sesión desde una dirección IP específica
    * @param string $ip La dirección IP para la cual se desean obtener los intentos fallidos
    * @return int El número de intentos fallidos
    */
    public static function obtenerIntentosFallidosPorIp($ip) {
        $db = new DbHandler();
        $query = "SELECT COUNT(*) AS intentos_fallidos FROM api_intentos_fallidos_login WHERE ip = ? AND timestamp > NOW() - INTERVAL 30 MINUTE";
        $resultado = $db->metodoGet($query, array($ip));

        if (!empty($resultado)) {
            return $resultado[0]['intentos_fallidos'];
        } else {
            return 0;
        }
    }

    // ... otros métodos ...
}
```

## Ejemplo de Uso

```php
$authorController = new AuthorController();
$authorController::handleRequest();
```
