<?php
namespace app\kivansag;

/** adatbazis tamogato osztaly */
class db {

    /** db peldany, de lehet statikusan memoriaban megorzott
     *	@return \zs\db\PDO	adatbazis peldany
    */
    function instance(): \zs\db\pdo {
        return self::$_instance ?: self::$_instance = self::newinstance();
    }
    private static $_instance;

    /** uj db peldany.
     *	@return \zs\db\PDO	adatbazis peldany
     */
    function newinstance(): \zs\db\PDO {
        $db = new \zs\db\pdo(parse_ini_file("config/local.ini",1)['db']['dsn']);
        return $db;
    }
    
}

