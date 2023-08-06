<?php
    // show error reporting
    error_reporting(E_ALL);

    // set time
    date_default_timezone_set('Asia/Calcutta');

    // vars for jwt
    $key ="good_key";
    $issued_at = time();
    $expiration_time = $issued_at +(60*60);
    // $issuer = "http://localhost/CodeOfaNinja/RestApiAuthLevel1/";
    $issuer = "http://localhost/code/src/api/";
    ?>