<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../src/config/db.php';

$app = new \Slim\App;

// Customer routes
// require '../src/routes/libros.php';
// require '../src/routes/reviews.php';
// require '../src/routes/usuarios.php';
// require '../src/routes/empresas.php';
// require '../src/routes/alquileres.php';
// require '../src/routes/mail.php';
// require '../src/routes/oauth.php';
// require '../src/routes/yo.php';
require '../src/routes/pastillero.php';
require '../src/routes/medicina.php';
require '../src/routes/droga.php';
require '../src/routes/cors.php';


$app->run();

// echo('hla');
