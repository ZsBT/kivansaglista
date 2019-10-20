<?php	/*

    email küldés - Zsombor 2017-04

    Pelda:
    



<?php
date_default_timezone_set("Europe/Budapest");

$fn = 'sql/roszke-tegnap.csv';

require_once("phplib/zs/email.class.php");

use \zs\email;

email::$FELADO = 'mir-automata@bah.b-m.hu';

$em = new email;
$em->EMAILCIM = 'zsombor@bah.b-m.hu';
$em->TARGY = 'próba levél ';
$em->TORZS_TXT = 'próba tartalom';
$em->TORZS_HTML = '<html><meta charset=utf-8><h2>próba tartalom</h2></html>';
$em->MELLEKLETEK[] = $fn;

$r = $em->kuld();

var_dump($r);






    */


namespace zs\net;

class email {

    public $EMAILCIM, $TARGY, $TORZS_TXT, $TORZS_HTML, $MELLEKLETEK=[], $FILENEVEK=[];
    
    public static $FELADO = "undefined-felado <undefined-felado@bah.b-m.hu>";
    public static $XMailer;
    
    private $boundary;
    
    function __construct(){
        $this->boundary = sprintf("----=_%s_boundary", uniqid());
    }
    
    
    function kuld(){
        if(!self::$XMailer)self::$XMailer = sprintf("%s@%s", __CLASS__, "BMH" );
    
        if(!strlen(self::$FELADO)*strlen($this->EMAILCIM)*strlen($this->TARGY))
            throw new \Exception("FELADO, EMAILCIM vagy TARGY hianyzik");
            
        $HA = [
            "From"=>	self::$FELADO,
            "Content-Type"=>	sprintf('multipart/mixed; boundary="%s"', $this->boundary),
            "X-Mailer"=>	self::$XMailer,
#            "To"=>	$this->EMAILCIM,
            "MIME-Version"=>	"1.0",
            "Date"=>	date("r"),
        ];
        
        $HS = '';
        foreach($HA as $k=>$v)
            $HS.="$k: $v\r\n";
        
        $B = &$this->boundary;
        
        $TORZS = '';
        if($this->TORZS_TXT)
            $TORZS.= "--$B\nContent-Type: text/plain; charset=\"utf-8\"\nContent-Transfer-Encoding: binary\n\n{$this->TORZS_TXT}\n\n";
        
        if($this->TORZS_HTML)
            $TORZS.= "--$B\nContent-Type: text/html; charset=\"utf-8\"\nContent-Transfer-Encoding: binary\n\n{$this->TORZS_HTML}\n\n";
        
        foreach($this->MELLEKLETEK as $mi=>$mellfn)if(file_exists($mellfn)){
            $bfn = isset($this->FAJLNEVEK[$mi]) ? $this->FAJLNEVEK[$mi] : basename($mellfn);
            $ct = mime_content_type($mellfn);
            $TORZS.=sprintf("--%s\nContent-Type: %s; name=\"%s\""
                ."\nContent-Transfer-Encoding: base64"
                ."\nContent-Disposition: attachment; filename=\"%s\""
                ."\n\n"
                , $B, $ct, $bfn, $bfn
            );
            $TORZS.= @chunk_split(@base64_encode(@file_get_contents($mellfn)),76,"\n");
            $TORZS.="\n\n";
        }
        
        $TORZS.="--$B--\n";
        
        $addparms = '';
        if(preg_match('/([^<]+@[^>]+)/i',self::$FELADO,$ma))
            $addparms .= " -f ".$ma[1];
        
        return mail($this->EMAILCIM
            , sprintf("=?UTF-8?B?%s?=", base64_encode($this->TARGY))
            , $TORZS, $HS, $addparms
        );

    }
    
    
}

