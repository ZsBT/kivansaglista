<?php
namespace app\kivansag;

$USER = user::current();
if(!$USER || !\zs\nonce::check(0,0) ) http_response_code(403) and exit;
if(!$id = 0+$_REQUEST['id']) http_response_code(400) and exit;	// ha nincs update-elni valo ID

$wish = new wish($id);
$wishlist = new wishlist($wish->lista);
if($wishlist->felhasznalo != $USER->id)http_response_code(403) and exit;	// nem a sajat listaja!

$wish->nev = $_REQUEST['nev'];
$wish->ar = $_REQUEST['ar'];

$ret=0;
try { 
    if( $wish->update() )
        $ret=['siker'=>"módosítás sikeres"] and $USER->logger->notice("kívánság #{$wish->id} módosítása");
    else
        $ret=['hiba'=>"módosítás SIKERTELEN"];
} catch (\Exception $E) {
        $ret=['hiba'=>$E->getMessage()];
}

\zs\net\http::response()->end($ret);
