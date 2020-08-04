<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve el listado de todos los pastilleros
$app->get('/api/pastillero', function (Request $request, Response $response) {
    try {
        $sql = "SELECT * FROM pastillero";
        $db = new db();
        $db = $db->connect();

        // Selecciona todos los pastilleros
        $stmt = $db->query($sql);
        $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Los tiene que agregar a un objeto para devolverlos
        $pastilleros_response->pastilleros = $pastilleros;

        $db = null;
        return dataResponse($response, $pastilleros_response, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 503);
    }
});


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
