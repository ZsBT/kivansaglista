<?php
/**	egyszeru dummy valasz */

namespace app\kivansag;

$msg = [];
try {
    $db = db::instance();
    $time = $db->oneValue("select now()");
    $msg[] = "DB elérhető";
} catch (\Exception $E) {
    $msg[] = "DB nem elérhető";
    $time = "STUB";
}

$ret = ['pong'=>[
    "status" => $msg,
    "servertime" =>  $time,
]];

(\zs\net\http::response())->end($ret);
