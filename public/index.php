<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../src/config/db.php';
require '../src/auxiliares/funciones.php';



$app = new \Slim\App;



// Customer routes
require '../src/routes/drogaxdosis.php';
require '../src/routes/pastillero.php';
// require '../src/routes/medicina.php';
require '../src/routes/droga.php';
require '../src/routes/compra.php';
require '../src/routes/stock.php';
require '../src/routes/armarpastillero.php';
require '../src/routes/usuario.php';
require '../src/routes/oauth.php';
require '../src/routes/verificarlogin.php';
require '../src/routes/cors.php';


$app->run();

// echo('hla');
