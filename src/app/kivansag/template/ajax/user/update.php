<?php
//	felhasznalo torlese
namespace app\kivansag;
use \app\msga;

$U = user::current();
if($U->jog<user::JOG_ADMIN) http_response_code(403) and exit;
if(!\zs\nonce::check()) http_response_code(403) and exit;
if(!$_REQUEST['id']) http_response_code(400) and exit;

// modositando felhasznalo
$user = new user(0+$_REQUEST['id']);

if($U->id == $user->id) msga::put("saját magad nem módosíthatod!","danger"); else {

    // atirjuk az adatait
    $_REQUEST['aktiv'] = 0+$_REQUEST['aktiv'];
    foreach($_REQUEST as $k=>$v)switch($k){
        case "jelszo":	# csak akkor mentjuk ha meg van adva uj
            if(!$v)break;
        default:
            $user->{$k} = $v;
    }

    try {
        $db = db::instance();
        $db->begin();
        if( $user->update() ){
            $db->exec("update felhasznalo set modositva=now() where id={$user->id}");
            msga::put($msg="felhasználó módosítása sikeres",'success');
        } else msga::put($msg="felhasználó módosítása SIKERTELEN",'danger');
        $db->commit();
    } catch (userException $E) {
        msga::put($msg="felhasználó módosítás sikertelen: ".$E->getMessage(),'danger');
    }

    $user->logger->notice("admin #{$U->id} módosította a #{$user->id} felhasználót");
}

(\zs\net\http::response())->end(TRUE);
