<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Agregar drogaxdosis
$app->post('/api/drogaxdosis', function (Request $request, Response $response) {
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
                $droga_id = $request->getParam('droga_id');
                $dosis_id = $request->getParam('dosis_id');
                $cantidad_mg = $request->getParam('cantidad_mg');
                $notas = $request->getParam('notas');

                // Verifica que se hayan enviado los campos correctos
                if ($droga_id && $dosis_id && $cantidad_mg) {


                    // Verifica que la droga exista
                    $sql="SELECT * FROM droga WHERE id = $droga_id";

                    $db = new db();
                    $db = $db->connect();

                    $stmt = $db->query($sql);
                    $drogas = $stmt->fetchAll(PDO::FETCH_OBJ);

                    if (count($drogas)>0) {

                        // Verifica que la dosis exista
                        $sql="SELECT * FROM dosis WHERE id = $dosis_id";

                        $db = new db();
                        $db = $db->connect();

                        $stmt = $db->query($sql);
                        $dosises = $stmt->fetchAll(PDO::FETCH_OBJ);

                        if (count($dosises)>0) {

                            // Verifica que el usuario tenga acceso al pastillero
                            $pastillero_id = $dosises[0]->pastillero_id;
                            $usuario_id = $user_found[0]->usuario_id;
                            $permisos_usuario = verificarPermisosUsuarioPastillero($usuario_id, $pastillero_id);

                            // Como es un POST verifica permisos de escritura
                            if ($permisos_usuario->acceso_edicion_pastillero) {
                                $sql = "INSERT INTO droga_x_dosis (droga_id,dosis_id,cantidad_mg,notas) VALUES (:droga_id,:dosis_id,:cantidad_mg,:notas)";

                                try {
                                    $db = new db();
                                    $db = $db->connect();

                                    $stmt = $db->prepare($sql);

                                    $stmt->bindParam(':droga_id', $droga_id);
                                    $stmt->bindParam(':dosis_id', $dosis_id);
                                    $stmt->bindParam(':cantidad_mg', $cantidad_mg);
                                    $stmt->bindParam(':notas', $notas);

                                    $stmt->execute();

                                    $db = null;
                                    return messageResponse($response, 'Dosis agregada exitosamente.', 201);
                                } catch (PDOException $e) {
                                    $db = null;
                                    return messageResponse($response, $e->getMessage(), 503);
                                }
                            } else {  // if ($permisos_usuario->acceso_lectura_pastillero) {
                                $db = null;
                                return messageResponse($response, 'No tiene permisos para acceder al pastillero seleccionado', 403);
                            }
                        } else {  // if (count($drogas)>0) {
                            return messageResponse($response, 'La toma seleccionada no existe', 404);
                        }
                    } else {  // if (count($drogas)>0) {
                        return messageResponse($response, 'La droga seleccionada no existe', 404);
                    }
                } else {   //   if($droga_id && $dosis_id && $cantidad_mg){
                    return messageResponse($response, 'Campos incorrectos', 401);
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


// Actualizar drogaxdosis
$app->put('/api/drogaxdosis/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');

    $droga_id = $request->getParam('droga_id');
    $dosis_id = $request->getParam('dosis_id');
    $cantidad_mg = $request->getParam('cantidad_mg');
    $notas = $request->getParam('notas');


    $sql = "UPDATE droga_x_dosis SET
        droga_id = :droga_id,
        dosis_id = :dosis_id,
        cantidad_mg = :cantidad_mg,
        notas = :notas
        WHERE id = $id";

    try {
        $db = new db();
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':droga_id', $droga_id);
        $stmt->bindParam(':dosis_id', $dosis_id);
        $stmt->bindParam(':cantidad_mg', $cantidad_mg);
        $stmt->bindParam(':notas', $notas);

        $stmt->execute();

        $db = null;
        return messageResponse($response, 'Dosis actualizada exitosamente.', 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 503);
    }
});

// Eliminar drogaxdosis
$app->delete('/api/drogaxdosis/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');

    $droga_id = $request->getParam('droga_id');
    $dosis_id = $request->getParam('dosis_id');
    $cantidad_mg = $request->getParam('cantidad_mg');
    $notas = $request->getParam('notas');


    $sql = "DELETE FROM droga_x_dosis WHERE id = $id";

    try {
        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->execute();

        $db = null;
        return messageResponse($response, "Dosis eliminada exitosamente.", 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 503);
    }
});
