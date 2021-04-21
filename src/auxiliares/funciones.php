<?php

use \Psr\Http\Message\ResponseInterface as Response;

// Responde un texto
 function messageResponse(Response $response, String $text, Int $status)
 {
     $responseBody = array('detail' => $text);
     $newResponse = $response
 ->withStatus($status)
 ->withJson($responseBody);
     return $newResponse;
 };

// Responde un objeto
function dataResponse(Response $response, object $data, Int $status)
{
    $newResponse = $response
->withStatus($status)
->withJson($data);
    return $newResponse;
}


// Devuelve un string aleatorio de largo especificado
 function random_str($length, $keyspace = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ')
 {
     $pieces = [];
     $max = mb_strlen($keyspace, '8bit') - 1;
     for ($i = 0; $i < $length; ++$i) {
         $pieces []= $keyspace[random_int(0, $max)];
     }
     return implode('', $pieces);
 };


 // Devuelve el usuario del login en base al token
   function verifyToken(String $access_token)
   {
       if (empty($access_token)) {
           return null;
       }
       $sql = "SELECT * FROM login WHERE token = '$access_token' ORDER BY login_dttm DESC LIMIT 1";
       try {
           $db = new db();
           $db = $db->connect();
           $stmt = $db->query($sql);
           $users = $stmt->fetchAll(PDO::FETCH_OBJ);

           // Verifica que el login sea el último del usuario
           $hora_ultimo_login = $users[0]->fechahora;
           $usuario_id = $users[0]->usuario_id;

           $sql = "SELECT * FROM login WHERE usuario_id = '$usuario_id' ORDER BY login_dttm DESC LIMIT 1";
           $stmt = $db->query($sql);
           $ultimo_login = $stmt->fetchAll(PDO::FETCH_OBJ);

           // Verifica que el último login del usuario sea el mismo que el del token
           if ($ultimo_login[0]->token == $access_token) {
               $db = null;
               return $ultimo_login[0];
           }
           $db = null;
           return null;
       } catch (PDOException $e) {
           echo '{"error":{"text": '.$e->getMessage().'}}';
       }
   };


  // Verifica que un usuario tenga permisos para acceder a un pastillero específico
  function verificarPermisosUsuarioPastillero(Int $usuario_id, Int $pastillero_id)
  {

        // Si no se envía un id del pastillero, devuelve falso
      if (!$pastillero_id) {
          return false;
      }

      $sql = "SELECT * FROM usuario_x_pastillero WHERE usuario_id = $usuario_id AND pastillero_id = $pastillero_id";
      try {
          // Get db object
          $db = new db();
          // Connect
          $db = $db->connect();
          $stmt = $db->query($sql);
          $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);

          // Verifica que el profesor logueado tenga permisos para ver el examen
          $acceso_edicion_pastillero = false;
          $acceso_lectura_pastillero = false;

          foreach ($usuarios as $usuario) {
              if ($usuario->activo == 1) {
                  $acceso_lectura_pastillero = true;
                  if ($usuario->admin == 1) {
                      $acceso_edicion_pastillero = true;
                  }
              }
          }
          $permisos_usuario->acceso_edicion_pastillero = $acceso_edicion_pastillero;
          $permisos_usuario->acceso_lectura_pastillero = $acceso_lectura_pastillero;

          return $permisos_usuario;
      } catch (PDOException $e) {
          echo '{"error":{"text": '.$e->getMessage().'}}';
      }
  }
