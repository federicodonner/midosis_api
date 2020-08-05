<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve todas las drogas y sus medicinas
$app->get('/api/pastillero/{id}', function (Request $request, Response $response) {
    try {
        $id = $request->getAttribute('id');
        $sql = "SELECT * FROM pastillero WHERE id = $id";
        $db = new db();
        $db = $db->connect();

        // FALTA VERIFICAR QUE EL PASTILLERO EXISTA
        $stmt = $db->query($sql);
        $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);
        $pastillero = $pastilleros[0];

        // Obtiene las dosis ingresadas para el pastillero
        $sql = "SELECT * FROM dosis WHERE pastillero_id = $id";

        $stmt = $db->query($sql);
        $dosis = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Por cada dosis, va a buscar qué droga le corresponde
        foreach ($dosis as $dosi) {
            $dosi_id = $dosi->id;
            $sql = "SELECT * FROM droga_x_dosis WHERE dosis_id = $dosi_id";
            $stmt = $db->query($sql);
            $drogas = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Por cada droga, va a buscar los detalles
            foreach ($drogas as $droga) {
                $droga_id = $droga->droga_id;
                $sql = "SELECT * FROM droga WHERE id = $droga_id";
                $stmt = $db->query($sql);
                $drogas_de_dosis = $stmt->fetchAll(PDO::FETCH_OBJ);

                $droga->nombre = $drogas_de_dosis[0]->nombre;
            }

            $dosi->drogas = $drogas;
        }

        $pastillero->dosis = $dosis;

        $db = null;
        return dataResponse($response, $pastillero, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 503);
    }
});


// Agrega un pastillero
$app->post('/api/pastillero', function (Request $request, Response $response) {
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
                $usuario_id = $user_found[0]->id;
                // Obtiene los detalles del pastillero del request
                $dia_actualizacion = $request->getParam('dia_actualizacion');
                $dosis = $request->getParam('dosis');

                // Verify that the information is present
                if ($dia_actualizacion) {
                    try {

                      // Genera el pastillero en la base de datos
                        $sql = "INSERT INTO pastillero (dia_actualizacion) VALUES (:dia_actualizacion)";
                        $db = new db();
                        $db = $db->connect();

                        $stmt = $db->prepare($sql);
                        $stmt->bindparam(':dia_actualizacion', $dia_actualizacion);
                        $stmt->execute();

                        // Obtiene el id del pastillero recién creado para asignarle el usuario
                        $sql="SELECT * FROM pastillero WHERE id = LAST_INSERT_ID()";
                        $stmt = $db->query($sql);
                        $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);

                        $pastillero = $pastilleros[0];
                        $pastillero_id = $pastillero->id;

                        // Agrega al usuario como administrados y paciente del pastillero
                        $sql = "INSERT INTO usuario_x_pastillero (usuario_id, pastillero_id, admin, activo, paciente) VALUES (:usuario_id, :pastillero_id, :admin, :activo, :paciente)";

                        $uno = 1;

                        $stmt = $db->prepare($sql);
                        $stmt->bindparam(':usuario_id', $usuario_id);
                        $stmt->bindparam(':pastillero_id', $pastillero_id);
                        $stmt->bindparam(':admin', $uno);
                        $stmt->bindparam(':activo', $uno);
                        $stmt->bindparam(':paciente', $uno);
                        $stmt->execute();

                        // Verifica que el request tenga dosis para el pastillero
                        if ($dosis) {
                            foreach ($dosis as $dosi) {
                                // Convierte el string a un JSON
                                $dosi_objeto = json_decode($dosi);
                                $sql = "INSERT INTO dosis (horario, pastillero_id) VALUES (:horario, :pastillero_id)";

                                $horario = $dosi_objeto->horario;

                                $stmt = $db->prepare($sql);
                                $stmt->bindparam(':horario', $horario);
                                $stmt->bindparam(':pastillero_id', $pastillero_id);
                                $stmt->execute();
                            }
                        }

                        // Busca los detalles del usuario para devolverlo
                        $sql = "SELECT uxp.*, u.* FROM usuario_x_pastillero uxp LEFT JOIN usuario u ON uxp.usuario_id = u.id WHERE usuario_id = $usuario_id AND pastillero_id = $pastillero_id";

                        $stmt = $db->query($sql);
                        $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);

                        unset($usuarios[0]->pass_hash);
                        unset($usuarios[0]->pendiente_cambio_pass);

                        $pastillero->usuarios = $usuarios;

                        // Busca las dosis del pastillero para devolverlo
                        $sql="SELECT * FROM dosis WHERE pastillero_id = $pastillero_id";

                        $stmt = $db->query($sql);
                        $dosis_pastillero = $stmt->fetchAll(PDO::FETCH_OBJ);

                        $pastillero->dosis = $dosis_pastillero;


                        $db = null;
                        return dataResponse($response, $pastillero, 201);
                    } catch (PDOException $e) {
                        $db = null;
                        return messageResponse($response, $e->getMessage(), 500);
                    }
                } else { //if ($dia_actualización) {
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
