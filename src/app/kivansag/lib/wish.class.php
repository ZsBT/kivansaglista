<?php
namespace app\kivansag;

/** kivansaglista osztaly */
class wish implements crud {

    /** attributumok */
    public $id, $lista, $nev;
    
    /** letrehozas. a peldany attributumai mar be kell legyenek allitva.
     *	@return uj user ID
     *	@throws wishException
     */
    public function create(){
        if(!$this->lista)	throw new wishException("hiányzik a lista ID");
        if(!strlen($this->nev))	throw new wishException("hiányzik a név");
        
        // ellenorizni kell, hogy felhasznalonkent egy kivansag csak egyszer szerepelhet.
        if(!$wishlist = new wishlist($this->lista)) throw new wishException("a lista #{$this->lista} nem létezik");
        if( self::exists($this->nev,$wishlist->felhasznalo) ) throw new wishException("már szerepel a kívánság egy listán");
        
        $newid = db::instance()->insert(self::TABLA, ["lista"=>$this->lista,"nev"=>$this->nev,"ar"=>max(0,0+$this->ar)], "id");
        $this->logger->info(__CLASS__." #$newid létrehozva");
        return $newid;
    }
    
    
    
    /** szerepel-e mar a kivansag
     *	@param string $kivansag	megnevezes
     *	@param string $felhasznaloID felhasznalo ID
     *	@return bool szerepel
     */
    public static function exists($nev, $felhasznaloID){
        return db::instance()->oneValue("select k.id from kivansaglista kl left join kivansag k on kl.id=k.lista where k.nev='$nev' and kl.felhasznalo=".(0+$felhasznaloID));
    }
    
    

    /** rekord betoltese.
     *	@return wish objektumpeldany
     *	@throws wishException
     */
    public function read($ID=NULL){
        
        if(isset($this)){	// ha peldanybol hivtuk meg
            if($ID) $this->id = $ID;
            if(!$this->id) throw new wishException('ismeretlen ID');
            $ID = $this->id;
            $wish = $this;
        } else {
            if(!$ID) throw new wishException('nincs kapott $ID');
            $wish = new user;
        }

        foreach( self::load($ID) as $k=>$v)
            $wish->{$k} = $v;
        
        return $wish;
    }
    
    
    /**  adatok frissitese.
     *	@return boolean $sikeres
     *	@throws wishException
     */
    public function update(){
        $IA=[];
        foreach(['id','lista','nev','ar']as $attr)
            if(!$this->{$attr})
                throw new wishException("$attr hiányzik");
            else
                $IA[$attr] = $this->{$attr};
        
        $IA['ar'] = max(0,0+$this->ar);
        
        // ellenorizni kell, hogy felhasznalonkent egy kivansag csak egyszer szerepelhet.
        if(!$wishlist = new wishlist($this->lista)) throw new wishException("a lista #{$this->lista} nem létezik");
        if( $existid=self::exists($this->nev,$wishlist->felhasznalo) and $existid!=$this->id ) throw new wishException("már szerepel a kívánságod egy listán");
        
        
        $this->logger->info(__CLASS__." #{$this->id} módosítása");
        return db::instance()->update(self::TABLA, $IA, "id={$this->id}")?TRUE:FALSE;
    }
    
    
    /** torlese. 
     *	@return bool sikeres
     */
    public function delete(){
        $succ = db::instance()->exec("delete from ".self::TABLA." where id=".(0+$this->id));
        $this->logger->notice(__CLASS__." #{$this->id} törlése ".($succ?"sikeres":"SIKERTELEN"));
        return $succ?TRUE:FALSE;
    }
    
    
    
    
    /**  rekord betoltese. @return mixed rekord objektum */
    protected static function load($id=0){
        $rec = db::instance()->oneRow("select * from ".self::TABLA." where id=".(0+$id));
        if(!$rec) throw new wishException(__CLASS__." rekord #$id nem letezik");
        return $rec;
    }
    
    
    
    /** peldanyositaskor opcionalisan ID-t adhatunk meg, ekkor be is tolti */
    function __construct($ID=NULL){
        $this->logger = new \app\logger;
        if($ID) $this->read($ID);
    }
    
    
    
    /** ezt az adatbazistablat hasznaljuk */
    const TABLA = "kivansag";
}


/**  muvelet kivetele */
class wishException extends \Exception {}
