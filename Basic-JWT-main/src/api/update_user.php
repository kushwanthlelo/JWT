<?php
    // headers
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Max-Age: 3600');

    // files for decoding jwt and db conn
    include_once 'config/core.php';
    include_once 'libs/php-jwt-master/src/ExpiredException.php'; 
    include_once 'libs/php-jwt-master/src/BeforeValidException.php';
    include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
    include_once 'libs/php-jwt-master/src/JWT.php';
    include_once 'config/database.php';
    include_once 'object/student.php';
    use Firebase\JWT\JWT;

    // db conn
    $database = new Database();
    $db = $database->getConnection();

    // init student obj
    $student = new Student($db);

    // get given jwt and posted data
    $data = json_decode(file_get_contents("php://input"));
    $jwt = isset($data->jwt) ? $data->jwt : "";

    // decode jwt data
    if($jwt){
        
        try{
            $decoded = JWT::decode($jwt, $key, array('HS256'));
            // set prop values to student
            $student->name = $data->name;
            $student->email = $data->email;
            $student->contact = $data->contact;
            $student->password = $data->password;
            $student->id = $decoded->data->id;

            // update student
            if($student->update()){
                // regnrt jwt
                $token = array(
                    "iat" => $issued_at,
                    "exp" => $expiration_time,
                    "iss" => $issuer,
                    "data" => array(
                        "id" => $student->id,
                        "name" => $student->name,
                        "email" => $student->email,
                        "contact" => $student->contact
                    )
                    );
                $jwt = JWT::encode($token,$key);

                http_response_code(200);

                echo json_encode(array(
                    "message" => "Update success",
                    "jwt" => $jwt
                ));

            }
            else{
                http_response_code(401);
                echo jsom_encode(array("message: " => "Can't update Student"));
            }

        }catch(Exception $e){
            http_response_code(401);
            echo json_encode(array(
                'Message: ' => 'Access Denied',
                'error' => $e->getMessage()
            ));
        }
    }else{
        http_response_code(401);
        echo json_encode(array(
            'Message: ' => 'Access Denied not jwt found'
        ));
    }
    