<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve el listado de todos los pastilleros
$app->get('/api/pastillero', function (Request $request, Response $response) {
    try {
        $sql = "SELECT * FROM pastillero";
        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();

        // Selecciona todos los pastilleros
        $stmt = $db->query($sql);
        $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);


        // Reset all variables
        $db = null;

        // Agrega el array de drogas para la respuesta
        $newResponse = $response->withJson($pastilleros);
        return $newResponse;
    } catch (PDOException $e) {
        echo '{"error":{"text": '.$e->getMessage().'}}';
    }
});


// Devuelve todas las drogas y sus medicinas
$app->get('/api/pastillero/{id}', function (Request $request, Response $response) {
    try {
      $id = $request->getAttribute('id');
        $sql = "SELECT * FROM pastillero WHERE id = $id";
        // Get db object
        $db = new db();
        // Connect
        $db = $db->connect();

        // Selecciona todas las drogas
        $stmt = $db->query($sql);
        $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);
        $pastillero = $pastilleros[0];

$sql = "SELECT * FROM dosis WHERE pastillero_id = $id";

            $stmt = $db->query($sql);
            $dosis = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach($dosis as $dosi){
$dosi_id = $dosi->id;
  $sql = "SELECT * FROM droga_x_dosis WHERE dosis_id = $dosi_id";
  $stmt = $db->query($sql);
  $drogas = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach($drogas as $droga){
  $droga_id = $droga->id;
  $sql = "SELECT * FROM droga WHERE id = $droga_id";
  $stmt = $db->query($sql);
  $drogas_de_dosis = $stmt->fetchAll(PDO::FETCH_OBJ);

  $droga->nombre = $drogas_de_dosis[0]->nombre;
}

  $dosi->drogas = $drogas;
}


            $pastillero->dosis = $dosis;


        // Reset all variables
        $db = null;

        // Agrega el array de drogas para la respuesta
        $newResponse = $response->withJson($pastillero);
        return $newResponse;
    } catch (PDOException $e) {
        echo '{"error":{"text": '.$e->getMessage().'}}';
    }
});

// */
