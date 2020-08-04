<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve todas las drogas y sus medicinas
$app->get('/api/stock/{id}', function (Request $request, Response $response) {
    try {
        // Obtiene el id del pastillero del request
        $pastillero = $request->getAttribute('id');

        $sql = "SELECT * FROM droga WHERE pastillero = '$pastillero' ORDER BY nombre";

        $db = new db();
        $db = $db->connect();

        // Selecciona todas las drogas
        $stmt = $db->query($sql);
        $drogas = $stmt->fetchAll(PDO::FETCH_OBJ);

        //Encuentra las medicinas que tienen la droga seleccionada
        foreach ($drogas as $droga) {

            // Por cada droga, hace la query para buscar el stock
            $droga_id = $droga->id;
            $sql = "SELECT * FROM stock WHERE droga = $droga_id";
            $stmt = $db->query($sql);
            $stocks = $stmt->fetchAll(PDO::FETCH_OBJ);

            $droga_mg_total = 0;

            // Calcula para cuántos días hay stock
            foreach ($stocks as $stock) {
                $stockComprimido = $stock->comprimido;
                $stockCantidad = $stock->cantidad_doceavos/12;

                // droga_mg_total son los miligramos totales disponibles de la medicina
                $droga_mg_total = $droga_mg_total + $stockComprimido*$stockCantidad;
            }
            // Obtiene cuántos mg debe tomar el paciente por día
            $sql = "SELECT cantidad_mg FROM droga_x_dosis WHERE droga_id = $droga_id";
            $stmt = $db->query($sql);
            $dosis_diaria = $stmt->fetchAll(PDO::FETCH_OBJ);
            // Suma todas las dosis diarias de la droga
            $dosis_total = 0;
            foreach ($dosis_diaria as $dosis) {
                $dosis_total = $dosis_total + $dosis->cantidad_mg;
            }

            $droga->disponible_total = $droga_mg_total;
            $droga->dosis_total = $dosis_total;
            $droga->dosis_semanal = $dosis_total * 7;
            if ($dosis_total != 0) {
                $dias_disponible = floor($droga_mg_total/$dosis_total);
            } else {
                $dias_disponible = -1;
            }

            $droga->dias_disponible = $dias_disponible;

            $droga->stocks = $stocks;
        }

        // Genera un objeto para la respuesta
        $drogas_response->drogas = $drogas;

        $db = null;
        return dataResponse($response, $drogas_response, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 503);
    }
});
