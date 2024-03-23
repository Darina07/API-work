<?php

function handleCors($config) {

    if(php_sapi_name() == 'cli') return false;

    if($config->get("security.cors.enabled") == false ) return false;


    if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $config->get("security.cors.allowed_origins"))) {

        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    return false;
}
