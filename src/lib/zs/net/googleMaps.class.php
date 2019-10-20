<?php	/*

  google maps funkciok
  
  */

namespace zs\net;  

  

class googleMaps extends internet {
  
  var $APIkey = NULL;	// ki kell tolteni!
  
  const MAPURIPAT = "https://maps.googleapis.com/maps/api/geocode/json?key=%s&latlng=%f,%f&lang=hu&types=street_address";
  const GEOURIPAT = "https://maps.googleapis.com/maps/api/geocode/json?key=%s&lang=hu&address=%s";
  
  
  public function cim($lat,$lng)	// koordinata alapjan cim
  {
    if(!$this->APIkey)throw new \Exception('$APIkey hianyzik');
    if(!($lat*$lng)) throw new \Exception('$lat vagy $lng hianyzik');
    return @json_decode( $this->get(sprintf(self::MAPURIPAT, $this->APIkey, $lat, $lng ) ) );
  }


  public function geo($cim){	// cim alapjan koordinata
    $uri = sprintf(self::GEOURIPAT, $this->APIkey, urlencode($cim) );
    return @json_decode($this->get($uri)) ;
  }
  

  function __construct($apikey=NULL){$this->APIkey = $apikey;}
  
}


