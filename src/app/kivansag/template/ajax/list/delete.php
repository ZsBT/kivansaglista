<?php
namespace app\kivansag;
use \app\msga;

$USER = user::current();
if(!$USER || !\zs\nonce::check() ) http_response_code(403) and exit;
if(!$id = 0+$_REQUEST['id']) http_response_code(400) and exit;	// ha nincs update-elni valo ID

$wishlist = new wishlist($id);
if($wishlist->felhasznalo != $USER->id)http_response_code(403) and exit;	// nem a sajat listaja!

$ret=0;
try { 
    if( $ret=$wishlist->delete() )
        msga::put("törlés sikeres",'success') and $USER->logger->notice("lista #{$wishlist->id} törölve");
    else
        msga::put("törlés SIKERTELEN",'danger');
} catch (wishlistException $E) {
        msga::put("törlés SIKERTELEN: ".$E->getMessage(),'danger');
}

\zs\net\http::response()->end($ret);
