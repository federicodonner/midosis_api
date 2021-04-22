<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuevle un solo usuario
$app->post('/api/cambiopassword', function (Request $request, Response $response) {
    $actual = $request->getParam('actual');
    $nueva = $request->getParam('nueva');

    if (!$actual || !$nueva) {
        return messageResponse($response, 'Debe indicar la contraseña actual y la nueva', 403);
    }

    // El id del usuario logueado viene del middleware authentication
    $usuario_id = $request->getAttribute('usuario_id');
    $sql = "SELECT * FROM usuario WHERE id = $usuario_id";

    try {
        $db = new db();
        $db = $db->connect();

        $stmt = $db->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Si no hay ningún usuario con ese nombre
        if ($usuarios == null) {
            //cambio el estatus del mensaje e incluyo el mensaje de error
            $db = null;
            return messageResponse($response, 'Ocurrió un error con tu usuario, ponte en contacto con el administrador.', 401);
        }

        // Verifica el password contra el hash
        if (!password_verify($actual, $usuarios[0]->pass_hash)) {
            return messageResponse($response, 'La contraseña ingresada no es correcta.', 403);
        }

        // Si estoy acá es porque la contraseña actual es correcta
        // Genera el hash en base a la nueva contraseña
        $password_hash = password_hash($nueva, PASSWORD_BCRYPT);
        $sql = "UPDATE usuario SET pass_hash = :password_hash WHERE id = $usuario_id";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->execute();

        return messageResponse($response, 'Contraseña actualizada correctamente', 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);
