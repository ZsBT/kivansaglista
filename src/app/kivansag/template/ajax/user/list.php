<?php
//	felhasznalok listazasa
namespace app\kivansag;

$U = user::current();

if($U->jog<user::JOG_ADMIN) http_response_code("403") and exit;



(\zs\net\http::response())->end( 	// JSON valaszkent kuldjuk
    ["data"=>array_map(function($rec){	// humanusabb formaban ...
        $rec->aktivH = $rec->aktiv ? "aktív":"inaktív";
        $rec->letrehozva = substr($rec->letrehozva,0,16);
        $rec->modositva = substr($rec->modositva,0,16);
        switch($rec->jog){
            case user::JOG_USER:
                $rec->jogH = "felhasználó";
                break;
            case user::JOG_ADMIN:
                $rec->jogH = "admin";
                break;
        }
        return $rec;
    },db::instance()->allRow("select * from felhasznalo")) ]	// ...az osszes rekordot
);

