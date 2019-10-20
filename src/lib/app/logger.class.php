<?php

namespace app;

/** naplozas */
class logger extends \Psr\Log\AbstractLogger {

    public function log($level, $message, array $context = array()){
        $msg = __NAMESPACE__.".$level: $message";
        if(count($context))$msg.=" ".json_encode($context);
        file_put_contents("php://stdout", "$msg\n", FILE_APPEND);
    }
}
