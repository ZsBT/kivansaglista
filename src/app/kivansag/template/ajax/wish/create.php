<?php
namespace app\kivansag;


$USER = user::current();
if(!$USER || !\zs\nonce::check(0,0) ) http_response_code(403) and exit;

$wish = new wish;
$wish->felhasznalo = $USER->id;
foreach($_REQUEST as $k=>$v)
    $wish->{$k} = $v;

$wishlist = new wishlist($wish->lista);
if($USER->id != $wishlist->felhasznalo)	{
    $USER->logger->warning("a lista#{$wishlist->id} nem a felhasználó#{$USER->id} tulajdona!");
    http_response_code(403);
    exit;
}


$ret=0;
try { 
    if( $ret=$wish->create() )
        msga::put("létrehozás sikeres",'success') and $USER->logger("kívánság #$ret létrehozása");
    else
        msga::put("létrehozás SIKERTELEN",'danger');
} catch (wishException $E) {
        msga::put("létrehozás SIKERTELEN: ".$E->getMessage(),'danger');
}

\zs\net\http::response()->end($ret);


// itt nem toltjuk ujra az oldalt, azaz ezeket az uzeneteket nem irjuk ki a HTML oldalra
class msga {
    static function put($msg){
    }
}

