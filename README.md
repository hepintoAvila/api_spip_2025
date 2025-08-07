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

