<?php
namespace app\kivansag;

/** adatbazis tamogato osztaly */
class db {

    /** db peldany, lehet korabban letrehozott is */
    function instance(){
        return self::$_instance ?: self::$_instance = self::newinstance();
    }
    private static $_instance;

    /** uj db peldany */
    function newinstance(){
        $db = new \zs\db\pdo(parse_ini_file("config/local.ini",1)['db']['dsn']);
        return $db;
    }
    
}

