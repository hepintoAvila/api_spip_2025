# API SPIP 2025

API SPIP 2025 es una API RESTful para interactuar con el sistema de gestión de contenido SPIP.

## Requisitos

* PHP 7.4 o superior
* SPIP 3.2 o superior

## Instalación

1. Clona el repositorio: `git clone https://github.com/hepintoAvila/api_spip_2025.git`
2. Instala las dependencias: `composer install`
3. Configura la base de datos en el archivo `config.php`

## Endpoints

### Autenticación

* **POST /api/auth**: Autentica un usuario y devuelve un token de acceso.
	+ Parámetros: `username`, `password`
	+ Respuesta: `token`

### Artículos

* **GET /api/articles**: Devuelve una lista de artículos.
	+ Parámetros: `limit`, `offset`
	+ Respuesta: `array de artículos`
* **GET /api/articles/{id}**: Devuelve un artículo específico.
	+ Parámetros: `id`
	+ Respuesta: `artículo`
* **POST /api/articles**: Crea un nuevo artículo.
	+ Parámetros: `title`, `content`
	+ Respuesta: `artículo creado`
* **PUT /api/articles/{id}**: Actualiza un artículo existente.
	+ Parámetros: `id`, `title`, `content`
	+ Respuesta: `artículo actualizado`
* **DELETE /api/articles/{id}**: Elimina un artículo.
	+ Parámetros: `id`
	+ Respuesta: `artículo eliminado`

## API Key

La API utiliza una clave de API para autenticar las solicitudes. Puedes obtener una clave de API mediante el endpoint `/api/auth`.

## Ejemplos

* **Autenticación**: `curl -X POST -H "Content-Type: application/json" -d '{"username": "tu_usuario", "password": "tu_contraseña"}' http://localhost/api_spip_2025/api/auth`
* **Listar artículos**: `curl -X GET -H "Authorization: Bearer tu_token" http://localhost/api_spip_2025/api/articles`

## Contribuir

Si deseas contribuir al proyecto, por favor, crea un fork del repositorio y envía un pull request con tus cambios.

## Licencia

Este proyecto está bajo la licencia MIT.
