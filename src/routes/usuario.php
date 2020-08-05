<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuevle un solo usuario
$app->get('/api/usuario', function (Request $request, Response $response) {
    // Verify if the auth header is available
    if ($request->getHeaders()['HTTP_AUTHORIZATION']) {
        // If the header is available, get the token
        $access_token = $request->getHeaders()['HTTP_AUTHORIZATION'][0];
        $access_token = explode(" ", $access_token)[1];
        // Find the access token, if a user is returned, post the products
        if (!empty($access_token)) {
            $user_found = verifyToken($access_token);
            // Verify that there is a user logged in
            if (!empty($user_found)) {
                // Devuelve el usuario dueño del token
                $id = $user_found[0]->id;
                $sql = "SELECT * FROM usuario WHERE id = $id";

                try {
                    $db = new db();
                    $db = $db->connect();

                    $stmt = $db->query($sql);
                    $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);

                    // Add the users array inside an object
                    if (!empty($usuarios)) {
                        // Delete the password hash for the response
                        unset($usuarios[0]->pass_hash);
                        unset($usuarios[0]->pendiente_cambio_pass);

                        $usuario = $usuarios[0];
                        $db = null;
                        return dataResponse($response, $usuario, 200);
                    } else {
                        return messageResponse($response, 'Id incorrecto', 401);
                    }
                } catch (PDOException $e) {
                    $db = null;
                    return messageResponse($response, $e->getMessage(), 500);
                }
            } else {  // if (!empty($user_found)) {
                $db = null;
                return messageResponse($response, 'Error de login, usuario no encontrado', 401);
            }
        } else { // if (!empty($access_token)) {
            $db = null;
            return messageResponse($response, 'Error de login, falta access token', 401);
        }
    } else { // if ($request->getHeaders()['HTTP_AUTHORIZATION']) {
        $db = null;
        return messageResponse($response, 'Error de encabezado HTTP', 401);
    }
});



// Agrega un profesor
$app->post('/api/usuario', function (Request $request, Response $response) {

    // Get the user's details from the request body
    $nombre = $request->getParam('nombre');
    $apellido = $request->getParam('apellido');
    $email = strtolower($request->getParam('email'));
    $password = $request->getParam('password');

    // Verify that the information is present
    if ($nombre && $apellido && $email && $password) {
        // Verify that the email has an email format
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Check that there is no other users's with the same username
            $sql = "SELECT email FROM usuario where email = '$email'";

            try {
                // Get db object
                $db = new db();
                // Connect
                $db = $db->connect();

                $stmt = $db->query($sql);
                $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);

                if (empty($usuarios)) {

                    // If it is, create the hash for storage
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);
                    $false = 0;

                    // Store the information in the database
                    $sql = "INSERT INTO usuario (nombre, apellido, email, pass_hash, pendiente_cambio_pass) VALUES (:nombre,:apellido,:email,:password,:pendiente_cambio_pass)";

                    $stmt = $db->prepare($sql);
                    $stmt->bindparam(':nombre', $nombre);
                    $stmt->bindparam(':apellido', $apellido);
                    $stmt->bindparam(':password', $password_hash);
                    $stmt->bindparam(':email', $email);
                    $stmt->bindparam(':pendiente_cambio_pass', $false);

                    $stmt->execute();

                    $sql="SELECT * FROM usuario WHERE id = LAST_INSERT_ID()";
                    $stmt = $db->query($sql);
                    $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);

                    unset($usuarios[0]->password);

                    $usuario = $usuarios[0];

                    // Si se crea el profesor correctamente, lo loguea
                    // Store the user token in the database
                    // Prepare viarables
                    $access_token = random_str(32);
                    $now = time();
                    $user_id = $usuario->id;

                    // SQL statement
                    $sql = "INSERT INTO login (usuario_id,token,login_dttm) VALUES (:user_id,:token,:now)";

                    $stmt = $db->prepare($sql);

                    $stmt->bindParam(':user_id', $user_id);
                    $stmt->bindParam(':token', $access_token);
                    $stmt->bindParam(':now', $now);

                    $stmt->execute();

                    $usuario->token = $access_token;
                    $usuario->grant_type = "password";

                    $db = null;

                    return dataResponse($response, $usuario, 201);
                } else { // if (empty($user)) {
                    return messageResponse($response, 'El usuario ya existe', 401);
                }
            } catch (PDOException $e) {
                $db = null;
                return messageResponse($response, $e->getMessage(), 500);
            }
        } else { // if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return messageResponse($response, 'Formato de email incorrecto', 401);
        }
    } else { // if ($name && $username && $password && $email) {
        return messageResponse($response, 'Campos incorrectos', 401);
    }
});
