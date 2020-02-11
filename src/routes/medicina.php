<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve todas las drogas y sus medicinas
$app->get('/api/medicina', function (Request $request, Response $response) {
    try {
        $sql = "SELECT * FROM medicina";
        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();

        // Selecciona todas las drogas
        $stmt = $db->query($sql);
        $medicinas = $stmt->fetchAll(PDO::FETCH_OBJ);

        //Encuentra las medicinas que tienen la droga seleccionada
        foreach ($medicinas as $medicina) {

            // Por cada medicina, hace el query para buscar el nombre de la droga
            $droga_id = $medicina->droga_id;
            $sql = "SELECT * FROM droga WHERE id = $droga_id";
            $stmt = $db->query($sql);
            $drogas = $stmt->fetchAll(PDO::FETCH_OBJ);
            $droga_nombre = $drogas[0]->nombre;
            $medicina->droga = $droga_nombre;
        }

        $db = null;

        // Añade las medicinas a un array para la respuesta
        $newResponse = $response->withJson($medicinas);
        return $newResponse;
    } catch (PDOException $e) {
        echo '{"error":{"text": '.$e->getMessage().'}}';
    }
});




// Añade una medicina
$app->post('/api/medicina', function (Request $request, Response $response) {
    $nombre = $request->getParam('nombre');
    $droga_id = $request->getParam('droga_id');
    $comprimidos_por_caja = $request->getParam('comprimidos_por_caja');
    $concentracion_mg = $request->getParam('concentracion_mg');
    $imagen = $request->getParam('imagen');


    $sql = "INSERT INTO medicina (
      nombre,
      droga_id,
      comprimidos_por_caja,
      concentracion_mg,
      imagen) VALUES (
        :nombre,
        :droga_id,
        :comprimidos_por_caja,
        :concentracion_mg,
        :imagen)";

    try {
        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':droga_id', $droga_id);
        $stmt->bindParam(':comprimidos_por_caja', $comprimidos_por_caja);
        $stmt->bindParam(':concentracion_mg', $concentracion_mg);
        $stmt->bindParam(':imagen', $imagen);

        $stmt->execute();

        $newResponse = $response->withStatus(200);
        $body = $response->getBody();
        $body->write('{"status": "success","message": "Medicina agregada", "Medicina": "'.$nombre.'"}');
        $newResponse = $newResponse->withBody($body);
        return $newResponse;
    } catch (PDOException $e) {
        echo '{"error":{"text": '.$e->getMessage().'}}';
    }
});
