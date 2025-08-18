<?php
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
        if (!function_exists('getallheaders')) {
        function getallheaders() {
            $headers = [];
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    $header = str_replace('HTTP_', '', $key);
                    $header = str_replace('_', '-', $header);
                    $header = ucwords(strtolower($header));
                    $headers[$header] = $value;
                }
            }
                return $headers;
            }
        }
        $headers = getallheaders();
 // Obtener la acción
		$path = null;
		if (isset($_GET['accion'])) {
			$path = $_GET['accion'];
		} elseif ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
			$input = json_decode(file_get_contents("php://input"), true);
			if (isset($input['accion'])) {
				$path = $input['accion'];
			}
		} 
	 // Decodificar la acción si es necesario
		if ($path) {
			$path = base64_decode($path, 7);
		}		
		 /*
		$requestUri = $_SERVER['REQUEST_URI'];
		$queryString = parse_url($requestUri, PHP_URL_QUERY);
		parse_str($queryString, $params);
		$path = isset($params['accion']) ? $params['accion'] : null;
    
        // Depuración
          */
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
