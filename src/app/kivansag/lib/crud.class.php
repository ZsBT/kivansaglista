<?php
namespace app\kivansag;


/**	ebbol szarmaztatjuk a CRUD tipusu osztalyokat.
 *	a szabvanyositas miatt praktikus igy.
 */
interface crud {
    
    /** letrehozas */
    public function create();
    
    /** lekerdezes. @param int $ID opcionalis  */
    public function read($ID=NULL);
    
    /** frissites */
    public function update();
    
    /** torles */
    public function delete();
}

