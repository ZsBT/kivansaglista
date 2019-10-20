<?php
namespace zs;
/**
 *	osztálybetöltő. 
 *	elég ezt inklúdolni és máris be fogja tölteni a használni kívánt php osztályt.
 */


\spl_autoload_register( function($class){
    
    if(file_exists($fn=__DIR__.'/..'.strtolower(str_replace("\\","/","/$class.class.php"))))
        require_once($fn) ;#else echo "$fn nincs\n";
    
    if(file_exists($fn=__DIR__.'/..'.(str_replace("\\","/","/$class.class.php"))))
        require_once($fn) ;#else echo "$fn nincs\n";
});

