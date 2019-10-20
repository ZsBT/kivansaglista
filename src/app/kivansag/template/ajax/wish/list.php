<?php
//	kivansagok listazasa
namespace app\kivansag;

use \app\msga;

$USER = user::current();
if(!$USER) http_response_code(403) and exit;
if(!$listID = 0+$_REQUEST['listID']) http_response_code(400) and exit;

$wishlist = new wishlist($listID);
if(!$wishlist or $USER->id!=$wishlist->felhasznalo) http_response_code(403) and exit;

(\zs\net\http::response())->end( 	// JSON valaszkent kuldjuk
    ["data"=>array_map(function($rec){	// humanusabb formaban ...
        $rec->letrehozva = substr($rec->letrehozva,0,16);
        return $rec;
    },db::instance()->allRow("select * from kivansag where lista=$listID")) ]	// ...az osszes rekordot
);

