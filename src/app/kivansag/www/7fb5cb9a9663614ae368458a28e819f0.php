<?php
/**	azert a krixkrax fajlnev, hogy ne legyen konnyu kitalalni hogy mi az index php.
        ugye ez a konyvtar a DocumentRoot, azaz "kivulrol" csakis az itt levo fajlokat lehet kozvetlenul elerni
         */
namespace app\kivansag\router;


// osztaly SPL-ek toltese
require_once("../lib/autoload.php");
chdir(__DIR__."/../../..");
require_once("lib/autoload.php");

\app\kivansag\user::hash("");	# be kell tolteni az osztalyt session_start elott, enelkul sikertelen a deszerializacio

// elfedjuk a kiszolgalo reszleteit
session_name("JSESSIONID");
session_start();
header("X-Powered-By: dotnet");
header("Server: IIS");
header("X-XSS-Protection: 1; mode=block");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
$ini = parse_ini_file("config/local.ini",1);


# csak a sajat context path engedelyezett!
$context = \app\kivansag\config::get("site")["context"];
if(!preg_match("~^".$context."([a-z0-9/_-]+)~i",$_SERVER["REQUEST_URI"],$ma))die("#invalid context#");


# ez lesz a "szep URL" eleresi utja. ne lehessen trukkozni szulokonytarba lepessel.
$path = explode("/",$pathStr=str_replace("..",".",trim($ma[1],'/')));

# template kezelo betoltese
$sm = new \Smarty;
$sm->setConfigDir(__DIR__."/../config");
$sm->setTemplateDir($TPLDIR=__DIR__."/../template");
$sm->setCompileDir($ini['dirs']['cache']);
$sm->setCacheDir($ini['dirs']['cache']);

# fuggvenyek regisztralasa, melyeket a template-ekben hasznalhatunk
\app\kivansag\smartyplugins::register($sm);

$sm->assign([
    "path" => $path,
    "context" => $context,
    "spath" => json_encode($path),
    "USER" => (array)$_SESSION['USER'],
]);

# HTTP GET/POST adatok. eleve a bemenetet módosítjuk XSS / SQL befecskendezés ellen
foreach( $_REQUEST as $k=>$v )
    $_REQUEST[$k] = @str_replace(['<','>',"'"],['&lt;','&gt;','`'],$v);

# ezekre tobbe nincs szuksegunk, veletlenul se hasznalja senki 8 ev mulva sem.
unset($_POST);unset($_GET);


# ha valamely template-hez PHP tartozik, futtatjuk
if(file_exists($php="$TPLDIR/$pathStr.php"))require_once($php);

# oldal megjelenitese
if(!$path[0]) $sm->display("home.htm");
elseif(file_exists($tpl="$TPLDIR/".$path[0].".htm")) $sm->display($tpl);
else $sm->display("404.htm");

