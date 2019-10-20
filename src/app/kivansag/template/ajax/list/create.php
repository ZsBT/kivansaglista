<?php
namespace app\kivansag;
use \app\msga;

$USER = user::current();
if(!$USER || !\zs\nonce::check() ) http_response_code(403) and exit;

$wishlist = new wishlist;
$wishlist->felhasznalo = $USER->id;
foreach($_REQUEST as $k=>$v)
    $wishlist->{$k} = $v;


$ret=0;
try { 
    if( $ret=$wishlist->create() )
        msga::put("létrehozás sikeres",'success') and $USER->logger("lista #$ret létrehozása");
    else
        msga::put("létrehozás SIKERTELEN",'danger');
} catch (wishlistException $E) {
        msga::put("létrehozás SIKERTELEN: ".$E->getMessage(),'danger');
}

\zs\net\http::response()->end($ret);
