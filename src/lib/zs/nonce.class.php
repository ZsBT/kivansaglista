<?php
namespace zs;

/**
 *	egyszer használatos azonosító kezelése
 */
abstract class nonce {
    
  
  
  static public $datafile = "/dev/shm/zs-nonce.json";

  static protected $__nonces=[];
  static protected $__charset = 'abcdefghijkmnpqrstuvwxyz3456789ABCDEFGHJKLMNPQRSTUVWXYZ';


  static public function create($len=31,$value=1) {
    self::load();
    while($len--)
      @$new .= substr(self::$__charset, rand(0,strlen(self::$__charset)), 1);
    
    self::$__nonces[$new] = $value;
    self::save();
    return $new;
  }



  static public function check($nonce=false, $remove=true){
    if(!$nonce) $nonce = @$_REQUEST['nonce'];
    self::load();
    $val = @self::$__nonces[$nonce];
    if($remove) {
      unset(self::$__nonces[$nonce]);
      self::save();
    }
    return $val ? $nonce:FALSE;
  }


  static protected function load(){
    if( file_exists(self::$datafile) && ($stored=@json_decode(@file_get_contents(self::$datafile)))  )
      self::$__nonces = (array)$stored;
  }
  
  
  static protected function save(){
    umask(0000);
    return file_put_contents(self::$datafile, @json_encode(self::$__nonces) );
  }
  

}

?>