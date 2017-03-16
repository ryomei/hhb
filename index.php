<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 72000');
header('Access-Control-Allow-Methods: PUT, HEITOR');
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == "OPTIONS"){
    header(200);
}else{
    require_once 'api' . DIRECTORY_SEPARATOR . 'HHB.php';
    \api\HHB::init();
}
