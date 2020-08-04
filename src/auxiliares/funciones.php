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
