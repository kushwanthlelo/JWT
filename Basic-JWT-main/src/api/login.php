<?php

    // headers
    header("Access-Control-Allow-Origin: http://localhost/api/");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers:content-type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Access-Control-Max-Age: 3600");
    header("Content-Type:application/json; charset=UTF-8");

    //files for conn to db
    include_once 'config/database.php';
    include_once 'object/student.php';

    // get db conn
    $database = new Database();
    $db = $database->getConnection();

    // init student object
    $student = new Student($db);
    
    // chk for mail existence
    // get inout  data

    $data = json_decode(file_get_contents("php://input"));

    // assign student data
    $student->email = $data->email;
    $email_exists = $student->emailexists();
    // $hash_password = password_hash($data->password, PASSWORD_BCRYPT);
    // $password_hash = password_hash($data->password, PASSWORD_BCRYPT);
    
    // files for jwt
    include_once 'config/core.php';
    include_once 'libs/php-jwt-master/src/BeforeValidException.php';
    include_once 'libs/php-jwt-master/src/ExpiredException.php';
    include_once 'libs/php-jwt-master/src/JWT.php';
    include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
    use \Firebase\JWT\JWT;
    
    // generate jwt 
    // chk for email and password correctness
    if($email_exists && password_verify($data->password, $student->password)){
        $token =array(
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
        http_response_code(200);

        // generate JWT
        $jwt = JWT::encode($token,$key);
        echo json_encode(
            array(
                "message: " => "Successful Login",
                "jwt" => $jwt
            )
            );
    }
    // else login fail
    else{
        if($email_exists){
            echo json_encode(array('Message' => ' email exists but incorrect password'));
        }
        else{

            http_response_code(401);
            echo json_encode(array("Message: " =>"Login Failed no email found",
                                    "email" => $data->email));
        }
    }