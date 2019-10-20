<?php

namespace zs\net;

/** HTTP metodusok, foleg CRUD tamogatasara */
class http {

    function response(){
        return new httpResponse;
    }
    
    
    /** egyszeruen csak visszaad HTML formatumban egy valtozot */
    function vardump($var){
        return sprintf("<pre>VARDUMP:\n%s</pre>", print_R($var,1) );
    }
}


/** HTTP valasz osztaly*/
class httpResponse {
    /** valaszkod */
    public $code = 200;
    
    
    /** MIME tipus */
    public $contentType = "text/plain";
    

    /** tovabbi fejlecek */
    public $headers = [];
    
    /** ki voltak-e irva mar a fejlecek */
    private $headers_written = 0; 
    
    /** reszleges valasz kuldese */
    function write( string &$str){
        if(!$this->headers_written){
            http_response_code($this->code);
            $this->headers_written = 1;
            foreach($this->headers as $key=>$val)
                header($key,$val);
        }
        print($str);
        return true;
    }
    
    /** valasz befejezese. nem skalaris adat eseten JSON a valasz
     *	@param mixed $data opcionalisan valaszt is kuldhetunk
     */
    function end( $data=NULL ){
        if(is_scalar($data)) {
            header("Content-Type: {$this->contentType}");
            $this->write($data);
        } else {
            header("Content-Type: text/json");
            $this->write(@json_encode($data));
        }
        exit;
    }
}
