<?php
namespace app\kivansag;

/** kivansaglista osztaly */
class wishlist implements crud {

    /** attributumok */
    public $id, $felhasznalo, $nev;
    
    /** letrehozas. a peldany attributumai mar be kell legyenek allitva.
     *	@return uj user ID
     *	@throws wishlistException
     */
    public function create(){
        if(!$this->felhasznalo)	throw new wishlistException("hiányzik a felhasználó ID");
        if(!$this->nev)	throw new wishlistException("hiányzik a név");
        if(db::instance()->oneValue(sprintf("select count(1) from %s where felhasznalo=%d and nev='%s'"
            ,self::TABLA, $this->felhasznalo, $this->nev))) throw new wishlistException("már létezik ilyen listád");
        
        $newid = db::instance()->insert(self::TABLA, [
            "felhasznalo"=>$this->felhasznalo,
            "nev"=>$this->nev,
            "linkhash" => $this->genlinkhash(),
        ], "id");
        
        $this->logger->info(__CLASS__." #$newid létrehozva");
        return $this->id=$newid;
    }
    
     
    /** rekord betoltese.
     *	@return wishlist objektumpeldany
     *	@throws wishlistException
     */
    public function read($ID=NULL){
        
        if(isset($this)){	// ha peldanybol hivtuk meg
            if($ID) $this->id = $ID;
            if(!$this->id) throw new wishlistException('ismeretlen ID');
            $ID = $this->id;
            $wishlist = $this;
        } else {
            if(!$ID) throw new wishlistException('nincs kapott $ID');
            $wishlist = new user;
        }

        foreach( self::load($ID) as $k=>$v)
            $wishlist->{$k} = $v;
        
        return $wishlist;
    }
    
    
    /**  adatok frissitese.
     *	@return boolean $sikeres
     *	@throws wishlistException
     */
    public function update(){
        $IA=[];
        foreach(['id','felhasznalo','nev']as $attr)
            if(!$this->{$attr})
                throw new wishlistException("$attr hiányzik");
            else
                $IA[$attr] = $this->{$attr};

        if(db::instance()->oneValue(sprintf("select count(1) from %s where felhasznalo=%d and nev='%s'"
            ,self::TABLA, $this->felhasznalo, $this->nev))) throw new wishlistException("már létezik ilyen listád");
        
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
    


    /** kivansag hozzaadasa
     *	@param wish $wish kivansag
     *	@return bool sikeres
     */
    public function addWish(wish $wish){
        $wish->lista = $this->id;
        return $wish->create();
    }
    
    
    /** kivansagok listaja
      *	@return wish[]
      */
    public function wishes(){
        if(!$this->id) throw new wishlistException("nincs ID");
        
        $ret = [];
        foreach( db::instance()->oneCol("select id from kivansag where lista={$this->id}") as $wishID )
            $ret[] = new wish($wishID);
        
        return $ret;
    }
    
    
    /** megoszthato link hash generalasa
     *	@return string link hash
     */
    public function genlinkhash(){
        $linkhash = substr(md5(uniqid()),0,17);	// megoszthato link uj ellenorzo kodja
        if($this->id and !db::instance()->update(self::TABLA,["linkhash"=>$linkhash],"id={$this->id}"))
            return false;
        
        return $linkhash;
    }
    
    
    /**  rekord betoltese. @return mixed rekord objektum */
    protected static function load($id=0){
        $rec = db::instance()->oneRow("select * from ".self::TABLA." where id=".(0+$id));
        if(!$rec) throw new wishlistException(__CLASS__." rekord #$id nem letezik");
        return $rec;
    }
    
    
    
    /** peldanyositaskor opcionalisan ID-t adhatunk meg, ekkor be is tolti */
    function __construct($ID=NULL){
        $this->logger = new \app\logger;
        if($ID) $this->read($ID);
    }
    
    
    
    /** ezt az adatbazistablat hasznaljuk */
    const TABLA = "kivansaglista";
}


/**  muvelet kivetele */
class wishlistException extends \Exception {}
