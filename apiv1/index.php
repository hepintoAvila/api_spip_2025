<?php
// index.php
/**
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2022
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo
 **/
require_once 'vendor/autoload.php';
use App\Controllers\EnviarController;
use App\Routing\Router;
$router = new Router();
$enviarController = new EnviarController($auth);

$router->handleRequest();
?>
