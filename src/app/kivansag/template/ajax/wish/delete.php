<?php
namespace app\kivansag;

$USER = user::current();
if(!$USER || !\zs\nonce::check(0,0) ) http_response_code(403) and exit;
if(!$id = 0+$_REQUEST['id']) http_response_code(400) and exit;	// ha nincs update-elni valo ID

$wish = new wish($id);
$wishlist = new wishlist($wish->lista);
if($wishlist->felhasznalo != $USER->id)http_response_code(403) and exit;	// nem a sajat listaja!

$ret=0;
try { 
    if( $ret=$wish->delete() )
        msga::put("törlés sikeres",'success') and $USER->logger->notice(__CLASS__." #{$id} törölve");
    else
        msga::put("törlés SIKERTELEN",'danger');
} catch (wishlistException $E) {
        msga::put("törlés SIKERTELEN: ".$E->getMessage(),'danger');
}

\zs\net\http::response()->end($ret);


class msga {
    static function put($msg){}
}
