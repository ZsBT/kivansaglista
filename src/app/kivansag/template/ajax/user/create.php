<?php
//	felhasznalo torlese
namespace app\kivansag;
use \app\msga;

$U = user::current();
if($U->jog<user::JOG_ADMIN) http_response_code(403) and exit;
if(!\zs\nonce::check()) http_response_code(403) and exit;
if($_REQUEST['id']) http_response_code(400) and exit;

// uj felhasznalo peldany
$user = new user;


// atirjuk az adatait
$_REQUEST['aktiv'] = 0+$_REQUEST['aktiv'];
foreach($_REQUEST as $k=>$v)switch($k){
    case "jelszo":	# csak akkor mentjuk ha meg van adva uj
        if(!$v)break;
    default:
        $user->{$k} = $v;
}

try {
    if( $newid=$user->create() ){
        msga::put($msg="felhasználó létrehozása sikeres",'success');
    } else msga::put($msg="felhasználó létrehozása SIKERTELEN",'danger');
} catch (userException $E) {
    msga::put($msg="felhasználó létrehozása sikertelen: ".$E->getMessage(),'danger');
}

$user->logger->notice("admin #{$U->id} létrehozta a #{$newid} felhasználót");

(\zs\net\http::response())->end(TRUE);
