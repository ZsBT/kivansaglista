<?php

namespace app\kivansag;

/** INI fajl kezelese */
class config {
    
    /** egy INI betoltese */ 
    function get($name){
        if(@self::$__cache[$name])return self::$__cache[$name];
        
        if(!file_exists($fn=sprintf("%s/../config/%s.ini", __DIR__, $name)))
            return false;
        
        return $__cache[$name] = @parse_ini_file($fn,false);
    }
    
    
    private static $__cache=[];
}
