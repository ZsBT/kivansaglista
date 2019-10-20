<?php
namespace app\kivansag;



/** felhasznalokezelo osztaly */
class user implements crud {

    const JOG_USER = 1;
    const JOG_ADMIN = 2;
    
    /** a kotelezo jellemzok nevei, egyeznek az adatbazis rekord oszlopokkal */
    protected static $attr = ['azonosito','jelszo','jog','nev','aktiv'];
    
    
    /** alapertelmezesek */
    public $jog=self::JOG_USER;
    public $aktiv=1;
    
    
    
    /** letrehozas. a user peldany attributumai mar be kell legyenek allitva.
     *	@return uj user ID
     *	@throws userException
     */
    public function create(){

        // ha nincs vagy nincs hash-elve a jelszo, meghiusul
        if(!self::valid_hash($this->jelszo))
            throw new userException("hianyzo vagy nem hashelt jelszo");
        
        $IA = [];
        // ha valami nincs kitoltve, meghiusul
        foreach( self::$attr as $attr )
            if(!strlen(@$this->{$attr}))
                throw new userException("hianyzik a '$attr' attributum");
            else $IA[$attr] = $this->{$attr};
        
        if(!preg_match('/^[a-z0-9@_.]{4,50}$/',$this->azonosito))
            throw new userException("nem megfelelő azonosító: legalább 4, legfeljebb 50 karakter hosszúságú, az alábbi karakterekből: a-z, 0-9, @, _, .");
        else $this->azonosito = strtolower($this->azonosito);
            
        if(self::exists($this->azonosito))
            throw new userException("a felhasznaloi azonosito mar letezik");
        
        $succ = db::instance()->insert("felhasznalo", $IA, "id");
        $this->logger->notice(sprintf("'{$this->azonosito}' felhasznalo letrehozva [%s]", json_encode($succ) ));
        return $succ;
    }
    
     
    /** felhasznalo betoltese.
     *	@return user felhasznalopeldany 
     *	@throws userException
     */
    public function read($ID=NULL){
        
        if(isset($this)){	// ha peldanybol hivtuk meg
            if($ID) $this->id = $ID;
            if(!$this->id) throw new userException('ismeretlen ID');
            $ID = $this->id;
            $user = $this;
        } else {
            if(!$ID) throw new userException('nincs kapott $ID');
            $user = new user;
        }

        foreach( self::load($ID) as $k=>$v)
            $user->{$k} = $v;
        
        return $user;
    }
    
    
    /** felhasznaloi adatok frissitese.
     *	@return boolean $sikeres
     *	@throws userException
     */
    public function update(){
        if(!$this->id)throw new userException("nincs megadva id");

        // ha nincs vagy nincs hash-elve a jelszo, meghiusul
        if(!self::valid_hash($this->jelszo))
            throw new userException("hianyzo vagy nem hashelt jelszo");
        
        $IA = ['modositva'=>date("Y-m-d H:i:s")];
        // ha valami nincs kitoltve, meghiusul
        foreach( self::$attr as $attr )
            if(!strlen(@$this->{$attr}))
                throw new userException("hianyzik a '$attr' attributum");
            else $IA[$attr] = $this->{$attr};
        
        if(!preg_match('/^[a-z0-9@_.]{4,50}$/',$this->azonosito))
            throw new userException("nem megfelelő azonosító: legalább 4, legfeljebb 50 karakter hosszúságú, az alábbi karakterekből: a-z, 0-9, @, _, .");
        else $this->azonosito = strtolower($this->azonosito);
            
        $succ = db::instance()->update("felhasznalo", $IA, "id=".(0+$this->id) );
        $this->logger->notice(sprintf("felhasznalo #{$this->id} frissitve [%s]", json_encode($succ) ));
        return $succ;
        
    }
    
    
    /** felhasznalo torlese. 
     *	@return bool sikeres
     */
    public function delete(){
        $succ = db::instance()->exec("delete from felhasznalo where id=".(0+$this->id));
        $this->logger->notice("felhasznalo #{$this->id} torlese ".($succ?"sikeres":"SIKERTELEN"));
        return $succ?TRUE:FALSE;
    }
    
    
    
    
    /** felhasznalo rekord betoltese. @return mixed rekord objektum */
    protected static function load($id=0){
        $rec = db::instance()->oneRow("select * from felhasznalo where id=".(0+$id));
        if(!$rec) throw new userException("a felhasznalo rekord #$id nem letezik");
        return $rec;
    }
    
    
    /** letezik-e a felhasznalo? 
     *	@param string $azonosito lehet id vagy felhasznaloi azonosito
     *	@param string $jelszo	nem kotelezo, de ha meg van adva akkor a jelszo hash-nek egyeznie kell.
     *	@return	int	felhasznalo ID - ha nem letezik akkor 0
    */
    public static function exists($azonosito, $jelszo=NULL){
        $azonosito = strtolower($azonosito);
        
        $sql = sprintf("select id from felhasznalo where %s "
            ,is_numeric($azonosito) ? "ID=$azonosito" : "azonosito='$azonosito'"
        );

        if($jelszo){
            if(!self::valid_hash($jelszo)) 
                throw new userException("nem hashelt jelszo");
            $sql.=" and jelszo='$jelszo'";
        }
        
        return 0+db::instance()->oneValue($sql);
    }
    
    
    /** adott string hash-elese
     *	@param string $string	amit hashelni kell
     *	@return string	hash
    */
    public static function hash($string){
        $hashso = config::get("site")["hashso"];
        return hash('sha256', $string.$hashso);
    }
    
    
    /** a megadott string tenyleg hash formatumu?
     *	@return bool	ervenyes
     */
    public static function valid_hash($hash){
        return preg_match('/^[0-9a-f]{64}$/',$hash) ? true:false;
    }
    


    /** az aktualis session felhasznalo objektumpeldanya */
    public static function current(){
        return $_SESSION['USER'];
    }
    
    /** peldanyositaskor opcionalisan ID-t adhatunk meg, ekkor be is tolti */
    function __construct($ID=NULL){
        $this->logger = new \app\logger;
        if($ID) $this->read($ID);
    }
    
}


/** felhasznalo muvelet kivetele */
class userException extends \Exception {}
