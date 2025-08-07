<?php
// Router.php
/**
 * @About:      API Interface
 * @File:       Router.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/
 
class Router {
    /**
     * Arreglo de rutas
     * @var array
     */
    private $routes = [];

    /**
     * Agrega una nueva ruta a la lista de rutas
     * @param string $method  Método HTTP de la ruta
     * @param string $path    Ruta URL que se va a manejar
     * @param callable $handler Función que se ejecutará cuando se acceda a la ruta
     */
    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * Maneja la solicitud HTTP actual y busca una ruta coincidente en la lista de rutas
     */
    public function handleRequest() {
        // Obtiene el método HTTP actual
        $method = $_SERVER['REQUEST_METHOD'];

        // Obtiene los encabezados HTTP
        $headers = getallheaders();

        // Verifica si se ha enviado un encabezado personalizado
        $headersPosman = isset($headers['header']) ? $headers['header'] : null;

        // Obtiene la ruta URL actual
        if ($headersPosman) {
            $path = isset($_GET['accion']) ? $_GET['accion'] : '';
        } else {
            $path = isset($_GET['accion']) ? base64_decode($_GET['accion']) : '';
        }

        // Busca una ruta coincidente en la lista de rutas
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                // Ejecuta la función asociada a la ruta coincidente
                call_user_func($route['handler']);
                return;
            }
        }

        // Si no se encuentra ninguna ruta coincidente, devuelve un error 404
        header("HTTP/1.0 404 Not Found");
        echo "Acción no reconocida: '".$path."'";
    }
}
?>
