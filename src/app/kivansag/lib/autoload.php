<?php
namespace app\kivansag;
/**
 *	osztálybetöltő. 
 *	elég ezt inklúdolni és máris be fogja tölteni a használni kívánt php osztályt.
 */


\spl_autoload_register( function($class){
    if(file_exists($fn=sprintf("%s/%s.class.php", __DIR__, str_replace(__NAMESPACE__."\\","",$class))))
        require_once($fn) ;#else echo "$fn nincs ($class) \n";
});

