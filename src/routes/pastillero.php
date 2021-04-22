<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve todas las drogas y sus medicinas
$app->get('/api/pastillero/{id}', function (Request $request, Response $response) {
    // El id del usuario logueado viene del middleware authentication
    $usuario_id = $request->getAttribute('usuario_id');
    // Verifica que el usuario tenga permisos de lectura del pastillero
    $pastillero_id = $request->getAttribute('id');
    $permisos_usuario = verificarPermisosUsuarioPastillero($usuario_id, $pastillero_id);
    // Como es un GET sólo verifica permisos de lectura
    if (!$permisos_usuario->acceso_lectura_pastillero) {
        $db = null;
        return messageResponse($response, 'No tiene permisos para acceder al pastillero seleccionado', 403);
    }
    try {
        $id = $request->getAttribute('id');
        $sql = "SELECT id, dia_actualizacion, paciente_id FROM pastillero WHERE id = $id";
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

        $paciente_id = $pastillero->paciente_id;
        $sql="SELECT nombre, apellido FROM usuario WHERE id=$paciente_id";
        $stmt = $db->query($sql);
        $pacientes = $stmt->fetchAll(PDO::FETCH_OBJ);

        $pastillero->paciente=$pacientes[0];

        $db = null;
        return dataResponse($response, $pastillero, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 503);
    }
})->add($authenticate);


// Agrega un pastillero
$app->post('/api/pastillero', function (Request $request, Response $response) {
    // El id del usuario logueado viene del middleware authentication
    $usuario_id = $request->getAttribute('usuario_id');

    // Obtiene los detalles del pastillero del request
    $dia_actualizacion = $request->getParam('dia_actualizacion');
    $dosis = $request->getParam('dosis');

    // Verify that the information is present
    if (!$dia_actualizacion) {
        return messageResponse($response, 'Campos incorrectos', 400);
    }
    try {

            // Genera el pastillero en la base de datos
        $sql = "INSERT INTO pastillero (dia_actualizacion, paciente_id) VALUES (:dia_actualizacion, :paciente_id)";
        $db = new db();
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->bindparam(':dia_actualizacion', $dia_actualizacion);
        $stmt->bindparam(':paciente_id', $usuario_id);
        $stmt->execute();

        // Obtiene el id del pastillero recién creado para asignarle el usuario
        $sql="SELECT * FROM pastillero WHERE id = LAST_INSERT_ID()";
        $stmt = $db->query($sql);
        $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);

        $pastillero = $pastilleros[0];
        $pastillero_id = $pastillero->id;

        // Agrega al usuario como administrados y paciente del pastillero
        $sql = "INSERT INTO usuario_x_pastillero (usuario_id, pastillero_id, admin, activo) VALUES (:usuario_id, :pastillero_id, :admin, :activo)";

        $uno = 1;

        $stmt = $db->prepare($sql);
        $stmt->bindparam(':usuario_id', $usuario_id);
        $stmt->bindparam(':pastillero_id', $pastillero_id);
        $stmt->bindparam(':admin', $uno);
        $stmt->bindparam(':activo', $uno);
        $stmt->execute();

        // Verifica que el request tenga dosis para el pastillero
        if ($dosis) {
            foreach ($dosis as $dosi) {
                // Convierte el string a un JSON
                $dosi_objeto = json_decode($dosi);

                $sql = "INSERT INTO dosis (horario, pastillero_id) VALUES (:horario, :pastillero_id)";

                $horario = $dosi_objeto->horario;

                $stmt = $db->prepare($sql);
                $stmt->bindparam(':horario', $horario);
                $stmt->bindparam(':pastillero_id', $pastillero_id);
                $stmt->execute();
            }
        }

        // Busca los detalles del usuario para devolverlo
        $sql = "SELECT uxp.*, u.* FROM usuario_x_pastillero uxp LEFT JOIN usuario u ON uxp.usuario_id = u.id WHERE usuario_id = $usuario_id AND pastillero_id = $pastillero_id";

        $stmt = $db->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);

        unset($usuarios[0]->pass_hash);
        unset($usuarios[0]->pendiente_cambio_pass);

        $pastillero->usuarios = $usuarios;

        // Busca las dosis del pastillero para devolverlo
        $sql="SELECT * FROM dosis WHERE pastillero_id = $pastillero_id";

        $stmt = $db->query($sql);
        $dosis_pastillero = $stmt->fetchAll(PDO::FETCH_OBJ);

        $pastillero->dosis = $dosis_pastillero;

        $db = null;
        return dataResponse($response, $pastillero, 201);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);


// Edita los nombres de las dosis del pastillero
$app->put('/api/pastillero/{id}', function (Request $request, Response $response) {
    // El id del usuario logueado viene del middleware authentication
    $usuario_id = $request->getAttribute('usuario_id');
    $pastillero_id = $request->getAttribute('id');

    // Verifica que el usuario tenga permisos de lectura del pastillero
    $permisos_usuario = verificarPermisosUsuarioPastillero($usuario_id, $pastillero_id);
    // Como es un PUT verifica permisos de escritura
    if (!$permisos_usuario->acceso_edicion_pastillero) {
        $db = null;
        return messageResponse($response, 'No tiene permisos para acceder al pastillero seleccionado', 403);
    }

    // Obtiene los detalles del pastillero del request
    $dosis = $request->getParam('dosis');

    // Verify that the information is present
    if (!$dosis) {
        return messageResponse($response, 'Campos incorrectos', 400);
    }

    try {
        $sql = "SELECT * FROM pastillero WHERE id = $pastillero_id";
        $db = new db();
        $db = $db->connect();

        $stmt = $db->query($sql);
        $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);

        print_r($pastilleros);

        // if
//
//       $pastillero = $pastilleros[0];
//
//
//
//
//         // Genera el pastillero en la base de datos
//         $sql = "INSERT INTO pastillero (dia_actualizacion, paciente_id) VALUES (:dia_actualizacion, :paciente_id)";
//         $db = new db();
//         $db = $db->connect();
//
//         $stmt = $db->prepare($sql);
//         $stmt->bindparam(':dia_actualizacion', $dia_actualizacion);
//         $stmt->bindparam(':paciente_id', $usuario_id);
//         $stmt->execute();
//
//         // Obtiene el id del pastillero recién creado para asignarle el usuario
//         $sql="SELECT * FROM pastillero WHERE id = LAST_INSERT_ID()";
//         $stmt = $db->query($sql);
//         $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);
//
//         $pastillero = $pastilleros[0];
//         $pastillero_id = $pastillero->id;
//
//         // Agrega al usuario como administrados y paciente del pastillero
//         $sql = "INSERT INTO usuario_x_pastillero (usuario_id, pastillero_id, admin, activo) VALUES (:usuario_id, :pastillero_id, :admin, :activo)";
//
//         $uno = 1;
//
//         $stmt = $db->prepare($sql);
//         $stmt->bindparam(':usuario_id', $usuario_id);
//         $stmt->bindparam(':pastillero_id', $pastillero_id);
//         $stmt->bindparam(':admin', $uno);
//         $stmt->bindparam(':activo', $uno);
//         $stmt->execute();
//
//         // Verifica que el request tenga dosis para el pastillero
//         if ($dosis) {
//             foreach ($dosis as $dosi) {
//                 // Convierte el string a un JSON
//                 $dosi_objeto = json_decode($dosi);
//
//                 $sql = "INSERT INTO dosis (horario, pastillero_id) VALUES (:horario, :pastillero_id)";
//
//                 $horario = $dosi_objeto->horario;
//
//                 $stmt = $db->prepare($sql);
//                 $stmt->bindparam(':horario', $horario);
//                 $stmt->bindparam(':pastillero_id', $pastillero_id);
//                 $stmt->execute();
//             }
//         }
//
//         // Busca los detalles del usuario para devolverlo
//         $sql = "SELECT uxp.*, u.* FROM usuario_x_pastillero uxp LEFT JOIN usuario u ON uxp.usuario_id = u.id WHERE usuario_id = $usuario_id AND pastillero_id = $pastillero_id";
//
//         $stmt = $db->query($sql);
//         $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);
//
//         unset($usuarios[0]->pass_hash);
//         unset($usuarios[0]->pendiente_cambio_pass);
//
//         $pastillero->usuarios = $usuarios;
//
//         // Busca las dosis del pastillero para devolverlo
//         $sql="SELECT * FROM dosis WHERE pastillero_id = $pastillero_id";
//
//         $stmt = $db->query($sql);
//         $dosis_pastillero = $stmt->fetchAll(PDO::FETCH_OBJ);
//
//         $pastillero->dosis = $dosis_pastillero;
//
//         $db = null;
//         return dataResponse($response, $pastillero, 201);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);


// Genera el código para compartir el pastillero y lo guarda en la base de datos
$app->get('/api/compartirpastillero/{id}', function (Request $request, Response $response) {
    // El id del usuario logueado viene del middleware authentication
    $usuario_id = $request->getAttribute('usuario_id');
    // Verifica que el usuario tenga permisos de lectura del pastillero
    $pastillero_id = $request->getAttribute('id');
    $permisos_usuario = verificarPermisosUsuarioPastillero($usuario_id, $pastillero_id);
    // Verifica permisos de escritura para el pastillero
    if (!$permisos_usuario->acceso_edicion_pastillero) {
        $db = null;
        return messageResponse($response, 'No tiene permisos para acceder al pastillero seleccionado', 403);
    }
    try {
        // Si tiene persmisos de secritura, genera el hash para compartir
        $hash=random_str(6, "23456789ABCDEFGHJKLMNPQRSTUVWXYZ");
        // Lo agarega a la base de datos
        $sql = "UPDATE pastillero SET hash_compartir = '$hash' WHERE id = $pastillero_id";
        $db = new db();
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->execute();

        // Devuelve el hash para mostrar
        $db = null;
        $objeto_respuesta->hash = $hash;
        return dataResponse($response, $objeto_respuesta, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);

// Utiliza el código de compartir de un pastillero y agrega al usuario como editor
$app->POST('/api/agregarpastillero', function (Request $request, Response $response) {
    // El id del usuario logueado viene del middleware authentication
    $usuario_id = $request->getAttribute('usuario_id');

    // Verifica que recibió toda la información necesaria desde el request
    $hash = $request->getParam('hash');
    if (!$hash) {
        $db=null;
        return messageResponse($response, 'Debe especificar un hash', 400);
    }

    try {
        // Verifica que exista algún pastillero con ese hash
        $sql="SELECT * FROM pastillero WHERE hash_compartir='$hash'";
        $db = new db();
        $db = $db->connect();

        $stmt = $db->query($sql);
        $pastilleros = $stmt->fetchAll(PDO::FETCH_OBJ);


        if (count($pastilleros)==0) {
            $db=null;
            return messageResponse($response, 'No hay ningún pastillero con ese hash', 404);
        }

        $pastillero=$pastilleros[0];

        // Verifica que el usuario no sea el paciente del pastillero
        if ($pastillero->paciente_id == $usuario_id) {
            $db=null;
            return messageResponse($response, 'Eres el paciente de este pastillero, no puedes agregarlo a tu cuenta', 409);
        }

        // Verifica que el usuario no tenga control ya del pastillero
        $pastillero_id = $pastillero->id;
        $sql = "SELECT * FROM usuario_x_pastillero WHERE usuario_id = $usuario_id AND pastillero_id = $pastillero_id";

        $stmt = $db->query($sql);
        $permisos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if (count($permisos)!=0) {
            $db=null;
            return messageResponse($response, 'Ya tienes acceso al pastillero seleccionado', 409);
        }


        // Si estoy acá es porque el pastillero existe, el usuario no es el paciente
        // ni tiene permisos sobre el pastillero seleccionado

        // Elimino el hash
        $sql="UPDATE pastillero SET hash_compartir = NULL WHERE id = $pastillero_id";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        // Agrega el usuario al pastillero
        $sql="INSERT INTO usuario_x_pastillero (usuario_id, pastillero_id, admin, activo) VALUES ($usuario_id, $pastillero_id, 1,1)";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        return messageResponse($response, "Acceso otorgado correctamente", 201);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);
