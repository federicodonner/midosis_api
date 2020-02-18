<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Add product
$app->post('/api/drogaxdosis', function (Request $request, Response $response) {
    $droga_id = $request->getParam('droga_id');
    $dosis_id = $request->getParam('dosis_id');
    $cantidad_mg = $request->getParam('cantidad_mg');

    $sql = "INSERT INTO droga_x_dosis (droga_id,dosis_id,cantidad_mg) VALUES (:droga_id,:dosis_id,:cantidad_mg)";

    try {
        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':droga_id', $droga_id);
        $stmt->bindParam(':dosis_id', $dosis_id);
        $stmt->bindParam(':cantidad_mg', $cantidad_mg);

        $stmt->execute();

        $newResponse = $response->withStatus(200);
        $body = $response->getBody();
        $body->write('{"status": "success","message": "dosis agregada"}');
        $newResponse = $newResponse->withBody($body);
        return $newResponse;
    } catch (PDOException $e) {
        echo '{"error":{"text": '.$e->getMessage().'}}';
    }
});


// Update product
$app->put('/api/drogaxdosis/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');

    $droga_id = $request->getParam('droga_id');
    $dosis_id = $request->getParam('dosis_id');
    $cantidad_mg = $request->getParam('cantidad_mg');


    $sql = "UPDATE droga_x_dosis SET
        droga_id = :droga_id,
        dosis_id = :dosis_id,
        cantidad_mg = :cantidad_mg
        WHERE id = $id";

    try {
        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':droga_id', $droga_id);
        $stmt->bindParam(':dosis_id', $dosis_id);
        $stmt->bindParam(':cantidad_mg', $cantidad_mg);

        $stmt->execute();

        $newResponse = $response->withStatus(200);
        $body = $response->getBody();
        $body->write('{"status": "success","message": "Dosis actualizada"}');
        $newResponse = $newResponse->withBody($body);
    } catch (PDOException $e) {
        echo '{"error":{"text": '.$e->getMessage().'}}';
    }
});
