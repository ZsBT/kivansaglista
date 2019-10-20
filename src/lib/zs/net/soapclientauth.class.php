<?php	/*

  osztály ami basic http auth segítségével név/jelszó párossal hívja meg a webszervízt
  
  */
 

namespace zs\net;

class SoapClientAuth extends \SoapClient {


    function __construct($wsdl, $user, $pass, $soapOptions = [] ){

        $context = \stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

/*
            'trace' => 1, 
            'exceptions' => true, 
            'cache_wsdl' => WSDL_CACHE_NONE, 
*/
        
        parent::__construct($wsdl, array_merge($soapOptions,[
            'stream_context'	=>$context,
            'soap_version' => SOAP_1_1,
            'login'	=>$user,
            'password'	=>$pass,
        ]));
    }
}
