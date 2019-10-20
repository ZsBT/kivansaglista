<?php
//	felhasznalo torlese
namespace app\kivansag;
use \app\msga;

$U = user::current();

if($U->jog<user::JOG_ADMIN) http_response_code("403") and exit;
if(!\zs\nonce::check()) http_response_code(403) and exit;
if(!$id=0+@$_REQUEST['id']) http_response_code("400") and exit;	# ha hiányzik az ID paraméter

if($id == $U->id) msga::put("saját magad nem törölheted!",'danger');
elseif (db::instance()->exec("delete from felhasznalo where id=$id")) msga::put("felhasználó törölve",'success');
else msga::put("törlés sikertelen",'danger');

(\zs\net\http::response())->end(TRUE);
