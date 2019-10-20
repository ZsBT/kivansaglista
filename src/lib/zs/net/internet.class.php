<?php	/*

  internet hasznalat
  
  Zsombor	2017-03
  
  
  */

namespace zs\net;

class internet {

   const DEFAULT_PROXYENV = 'http_proxy';
   public $VERBOSE = FALSE;


   // rogton az elejen probaljuk megtudakolni a proxy-t
   function __construct($proxyURL=NULL){
     if(!$proxyURL) $proxyURL = getenv(self::DEFAULT_PROXYENV);
     if($proxyURL)$this->setProxyURL($proxyURL);
   }
   
   // proxy megadasa
   public function setProxy($host,$port){ return $this->proxy = "$host:$port";}
   
   // proxy nev/jelszo megadasa
   public function setAuth($user,$pass){ return $this->auth = ("$user:$pass");}
   
   // proxy megadasa http://nev:jelszo@host:port formatumbol
   public function setProxyURL($proxyURL){
     if(!preg_match("~http://(.+):(.+)@(.+):([^/]+)~", $proxyURL, $ma))
         throw new \Exception("hibas proxy url: $proxyURL");
     
     list(,$user,$pass,$host,$port) = $ma;
     $this->setAuth($user,$pass);
     return $this->setproxy($host,$port);
   }
   

   // proxy allitasa kornyezeti valtozobol
   public function setProxyEnv($envVar=NULL){
     if(!$envVar)$envVar = self::DEFAULT_PROXYENV;
     return $this->setProxyURL(getenv($envVar));
   }
   
   
   // HTTP GET inditasa
   public function get($uri=NULL){
     if(!$uri)$uri = $this->uri;
     if(!$uri)throw new \Exception('no $uri given');
     
     $ch = \curl_init();
     \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
     if($this->proxy) \curl_setopt($ch, CURLOPT_PROXY, $this->proxy );
     if($this->auth) \curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->auth);
     \curl_setopt($ch, CURLOPT_HEADER, false);
     \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
     \curl_setopt($ch, CURLOPT_URL, $uri);
     \curl_setopt($ch, CURLOPT_REFERER, $uri);
     \curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
     $result = \curl_exec($ch);
     \curl_close($ch);
     return $result;
   }
   
   // HTTP POST inditasa
   public function post($data=[],$uri=NULL){
     if(!$uri)$uri = $this->uri;
     if(!$uri)throw new \Exception('no $uri given');
     
     $ch = \curl_init();
     \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
     if($this->proxy) \curl_setopt($ch, CURLOPT_PROXY, $this->proxy );
     if($this->auth) \curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->auth);
     \curl_setopt($ch, CURLOPT_HEADER, false);
     \curl_setopt($ch, CURLOPT_POST, true);
     \curl_setopt($ch, CURLOPT_VERBOSE, $this->VERBOSE);
     \curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
     \curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
     \curl_setopt($ch, CURLOPT_URL, $uri);
     \curl_setopt($ch, CURLOPT_REFERER, $uri);
     \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     $result = \curl_exec($ch);
     $this->last_error = \curl_error($ch);
     $this->last_response_code = \curl_getinfo($ch,CURLINFO_RESPONSE_CODE);
     \curl_close($ch);
     return $result;
   }
   
   private $proxy, $auth;
}

