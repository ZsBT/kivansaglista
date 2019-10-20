<?php
namespace app\kivansag;

$USER = user::current();
if(!$USER || !\zs\nonce::check(0,0) ) http_response_code(403) and exit;
if(!$id = 0+$_REQUEST['id']) http_request_code(400) and exit;

$wishlist = new wishlist($id);
if($wishlist->felhasznalo != $USER->id) http_response_code(403) and exit;


\zs\net\http::response()->end( ['newhash'=>$wishlist->genlinkhash()] );
