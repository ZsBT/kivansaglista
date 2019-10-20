<?php

namespace zs\db;

/**
*	adatbazis hasznalata http hivason keresztul. a tuloldalon persze kezelni kell es JSON-kent visszaadni az eredmenyt.
**/
class webpdo {

  /**
  *	@param $epURL vegponti cim pl http://debian3/idtv/dev/sql.php
  **/
  function __construct($epURL){
    $this->epURL = $epURL;
  }
  
  
  protected function __webcall($keyval){
    list($key,$val) = $keyval;
    if(strlen($val)>5){	// hosszu query string eseten GET helyett POSTot hasznalunk
      $inet = new \zs\net\internet;
      $rs = @$inet->post([$key=>$val], $this->epURL );
    } else {
      $uri = "{$this->epURL}?$key=".urlencode($val);
      $rs = @file_get_contents($uri);
    }
    return @json_decode($rs);
  }
  
  public function exec($sql){
    return @$this->__webcall(['exec',($sql)]);
  }
  
  public function oneRow($sql){
    return @$this->__webcall(['oneRow',($sql)]);
  }
  
  public function oneValue($sql){
    return @$this->__webcall(['oneValue',($sql)]);
  }
  
  public function oneCol($sql){
    return @$this->__webcall(['oneCol',($sql)]);
  }
  
  public function allRow($sql){
    return @$this->__webcall(['allRow',($sql)]);
  }
  
}
