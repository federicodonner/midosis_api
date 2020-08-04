<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve todas las drogas y sus medicinas
$app->get('/api/droga', function (Request $request, Response $response) {
    try {

      // Verifica que se haya esepcificado de qué pastillero obtener los medicamentos
        $pastillero = $request->getQueryParams()['pastillero'];

        // En caso de contar con el pastillero, se hace la consulta
        if ($pastillero) {
            $sql = "SELECT * FROM droga WHERE pastillero = '$pastillero' ORDER BY nombre";
        } else {
            $sql = "SELECT * FROM droga ORDER BY nombre";
        }

        $db = new db();
        $db = $db->connect();

        // Selecciona todas las drogas
        $stmt = $db->query($sql);
        $drogas = $stmt->fetchAll(PDO::FETCH_OBJ);

        //Encuentra las medicinas que tienen la droga seleccionada
        foreach ($drogas as $droga) {

            // Por cada droga, hace la query para buscar la medicina
            $droga_id = $droga->id;
            $sql = "SELECT * FROM medicina WHERE droga_id = $droga_id";
            $stmt = $db->query($sql);
            $medicinas = $stmt->fetchAll(PDO::FETCH_OBJ);

            $droga->medicinas = $medicinas;
        }

        // Genera un objeto para la respuesta
        $drogas_response->drogas = $drogas;
        $db = null;
        return dataResponse($response, $drogas_response, 200);
        return $newResponse;
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 503);
    }
});




// Add product
$app->post('/api/droga', function (Request $request, Response $response) {
    $nombre = $request->getParam('nombre');
    $pastillero = $request->getParam('pastillero');

    $sql = "INSERT INTO droga (nombre, pastillero) VALUES (:nombre, :pastillero)";

    try {
        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':pastillero', $pastillero);

        $stmt->execute();

        $newResponse = $response->withStatus(200);
        $body = $response->getBody();
        $body->write('{"status": "success","message": "Droga agregada", "droga": "'.$nombre.'"}');
        $newResponse = $newResponse->withBody($body);
        return $newResponse;
    } catch (PDOException $e) {
        echo '{"error":{"text": '.$e->getMessage().'}}';
    }
});
