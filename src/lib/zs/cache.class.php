<?php
namespace zs;

/**
 *	egyszerű objektum-orientált cache  
 */
class cache {
    
  
  /**
  *	konstruktor
  *	@param int $TTL élettartam másodpercben (opcionális)
  *	@param string $PREFIX fájlnév kezdete (opcionális)
  *	@param string $DIR használni kívánt könyvtár helye (opcionális)
  *	@throws cacheException ha nem lehet létrehozni a cache könyvtárát
  **/
  function __construct($TTL=60,$PREFIX="x",$DIR=NULL){
    $this->DIR = $DIR ?: sys_get_temp_dir()."/zs-cache";
    $this->PREFIX = $PREFIX;
    $this->TTL = $TTL;
    
    $this->Salt = __FILE__;
    
    if(!file_exists($this->DIR)){
      if( !mkdir($this->DIR,0777,true))	
        throw new cacheException("Cannot create directory {$this->DIR}", 510);
      if(!file_put_contents("{$this->DIR}/CACHEDIR.TAG", "Signature: 8a477f597d28d172789f06886806bc55\n# created by ".__FILE__."\n" ))
        throw new cacheException("Cannot write directory {$this->DIR}", 511);
      chmod($this->DIR, 0777);
      chmod("{$this->DIR}/CACHEDIR.TAG", 0444 );
    }
    
  }
  

  /**
  *	cache-elni szánt adat írása
  *
  *	@param $key kulcs
  *	@param $data írni szánt adat
  *	@param $TTL opcionális élettartam
  *	@return int kiírt bájtok száma
  **/
  public function put($key,&$data,$TTL=NULL)	//
  {
    $ob = new cacheElement($key, $TTL ? $TTL : $this->TTL, $data );
    $r = file_put_contents($ffn=$this->fnbykey($key), @serialize($ob));
    chmod($ffn, 0666);
    return $r;
  }

  
  /**
  *	cache adat olvasása
  *	@param string $key kulcs
  *	@param fun $callable opcionális metódus ami akkor fut le ha nincs cache-elt adat. paraméterként megkapja a kulcsot
  *	@param int $TTL 
  *	@return mixed cache-elt adat
  **/
  public function get($key, $callable=NULL, $TTL=NULL )
  {
    $ob = @unserialize(@file_get_contents($this->fnbykey($key)));
    if($ob && ($ob->Expiry > time() ))return $ob->Data;
    
    if($callable){
      $data = $callable($key,$ob);
      if( ($data===NULL) && $ob )return $ob->Data;	//
      $this->put($key,$data,$TTL);
      return $data;
    }
    return NULL;
  }
  
  
  /**
  *	cache adat lejáratása
  *
  *	@param $key kulcs
  *	@return int kiírt byte-ok száma
  **/
  public function expire($key){	//
    $ob = @unserialize(@file_get_contents($this->fnbykey($key)));
    $ob->Expiry = time()-1;
    return file_put_contents($this->fnbykey($key), @serialize($ob) );
  }
  

  /**
  *	bejegyzés törlése
  *	@param $key kulcs
  *	@return bool sikeres
  **/
  public function del($key){	//
    if(!file_exists($fn=$this->fnbykey($key))) return true;
    return unlink($fn);
  }
  
  
  /**
  *	karbantartás: lejárt objektumok törlése
  *
  *	@param bool $force nem lejártak törlése is
  *	@return int torolt elemek szama
  **/
  public function cleanup($force=FALSE)	//
  {
    $ret = 0;
    foreach( glob(sprintf("%s/%s-*.ob",$this->DIR,$this->PREFIX)) as $fn ){
      if(!$ob = @unserialize(@file_get_contents($fn)))unlink($fn);else 
        if($force || ($ob->Expiry < time()) )
          $ret+=unlink($fn);
    }
    return $ret;
  }


  /**
   *	fajlnev a kulcs alapjan
   *	@return string fajlnev
   */
  protected function fnbykey($key)	{	//
    return sprintf("%s/%s-%s.ob", $this->DIR,$this->PREFIX, md5( $this->Salt . serialize($key)) );
  }


}


/**
 *	cache objektum peldany
 *	@used-by cache
 */
class cacheElement { 
  
  /**
   *	@var int $Created	letrehozva unix_epoch
   *	@var int $Expiry	lejar unix_epoch
   *	@var string $Key	kulcs
   *	@var $Data 	cache-elt adat
   */
  public $Created, $Expiry, $Key, $Data; 
  
  /**
   *	@param string $Key	kulcs
   *	@param int $TTL	lejarati ido masodpercben
   *	@param $Data	tarolni kivant adat
   */
  function __construct(&$Key,$TTL,&$Data){
    $this->Created = time();
    $this->Key = &$Key;
    $this->Expiry = time()+$TTL;
    $this->Data = $Data;
  }
  
}


/**
 *	kivetel
 *	@used-by cache
 */
class cacheException extends \Exception { }

?>
