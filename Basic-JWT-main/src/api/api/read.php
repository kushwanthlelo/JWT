<?php
    // headers
    header('Access-Control-Allow-Origin: *');
    header('content-type: application/json');

    include_once '../config/database.php';
    include_once '../object/student.php';

    // instantiate db & conn
    $database = new Database();
    $db = $database->getConnection();

    // instantiate post obj
    $post = new Student($db);

    $result = $post->read();

    $num = $result->rowCount();

    // check post
    if($num>0){
        // post arr
        $post_arr = array();
        $post_arr["data"] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $post_item = array(
                "id" => $id,
                "name" => $name,
                "email" => $email,
                "contact" => $contact,
                "password" => $password
            );
            // set response code - 200 OK
            http_response_code(200);

            // push to data
            array_push($post_arr["data"], $post_item);
        }

        
        // conv to json
        echo json_encode($post_arr);
    }else{

        // set response code - 404 Not found
        http_response_code(404);

        // output of no post
        echo json_encode(
            array('message' => 'No posts')
        );
    }

