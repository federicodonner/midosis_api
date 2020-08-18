<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve todas las drogas y sus medicinas
$app->get('/api/droga', function (Request $request, Response $response) {
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

                // Verifica que se haya esepcificado de qué pastillero obtener los medicamentos
                $pastillero_id = $request->getQueryParams()['pastillero'];

                // En caso de contar con el pastillero, se hace la consulta
                if ($pastillero_id) {

                    // Verifica que el usuario tenga permisos de lectura del pastillero
                    $usuario_id = $user_found[0]->usuario_id;
                    $permisos_usuario = verificarPermisosUsuarioPastillero($usuario_id, $pastillero_id);
                    // Como es un GET sólo verifica permisos de lectura
                    if ($permisos_usuario->acceso_lectura_pastillero) {
                        $sql = "SELECT * FROM droga WHERE pastillero_id = $pastillero_id ORDER BY nombre";
                    } else {    //   if ($permisos_usuario->acceso_lectura_pastillero) {
                        $db = null;
                        return messageResponse($response, 'No tiene permisos para acceder al pastillero seleccionado', 403);
                    }
                } else {
                    $sql = "SELECT * FROM droga ORDER BY nombre";
                }
                try {
                    $db = new db();
                    $db = $db->connect();

                    // Selecciona todas las drogas
                    $stmt = $db->query($sql);
                    $drogas = $stmt->fetchAll(PDO::FETCH_OBJ);

                    // Genera un objeto para la respuesta
                    $drogas_response->drogas = $drogas;
                    $db = null;
                    return dataResponse($response, $drogas_response, 200);
                } catch (PDOException $e) {
                    $db = null;
                    return messageResponse($response, $e->getMessage(), 503);
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




// Add product
$app->post('/api/droga', function (Request $request, Response $response) {
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
                $nombre = $request->getParam('nombre');
                $pastillero_id = $request->getParam('pastillero');
                // Verify that the information is present
                if ($nombre && $pastillero_id) {

                    // Verifica que el pastillero exista
                    $sql = "SELECT * FROM pastillero WHERE id=$pastillero_id";

                    $db = new db();
                    $db = $db->connect();

                    // Selecciona todas las drogas
                    $stmt = $db->query($sql);
                    $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);

                    // Verifica que haya algún pastillero con ese id
                    if (count($pastilleros)>0) {

                        // Verifica que el usuario tenga permisos de escritura del pastillero
                        $usuario_id = $user_found[0]->usuario_id;
                        $permisos_usuario = verificarPermisosUsuarioPastillero($usuario_id, $pastillero_id);
                        // Como es un POST verifica permisos de escritura
                        if ($permisos_usuario->acceso_edicion_pastillero) {
                            $sql = "INSERT INTO droga (nombre, pastillero_id) VALUES (:nombre, :pastillero)";

                            try {
                                $stmt = $db->prepare($sql);

                                $stmt->bindParam(':nombre', $nombre);
                                $stmt->bindParam(':pastillero', $pastillero_id);

                                $stmt->execute();

                                // Obtiene el id de la droga recién creada para devolverla
                                $sql="SELECT * FROM droga WHERE id = LAST_INSERT_ID()";
                                $stmt = $db->query($sql);
                                $drogas = $stmt->fetchAll(PDO::FETCH_OBJ);

                                $droga = $drogas[0];

                                $db=null;
                                return dataResponse($response, $droga, 201);
                            } catch (PDOException $e) {
                                echo '{"error":{"text": '.$e->getMessage().'}}';
                            }
                        } else {  // if ($permisos_usuario->acceso_lectura_pastillero) {
                            $db = null;
                            return messageResponse($response, 'No tiene permisos para acceder al pastillero seleccionado', 403);
                        }
                    } else {  // if(count($pastilleros)>0){
                        return messageResponse($response, 'El pastillero seleccionado no existe', 404);
                    }
                } else {   //   if ($nombre && $pastillero_id) {
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
