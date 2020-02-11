<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve todas las drogas y sus medicinas
$app->get('/api/droga', function (Request $request, Response $response) {
    try {
        $sql = "SELECT * FROM droga";
        // Get db object
        $db = new db();
        // Connect
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

        // Reset all variables
        $db = null;

        // Agrega el array de drogas para la respuesta
        $newResponse = $response->withJson($drogas);
        return $newResponse;
    } catch (PDOException $e) {
        echo '{"error":{"text": '.$e->getMessage().'}}';
    }
});




// Add product
$app->post('/api/droga', function (Request $request, Response $response) {
    $nombre = $request->getParam('nombre');

    $sql = "INSERT INTO droga (nombre) VALUES (:nombre)";

    try {
        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);

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
