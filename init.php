<?php

//define('DEV', true);
//
//if (DEV) {
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('memory_limit', '32M');
//}

define('ROOT_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']));

//function send_mail($to, $subject, $html) {
//    mail($to, $subject, $html, [
//        'From: ' . SMTP_FROM,
//        'Content-Type: text/html;charset=utf-8'
//    ]);
//}

function get_data($id) {
    $id = preg_replace('/[^0-9a-z]/', '', $id);
    $data = json_decode(file_get_contents(__DIR__ . "/data/$id.json"), true);
    return $data;
}

function save_data($data) {
    $r = file_put_contents(__DIR__ . '/data/' . $data['id'] . '.json', json_encode($data, JSON_PRETTY_PRINT));
//    if ($r === false) {
//        print_r(error_get_last());
//        die();
//    }
}

function get_image($i, $set) {
    static $files = null;

    if (!$files) {
        $files = glob(__DIR__ . '/assets/' . $set . '/*.*');
    }

    return 'assets/' . $set . '/' . basename($files[$i-1]);
}
