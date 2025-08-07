# ApiSena

La siguiente documentación proporciona una guía detallada para implementar una API en SPIP utilizando PHP y MySQL. A continuación, se presenta una descripción general de cómo implementar la API:

### 'Requisitos'

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
(1, 'evaluasoft', '', 'evaluasoft@gmail.com', '', '', 'C07B51296CED3DCA0356FB4F36B19B07696B8041D2E64A561F0E68F67C77F237', 'cf1e56c48044cc9daa88052a7ae8ea183dcbc7e1718e16401b7298571a774151', '', '0minirezo', 'non', '2025-08-06 20:39:03', '', '$1$YotSntng$Hj8FVrzeEdGDNQwFl4mu9/', '2025-08-06 15:39:03', '2220794496893bd67e06b87.22256236', '12268945846893bd67e0a363.82051712', '0minirezo', '3921839146845b1cdce3b38.02989916', 'spip', 'es', 'oui', 'Administrador', 'cb_1', '', 'Activo', '51793c7af7043d16ae275694ce39c9070b878f8651211aa2197f06cc1fa86680', '', NULL),

