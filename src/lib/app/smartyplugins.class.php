<?php

namespace app;

class smartyplugins {

    
    
    /** bongeszo atiranyitas */
    function function_redir($parm,$tpl){
      if($uri=$parm['uri']) header("Location: $uri") and exit;
      if($path=$parm['path']) header(config::get("site")['context']."/$path") and exit;
    }
    
    
    
    /** stackelt uzenetek betoltese, kiirasa */
    function function_msga($parm,$tpl){
        $ret = "";
        while($msga = msga::get() and list($msg,$class)=$msga)
          $ret.=sprintf("<p class='alert alert-%s'>%s</p>", $class, self::function_f(['k'=>$msg],0));
        return $ret;
    }
    
    
    
    
    
    /** regisztralja ezen osztaly fuggvenyeit smarty pluginkent */
    public static function register($smarty){
        foreach( get_class_methods(__CLASS__) as $fun )
            if(preg_match("/^(function|block|compiler|modifier)_(.+)/", $fun, $ma)) 
                $smarty->registerPlugin($ma[1], $ma[2], [__CLASS__,$fun]);
    }
    
}

