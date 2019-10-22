<?php
namespace app;

/** stackelt uzenetek rogzitese sessionben.
 * ezeket a HTML feluleten hasznaljuk, hogy a lap ujratoltesekor megjelenitsuk
 */
class msga {

    /** uzenet hozzaadasa */
    function put($msg, $class="primary"){
        @$_SESSION['MSGA'][] = [$msg,$class];
        (new logger)->debug("msga: ".json_encode($_SESSION['MSGA']));
    }
    
    /** legregebbi uzenet kiolvasasa */
    function get(){
        return @$_SESSION['MSGA'] ? array_shift($_SESSION['MSGA']) : false;
    }
    

}
