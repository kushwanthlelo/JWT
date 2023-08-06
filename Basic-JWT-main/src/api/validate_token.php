<?php
    header("Access-Control-Allow-Origin: http://localhost/api/");
    header('Content-Type: application/json; charset=utf-8');
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Access-Control-Max-Age: 3600");
    
    // required to decode jwt
    include_once 'config/core.php';
    include_once 'libs/php-jwt-master/src/BeforeValidException.php';
    include_once 'libs/php-jwt-master/src/ExpiredException.php';
    include_once 'libs/php-jwt-master/src/JWT.php';
    include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
    use \Firebase\JWT\JWT;

    // get data

    $data = json_decode(file_get_contents("php://input"));
    
    // get JWT
    $jwt = isset($data->jwt) ? $data->jwt : "";

    // decode jwt
    if($jwt){

        try{
            // decode JWT
            $decoded =JWT::decode($jwt,$key,array('HS256'));
            http_response_code(200);
            echo json_encode(array(
                'Message: ' => 'Access granted',
                'data' => $decoded->data
            ));
        }
        catch(Exception $e){
            http_response_code(401);
            echo json_encode(array(
                'Message: ' => 'Access Denied',
                'error' => $e->getMessage()
            ));
        }
    }
        else {
            http_response_code(401);
            echo json_encode(array(
                'Message: ' => 'Access Denied no jwt found'
            ));
    }       
            