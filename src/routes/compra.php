<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Add product
$app->post('/api/compra', function (Request $request, Response $response) {
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

// Verifica que se hayan enviado todos los parámetros



                $droga_id = $request->getParam('droga');
                $comprimido = $request->getParam('comprimido');
                $cantidad = $request->getParam('cantidad')*12;

                if ($droga_id && $comprimido && $cantidad) {

                    // Verifica que la droga exista
                    $sql="SELECT * FROM droga WHERE id = $droga_id";

                    $db = new db();
                    $db = $db->connect();

                    $stmt = $db->query($sql);
                    $drogas = $stmt->fetchAll(PDO::FETCH_OBJ);

                    if (count($drogas)>0) {

                        // Verifica que el usuario tenga permisos de edición del pastillero
                        $pastillero_id = $drogas[0]->pastillero_id;
                        $usuario_id = $user_found[0]->usuario_id;
                        $permisos_usuario = verificarPermisosUsuarioPastillero($usuario_id, $pastillero_id);

                        // Como es un POST verifica permisos de escritura
                        if ($permisos_usuario->acceso_edicion_pastillero) {
                            try {
                                $sql = "SELECT * FROM stock WHERE droga_id = $droga_id AND comprimido = $comprimido";

                                // Selecciona el stock existente de la droga ingresada
                                $stmt = $db->query($sql);
                                $stocks = $stmt->fetchAll(PDO::FETCH_OBJ);

                                // Verifica que el array esté vacío
                                // Si está vacío tengo que crear una nueva entrada en la tabla
                                if (empty($stocks)) {
                                    $fecha_ahora = time();

                                    $sql = "INSERT INTO stock (droga_id, comprimido, cantidad_doceavos, ingreso_dttm) VALUES (:droga_id,:comprimido,:cantidad,:ingreso_dttm)";

                                    $stmt = $db->prepare($sql);
                                    $stmt->bindParam(':droga_id', $droga_id);
                                    $stmt->bindParam(':comprimido', $comprimido);
                                    $stmt->bindParam(':cantidad', $cantidad);
                                    $stmt->bindParam(':ingreso_dttm', $fecha_ahora);
                                    $stmt->execute();
                                } else {

                                    // Si el array no está vacío, entonces incremento el stock del registro
                                    $nuevo_stock = $stocks[0]->cantidad_doceavos + $cantidad;
                                    $id_stock = $stocks[0]->id;
                                    $sql = "UPDATE stock  SET cantidad_doceavos = $nuevo_stock WHERE id=$id_stock";
                                    $stmt = $db->prepare($sql);
                                    $stmt->execute();
                                }

                                $db = null;
                                return messageResponse($response, "Compra ingresada exitosamente.", 201);
                            } catch (PDOException $e) {
                                $db = null;
                                return messageResponse($response, $e->getMessage(), 503);
                            }
                        } else {  // if ($permisos_usuario->acceso_lectura_pastillero) {
                            $db = null;
                            return messageResponse($response, 'No tiene permisos para editar el pastillero seleccionado', 403);
                        }
                    } else {  // if (count($drogas)>0) {
                        $db = null;
                        return messageResponse($response, 'La droga seleccionada no existe', 404);
                    }
                } else {   //   if($droga && $comprimido && $cantidad){
                    $db = null;
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
