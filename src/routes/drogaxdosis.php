<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Agregar drogaxdosis
$app->post('/api/drogaxdosis', function (Request $request, Response $response) {
    $droga_id = $request->getParam('droga_id');
    $dosis_id = $request->getParam('dosis_id');
    $cantidad_mg = $request->getParam('cantidad_mg');
    $notas = $request->getParam('notas');

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
