<?php
namespace app\kivansag;
use \app\msga;

$USER = user::current();
if(!$USER || !\zs\nonce::check() ) http_response_code(403) and exit;
if(!$id = 0+$_REQUEST['id']) http_response_code(400) and exit;	// ha nincs update-elni valo ID

$wishlist = new wishlist($id);
if($wishlist->felhasznalo != $USER->id)http_response_code(403) and exit;	// nem a sajat listaja!

$wishlist->nev = $_REQUEST['nev'];

$ret=0;
try { 
    if( $ret=$wishlist->update() )
        msga::put("módosítás sikeres",'success') and $USER->logger("lista #$ret módosítása");
    else
        msga::put("módosítás SIKERTELEN",'danger');
} catch (wishlistException $E) {
        msga::put("módosítás SIKERTELEN: ".$E->getMessage(),'danger');
}

\zs\net\http::response()->end($ret);
