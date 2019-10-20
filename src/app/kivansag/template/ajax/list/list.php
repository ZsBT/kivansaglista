<?php
//	kivansaglistak listazasa
namespace app\kivansag;

use \app\msga;

$USER = user::current();
if(!$USER) http_response_code(403) and exit;



(\zs\net\http::response())->end( 	// JSON valaszkent kuldjuk
    ["data"=>array_map(function($rec){	// humanusabb formaban ...
        $rec->letrehozva = substr($rec->letrehozva,0,16);
        $rec->ar = $rec->ar ? 0+$rec->ar : '-';
        return $rec;
    },db::instance()->allRow("select 
        kl.id, kl.nev, kl.letrehozva, kl.linkhash, sum(k.ar) ar
        from kivansaglista kl
        left join kivansag k on kl.id=k.lista
        where felhasznalo={$USER->id}
        group by kl.id,kl.nev,kl.letrehozva,kl.linkhash
        ")) ]	// ...az osszes rekordot
);

