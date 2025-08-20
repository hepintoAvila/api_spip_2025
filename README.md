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

* **POST /api2025/admin_login**: Autentica un usuario y devuelve un token de acceso.
	+ Parámetros: `username`, `password`
	+ Respuesta: `AppKey`,`Email`,`Nom`,`rol`

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

## Endpoint Description

This endpoint allows the administrator to manage permissions within the application. It is executed via an HTTP POST request, and it is specifically designed to handle various operations related to user roles and permissions.

### Request Parameters

The request includes the following parameters:

- `exec`: Specifies the execution context, in this case, `admin_permisos`.
    
- `opcion`: Encodes the operation type, which is related to user role management.
    
- `accion`: Indicates the action to be performed, here it is set to `admin_permisos`.
    
- `bonjour`: A custom parameter that is set to `oui`.
    
- `_SPIP_PAGE`: The page context for the request, which is `admin_permisos`.
    
- `action`: A boolean flag indicating whether the action should be executed.
    
- `var_ajax`: Indicates that the request is an AJAX call, with the value `form`.
    

### Response Structure

Upon a successful request, the server responds with a status code of `200` and a `Content-Type` of `text/html`. The response body contains a JSON object with the following structure:

``` json
{
  "Permisos": [
    {
      "query": "",
      "add": "",
      "update": "",
      "delete": "",
      "id_autorizacion": "",
      "menu": "",
      "submenu": ""
    }
  ]
}

 ```

### Response Fields

- `Permisos`: An array containing permission objects. Each object may include the following fields:
    
    - `query`: Represents the query associated with the permissions.
        
    - `add`: Indicates whether the add permission is granted.
        
    - `update`: Indicates whether the update permission is granted.
        
    - `delete`: Indicates whether the delete permission is granted.
        
    - `id_autorizacion`: The identifier for the authorization.
        
    - `menu`: The main menu associated with the permissions.
        
    - `submenu`: The submenu associated with the permissions.
        

### Usage of Response Fields

The fields returned in the `Permisos` array can be utilized in subsequent requests to determine the specific permissions available to a user. For example:

- Use the `add`, `update`, and `delete` fields to check if the user has the necessary permissions to perform these actions on resources.
    
- The `id_autorizacion` can be referenced in other API calls that require authorization checks.
    

This structured response allows for dynamic permission handling based on the user's role, enabling a more secure and flexible application environment.

## Endpoint

**POST** `{{urlEvaluaSoft}}?exec=admin_permisos&opcion=Y29uc3VsdGFyX3JvbGVzX3VzdWFyaW9z&accion=YWRtaW5fcGVybWlzb3M=&bonjour=oui&_SPIP_PAGE=admin_permisos&action=true&var_ajax=form`

## Description

This endpoint is used to manage permissions within the application. It allows administrators to perform various operations related to user roles and permissions.

## Request Parameters

The following query parameters are expected in the request:

- **exec**: Specifies the execution context (e.g., `admin_permisos`).
    
- **opcion**: Encodes the option for user roles (e.g., `Y29uc3VsdGFyX3JvbGVzX3VzdWFyaW9z`).
    
- **accion**: Encodes the action to be performed (e.g., `YWRtaW5fcGVybWlzb3M=`).
    
- **bonjour**: A flag parameter (e.g., `oui`).
    
- **_SPIP_PAGE**: Indicates the specific admin page (e.g., `admin_permisos`).
    
- **action**: A boolean flag indicating the action status (e.g., `true`).
    
- **var_ajax**: Indicates that the request is an AJAX call (e.g., `form`).
    

## Response

Upon a successful request, the server responds with a status code of `200` and a content type of `text/html`. The response body includes a JSON object containing permissions information:

``` json
{
  "Permisos": [
    {
      "query": "",
      "add": "",
      "update": "",
      "delete": "",
      "id_autorizacion": "",
      "menu": "",
      "submenu": ""
    }
  ]
}

 ```

### Response Fields

- **Permisos**: An array of permission objects, each containing:
    
    - **query**: The query related to the permission.
        
    - **add**: The permission to add.
        
    - **update**: The permission to update.
        
    - **delete**: The permission to delete.
        
    - **id_autorizacion**: The authorization ID.
        
    - **menu**: The associated menu.
        
    - **submenu**: The associated submenu.
        

## Notes

- Ensure that the required parameters are correctly encoded and included in the request.
    
- The response structure may vary based on the permissions defined in the system.
    
- This endpoint is intended for use by administrators only.
    

## Example

Here is an example of a request to this endpoint:

``` http
POST {{urlEvaluaSoft}}?exec=admin_permisos&opcion=Y29uc3VsdGFyX3JvbGVzX3VzdWFyaW9z&accion=YWRtaW5fcGVybWlzb3M=&bonjour=oui&_SPIP_PAGE=admin_permisos&action=true&var_ajax=form

 ```

The expected response would be a JSON object with permission details as outlined above.

This endpoint is used to manage permissions for users in the application. It allows the administrator to perform various operations related to user roles and permissions.

### Request

**Method:** POST  
**URL:** `{{urlEvaluaSoft}}?exec=admin_permisos&opcion=Y29uc3VsdGFyX3JvbGVzX3VzdWFyaW9z&accion=YWRtaW5fcGVybWlzb3M=&bonjour=oui&_SPIP_PAGE=admin_permisos&action=true&var_ajax=form`

#### Query Parameters

- `exec`: Specifies the execution context, in this case, `admin_permisos`.
    
- `opcion`: Encoded string representing the operation type, here it indicates user roles management.
    
- `accion`: Encoded string indicating the action to be performed, which is related to admin permissions.
    
- `bonjour`: A parameter that may indicate a greeting or a session check.
    
- `_SPIP_PAGE`: Specifies the page context, here it is `admin_permisos`.
    
- `action`: A boolean parameter indicating whether the action is to be executed.
    
- `var_ajax`: Indicates that the request is made via AJAX, with the value `form`.
    

### Expected Response

Upon a successful request, the server will respond with a JSON object containing the permissions structure.

#### Response Structure

``` json
{
  "Permisos": [
    {
      "query": "",
      "add": "",
      "update": "",
      "delete": "",
      "id_autorizacion": "",
      "menu": "",
      "submenu": ""
    }
  ]
}

 ```

- `Permisos`: An array containing permission objects.
    
    - `query`: A string that may hold the query related to permissions.
        
    - `add`: A string indicating permission to add.
        
    - `update`: A string indicating permission to update.
        
    - `delete`: A string indicating permission to delete.
        
    - `id_autorizacion`: A string representing the authorization ID.
        
    - `menu`: A string representing the main menu associated with the permissions.
        
    - `submenu`: A string representing any sub-menu associated with the permissions.
        

### Example Request

``` http
POST {{urlEvaluaSoft}}?exec=admin_permisos&opcion=Y29uc3VsdGFyX3JvbGVzX3VzdWFyaW9z&accion=YWRtaW5fcGVybWlzb3M=&bonjour=oui&_SPIP_PAGE=admin_permisos&action=true&var_ajax=form

 ```

### Example Response

``` json
{
  "Permisos": [
    {
      "query": "",
      "add": "",
      "update": "",
      "delete": "",
      "id_autorizacion": "",
      "menu": "",
      "submenu": ""
    }
  ]
}

 ```

This endpoint is crucial for managing user roles and ensuring that permissions are correctly assigned within the application.
## API Key

La API utiliza una clave de API para autenticar las solicitudes. Puedes obtener una clave de API mediante el endpoint `/api2025/admin_login`.

## Ejemplos

* **Autenticación**: `curl -X POST -H "Content-Type: application/json" -d '{"username": "tu_usuario", "password": "tu_contraseña"}' http://localhost/api2025/`
* **Listar permisos**: `curl --location --request POST 'http://localhost/api2025/?exec=admin_permisos&opcion=Y29uc3VsdGFyX3JvbGVzX3VzdWFyaW9z&accion=YWRtaW5fcGVybWlzb3M%3D&bonjour=oui&_SPIP_PAGE=admin_permisos&action=true&var_ajax=form' \
--header 'x-sices-api-apikey: 08va1GH/RDOsQ3EObGye2A==: :gAC0hV0zWcBEQRtYFr+o4A==' \
--header 'Authorization: Basic am90YXQwMDdAZ21haWwuY29tOmpvdGF0MDA3' \
--header 'Cookie: spip_admin=%40jotat007%40gmail.com; spip_session=81_74d6554f2f0aca7a41bb3fcdbb0fb36e'

## Endpoint Description

This endpoint is used to execute a specific action within the administrative menu of the application. It allows the user to perform operations related to menu management through an HTTP POST request.

### Request Parameters

The following parameters must be included in the request:

- **accion** (string): Encoded action to be performed (e.g., `YWRtaW5fbWVudQ==`).
    
- **opcion** (string): Encoded option related to the action (e.g., `Y29uc3VsdGFyX21lbnU=`).
    
- **bonjour** (string): A simple confirmation parameter, typically set to `oui`.
    
- **exec** (string): Indicates the execution context, in this case, `admin_menu`.
    
- **_SPIP_PAGE** (string): The specific page to be processed, here it is `admin_menu`.
    
- **action** (boolean): A flag indicating whether the action should be executed, set to `true`.
    
- **var_ajax** (string): Specifies that the request is an AJAX call, set to `form`.
    

### Expected Response Format

Upon successful execution of the request, the server will respond with a JSON object containing the following structure:

- **status** (integer): Indicates the execution status (0 for success).
    
- **type** (string): Type of response, typically empty in this case.
    
- **data** (object): Contains the following details:
    
    - **Menus** (array): An array of menu objects, each with the following fields:
        
        - **idMenu** (string): Identifier for the menu (currently empty).
            
        - **key** (string): Key associated with the menu (currently empty).
            
        - **label** (string): Label for the menu (currently empty).
            
        - **isTitle** (string): Indicates if the menu is a title (currently empty).
            
        - **icon** (string): Icon associated with the menu (currently empty).
            
        - **status** (string): Status of the menu (currently empty).
            
- **message** (string): Additional message from the server, typically empty.
    

### Additional Notes

- Ensure that the request parameters are correctly encoded as needed.
    
- The response may vary based on the action performed and the current state of the menus.
    
- This endpoint is primarily intended for administrative users and should be used with caution to avoid unintended modifications to the menu structure.
    

This endpoint is designed to handle administrative menu actions within the application. It accepts a POST request with various parameters that dictate the action to be performed.

### Request Parameters

- **accion** (string): Encoded action type, indicating the specific administrative action to be executed.
    
- **opcion** (string): Encoded option parameter that specifies additional context for the action.
    
- **bonjour** (string): A simple acknowledgment parameter, expected to be "oui".
    
- **exec** (string): Specifies the execution context, in this case, "admin_menu".
    
- **_SPIP_PAGE** (string): Indicates the page context for the request, set to "admin_menu".
    
- **action** (boolean): A flag indicating whether the action should be executed.
    
- **var_ajax** (string): Indicates that the request is an AJAX call, set to "form".
    

### Expected Response

Upon a successful request, the server responds with a JSON object containing the following structure:

- **status** (integer): Indicates the success or failure of the request. A value of `0` typically signifies success.
    
- **type** (string): A string that may provide additional information about the response type, though it is empty in this case.
    
- **data** (object): Contains the main data returned by the request.
    
    - **Menus** (array): An array of menu objects, each containing:
        
        - **idMenu** (string): The identifier for the menu (currently empty).
            
        - **key** (string): The key associated with the menu (currently empty).
            
        - **label** (string): The label for the menu (currently empty).
            
        - **isTitle** (string): Indicates if the menu is a title (currently empty).
            
        - **icon** (string): The icon associated with the menu (currently empty).
            
        - **status** (string): The status of the menu (currently empty).
            
- **message** (string): Any additional message from the server, which is empty in this response.
    

### Notes

- Ensure that all parameters are correctly encoded as required by the endpoint.
- The response structure may vary based on the action performed and the data available.
- This endpoint is primarily intended for administrative use and may require appropriate permissions to access.
  
## Contribuir

Si deseas contribuir al proyecto, por favor, crea un fork del repositorio y envía un pull request con tus cambios.

## Licencia

Este proyecto está bajo la licencia MIT.
