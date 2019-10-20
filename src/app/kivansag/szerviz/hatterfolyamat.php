<?php
namespace app\kivansag\szerviz;

require_once(__DIR__."/../lib/autoload.php");
require_once(__DIR__."/../../../lib/autoload.php");

declare(ticks=1);
 
$sig = new \zs\sighandler( function($signo) {
    printf("$signo szignalt kaptam\n");
}, range(1,31) );



while(1){
#    $db = \app\kivansag\db::instance();$most = $db->oneValue("Select now()");
    
    printf("TICK! ekkor: %s pidem: %d\n", $most, \getmypid() );
    sleep(3);
    
    if($sig->signal())break;
    
}


printf("\n\tszabalyos kilepes\n\n");
exit(0);
