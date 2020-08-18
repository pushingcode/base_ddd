<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @author Carlos Guillen
 *        
 */
class HomeApiController
{

    /**
     * @return JsonResponse
     */    
    public function homeApi(): JsonResponse
    {
        $data = [
            "title"=>"Hola Mundo"
            ,"content"=>"Hola Contenido"
        ];
        
        $header = ['Access-Control-Allow-Origin'=>'*'];
        
        $response =   new JsonResponse($data, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->add($header);
        return $response;
    }
}

