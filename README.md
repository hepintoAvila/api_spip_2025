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

### Administrador de usuarios


* **POST /api2025/admin_login**: Administrador de login.
	+ Parámetros: `accion`, `opcion`, `exec`, `_SPIP_PAGE`, `action`, `var_ajax`
	+ Respuesta: `array de Auth`
`{
    "status": 200,
    "type": "success",
    "message": "ok",
    "data": {
        "Roles": {
            "Nom": "xxxxx",
            "Email": "xxxxx@gmail.com",
            "rol": "Administrador",
            "AppKey": "xxx"
        },
        "Menus": [],
        "Permisos" [],
	}
 }`
* **POST /api2025/admin_roles**: Administrador de roles.
	+ Parámetros: `accion`, `opcion`, `exec`, `_SPIP_PAGE`, `action`, `var_ajax`
	+ Respuesta: `{
    "status": 200,
    "type": "success",
    "message": "ok",
    "data": {
        "Roles": {
                "idRol": "111",
                "tipo": "Hemerotecass",
                "status": "Activo"
            },
	}
 }`
* **POST /api2025/admin_menu**: Administrador de menus.
	+ Parámetros: `accion`, `opcion`, `exec`, `_SPIP_PAGE`, `action`, `var_ajax`
	+ Respuesta: `{
    "status": 200,
    "type": "success",
    "message": "ok",
  "data": {
        "Menus": [
            {
                "idMenu": "13",
                "key": "hemeroteca",
                "label": "Hemeroteca",
                "isTitle": "0",
                "icon": "mdi mdi-calendar-month",
                "status": "Active"
            },
   		]
 }}`
* **POST /api2025/admin_usuarios**: Administrador de usuarios.
	+ Parámetros: `accion`, `opcion`, `exec`, `_SPIP_PAGE`, `action`, `var_ajax`
	+ Respuesta: `{
    "status": 200,
    "type": "success",
    "message": "ok",
  "data": {
        "usuarios": [
            {
                "id_auteur": "3535",
                "nombres": "770257507",
                "email": "prueba2222@unicesar.edu.co",
                "login": "",
                "tipo": "Administrador"
            },
   		]
 }}`
* **POST /api2025/admin_permisos**: Administrador de Permisos.
	+ Parámetros: `accion`, `opcion`, `exec`, `_SPIP_PAGE`, `action`, `var_ajax`
	+ Respuesta: `{
    "status": 200,
    "type": "success",
    "message": "ok",
  "data": {
        "Permisos": [
            {
            "query": "S",
            "add": "S",
            "update": "S",
            "delete": "S",
            "id_autorizacion": "1",
            "menu": "Evaluacion",
            "submenu": "Evaluaciones"
            },
   		]
 }}`
## API Key

La API utiliza una clave de API para autenticar las solicitudes. Puedes obtener una clave de API mediante el endpoint `/api/auth`.

## Ejemplos

* **Autenticación**: `curl -X POST -H "Content-Type: application/json" -d '{"username": "tu_usuario", "password": "tu_contraseña"}' http://localhost/api_spip_2025/api/auth`
* **Listar permisos**: `curl --location --request POST 'https://lacasadelbarbero.com.co/api2025/?exec=admin_permisos&opcion=Y29uc3VsdGFyX3JvbGVzX3VzdWFyaW9z&accion=YWRtaW5fcGVybWlzb3M%3D&bonjour=oui&_SPIP_PAGE=admin_permisos&action=true&var_ajax=form' \
--header 'x-sices-api-apikey: 08va1GH/RDOsQ3EObGye2A==: :gAC0hV0zWcBEQRtYFr+o4A==' \
--header 'Authorization: Basic am90YXQwMDdAZ21haWwuY29tOmpvdGF0MDA3' \
--header 'Cookie: spip_admin=%40jotat007%40gmail.com; spip_session=81_74d6554f2f0aca7a41bb3fcdbb0fb36e'

## Contribuir

Si deseas contribuir al proyecto, por favor, crea un fork del repositorio y envía un pull request con tus cambios.

## Licencia

Este proyecto está bajo la licencia MIT.
