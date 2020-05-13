<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Add product
$app->post('/api/compra', function (Request $request, Response $response) {
    $droga = $request->getParam('droga');
    $comprimido = $request->getParam('comprimido');
    $cantidad = $request->getParam('cantidad')*12;

    try {
        $sql = "SELECT * FROM stock WHERE droga = $droga AND comprimido = $comprimido";

        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();
        // Selecciona el stock existente de la droga ingresada
        $stmt = $db->query($sql);
        $stocks = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Verifica que el array esté vacío
        // Si está vacío tengo que crear una nueva entrada en la tabla
        if (empty($stocks)) {
            $fecha_ahora = time();

            $sql = "INSERT INTO stock (droga, comprimido, cantidad_doceavos, fecha_ingreso) VALUES (:droga,:comprimido,:cantidad,:fecha_ingreso)";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':droga', $droga);
            $stmt->bindParam(':comprimido', $comprimido);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->bindParam(':fecha_ingreso', $fecha_ahora);
            $stmt->execute();

            $newResponse = $response->withStatus(200);
            $body = $response->getBody();
            $body->write('{"status": "success","message": "Stock actualizado"}');
        } else {
            // Si el array no está vacío, entonces incremento el stock del registro

            $nuevo_stock = $stocks[0]->cantidad_doceavos + $cantidad;
            $id_stock = $stocks[0]->id;
            $sql = "UPDATE stock  SET cantidad_doceavos = $nuevo_stock WHERE id=$id_stock";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $newResponse = $response->withStatus(200);
            $body = $response->getBody();
            $body->write('{"status": "success","message": "Stock actualizado"}');
        }


        $newResponse = $newResponse->withBody($body);
        return $newResponse;
    } catch (PDOException $e) {
        echo '{"error":{"text": '.$e->getMessage().'}}';
    }
});
