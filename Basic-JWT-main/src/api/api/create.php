<?php

    // headers
    header('Access-Control-Allow-Origin: *');
    header('content-type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers,Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 3600');

    // get db conn & obj
    include_once '../config/database.php';
    include_once '../object/student.php';

    $database =new Database();
    $db = $database->getConnection();

    $student = new Student($db);

    $result = $student->read();

    $num = $result->rowCount();
    $nm =$num + 1;

    // get POSTed data
    $data = json_decode(file_get_contents("php://input"));

    // is data valid?
    if(
        !empty($data->name) &&
        !empty($data->email) &&
        !empty($data->contact)
    ){
        // set values
        $student->id = $nm;
        $student->name = $data->name;
        $student->email = $data->email;
        $student->contact = $data->contact;
        $student->password = $data->password;
        
        // create
        if($student->create()){
            http_response_code(201);
            
            // tell status
            echo json_encode(array("Message: " => "Student added"));
        }else{
            http_response_code(503);
            
            echo json_encode(array("Message: " => "Unable to add syudent"));
        }
    }else{
        http_response_code(400);

        // tell data incomplete
        echo json_encode(array("Message: " => "incomplete data"));
    }
    // include '../../index.php';
     ?>