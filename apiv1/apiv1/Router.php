<?php

/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/ 

class Router {
    private $routes = [];
    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $headers = getallheaders();
        $headersPosman = isset($headers['header']) ? $headers['header'] : null;
    
        if ($headersPosman) {
            $path = isset($_GET['accion']) ? $_GET['accion'] : '';
        } else {
            $path = isset($_GET['accion']) ? base64_decode($_GET['accion']) : '';
        }
    
        // Depuración
        /*
        echo "Valor de \$path: $path\n";
        echo "Valor de \$method: $method\n";
        echo "Valor de \$headersPosman: $headersPosman\n";
        */
        foreach ($this->routes as $route) {
           // echo "Ruta actual: \n";
           // print_r($route);
           // echo "\n";
    
            if ($route['method'] === $method && $route['path'] === $path) {
                //echo "Ruta encontrada: $path\n";
                call_user_func($route['handler']);
                return;
            }
        }
    
        // If no route matched, return 404
        header("HTTP/1.0 404 Not Found");
        echo "Acción no reconocida: '".$path."'";
        //print_r($this->routes);
    }
}
?>
