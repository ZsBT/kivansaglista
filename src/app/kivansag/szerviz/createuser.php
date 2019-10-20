#!/usr/local/bin/php
<?php
/**	admin felhasznalo letrehozasa. */
namespace app\kivansag;


if(count($arg = $_SERVER['argv']) < 4) {
    printf("\n\nHasználat:\n\n%s <azonosító> <jelszó> <teljes név>\n\n", basename($arg[0])); 
    exit(1);
} else {

    require_once(__DIR__."/../lib/autoload.php");
    require_once(__DIR__."/../../../lib/autoload.php");
    
    
    $db = db::instance();

    $user = new user;
    $user->jog = user::JOG_ADMIN;

    array_shift($arg);	# nulladik parameter a fajl neve, nincs ra szukseg


    $user->azonosito = @array_shift($arg);
    $user->jelszo = user::hash(@array_shift($arg));
    $user->nev = implode(" ",$arg);

    try {
        $succ = $user->create();
    } catch (\Exception $E) {
        printf("létrehozás sikertelen: %s\n", $E->getMessage());
    }

    exit(0);
}
