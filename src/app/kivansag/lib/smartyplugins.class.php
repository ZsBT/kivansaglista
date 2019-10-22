<?php

namespace app\kivansag;

use \zs\net\http, \zs\nonce, \app\msga, \app\logger;

/** ezeket a fuggvenyeket lehet hasznalni a template-ekben */
class smartyplugins extends \app\smartyplugins {

    
    /** adatbazis lekerdezes */
    function function_db($parm, $tpl){
        (new logger)->info("DB lekerdezes ".json_encode($parm));
        if(isset($parm['oneValue']))
            return @db::instance()->oneValue($parm['oneValue']);
    }
    
    
    /** bongeszo atiranyitas */
    function function_redir($parm,$tpl){
      if($uri=$parm['uri']) header("Location: $uri") and exit;
      if($path=$parm['path']) header(config::get("site")['context']."/$path") and exit;
    }
    
    
    
    /** stackelt uzenetek betoltese, kiirasa */
    function function_msga($parm,$tpl){
        $ret = "";
        while($msga = msga::get() and list($msg,$class)=$msga)
          $ret.=sprintf("<p class='alert alert-%s'>%s</p>", $class, $msg);
        return $ret;
    }
    
    
    
    /** valtozo kiiratasa */
    function function_dump($parm,$tpl){
        if(isset($parm['var'])) return sprintf("<pre>%s</pre>", print_R($parm['var'],1));
    }
    
    

    /** megosztott kivansaglista */
    function function_kivansaglista($parm,$tpl){
        if(!$U=user::current())
            $tpl->assign("error","megosztott kívánságlista megtekintéséhez bejelentkezés szükséges.");
        else {
            $hash = @$_REQUEST['hash'];
            
            if(!preg_match('/^[0-9a-z]{4,80}$/',$hash) or !$lid=db::instance()->oneValue("select id from kivansaglista where linkhash='$hash'")) {
                $tpl->assign("error", "érvénytelen hivatkozás"); 
                $U->logger->warning("#{$U->id} felhasználó érvénytelen kívánságlistát akart nézni: $hash");
            } else {
                $U->logger->info("#{$U->id} felhasználó megnézte a #id kívánságlistát");
                $wishlist = new wishlist($lid);
                $user = new user($wishlist->felhasznalo);
                $wishes = $wishlist->wishes();
                foreach($wishes as $wish)
                    @$wishlist->osszeg += $wish->ar;
                
                $tpl->assign([
                    "kivansaglista" => $wishlist,
                    "felhasznalo"	=> $user,
                    "kivansagok"	=> $wishes,
                ]);
            }
        }
    }   
     
    
    /** bejelentkezes vizsgalata */
    function function_checkLogin($parm,$tpl){
        if( $nonce=nonce::check() and isset($_REQUEST['enter']) and $username=$_REQUEST['username'] and $password=$_REQUEST['password'] ){
            // bejelentkezesi kiserlet tortent
            
            if($_SESSION['loginhiba'] > config::get("site")['maxloginhiba'] ){
                // ilyenkor a DB-t mar nem is lassitjuk
                msga::put("túl sok sikertelen kísérlet",'danger');
                sleep(2);
                return;
            }
            
            // kliensbol a tarolt jelszo es a hozzafuzott nonce-bol kepzett hash erkezik
            if( $id=user::exists($username) and $user=user::read($id) and $password==$ph=hash('sha256',$user->jelszo.$nonce) ){
                if(!($user=user::read($id))->aktiv) msga::put("inaktív account"); else {
                    // sikeres azonositas
                    $_SESSION['USER'] = $user;
                    // atiranyitas kezdolapra
                    $user->logger->notice("#{$user->id} {$user->azonosito} bejelentkezett");
                    msga::put("sikeres bejelentkezés","success");
                    header("Location: ./");exit;
                }
            } else msga::put("hibás név vagy jelszó","danger");

            
            // jegyezzuk a sikertelenseg szamat
            @$_SESSION['loginhiba']++;
            
            // szopatjuk par masodpercig sikertelenseg eseten, hogy lassu legyen a brute-force tamadas.
            sleep(4);
            header("Location: ?fail");
            exit;
        }
        
    }
    
    
    
    
    /** nonce keszitese */
    function function_nonce($parm,$tpl){
        return \zs\nonce::create();
    }
    
    
    /** regisztralja ezen osztaly fuggvenyeit smarty pluginkent */
    public static function register($smarty){
        foreach( get_class_methods(__CLASS__) as $fun )
            if(preg_match("/^(function|block|compiler|modifier)_(.+)/", $fun, $ma)) 
                $smarty->registerPlugin($ma[1], $ma[2], [__CLASS__,$fun]);
    }
    
}

