/* admin muveletek 

    kulon fajlban helyezzuk el, hogy a sima user ne is lassa az ajax lehetosegeket */


(function($) {
  
  // felhasznalo admin funkciok {
  
    // lista
    var dtUser = $("#dtUser").DataTable({
      "ajax":"ajax/user/list",
      "language":{url:"js/dt-hu.lang"},
      "columns":[
        {data:"azonosito"},
        {data:"nev"},
        {data:"letrehozva"},
        {data:"modositva"},
        {data:"jogH"},
        {data:"aktivH"}
      ]
    })

    function hash(str){
        var h = new jsSHA("SHA-256","TEXT");
        h.update(str)
        return h.getHash("HEX")
    }
    

    // kattintas egy felhasznalora
    $('#dtUser tbody').on('click', 'tr', function () {
        var data = dtUser.row( this ).data();
        console.debug(dtUser,this,data);
        
        // feltoltjuk a modal ablak mezoinek ertekeit
        for(var k in data) if(k!='aktiv')$("#userModal [name="+k+"]").val(data[k])
        $("#userModal [name=aktiv]").attr("checked", data.aktiv==1)
        
        $("#userDelete").show()
        $("#userModal").modal()
        $("[name=jelszo]").val("")
    });

    // torles gomb
    $("#userDelete").on("click",function(E){
      if(!confirm("Biztosan törlöd a felhasználót? Nem vonható vissza."))return true;
      $.getJSON("ajax/user/delete", $("#userModal form").serializeArray(), function(ret){
        if(ret)window.location.reload()
        else console.warn(ret);
      })
    })
    
    // hozzaadas gomb
    $("#userAdd").on("click",function(E){
      $("#userDelete").hide();
      $("#userModal").modal();
      ['id','nev','azonosito','jelszo'].forEach(function(attr){
        $("#userModal [name="+attr+"]").val("")
      })
      $("#userModal [name=aktiv]").attr("checked","checked")
      
    })
    

    // mentes gomb
    $("#userSave").on("click",function(E){
      // ha megad uj jelszot, azt is allitjuk
      var plainpass = $("#plainpass").val(), newpassword=$("[name=jelszo]")
      if(plainpass.length>3)
        newpassword.val( hash((plainpass+newpassword.data("hasho"))) )
      
      $.getJSON( $("[name=id]").val().length ? "ajax/user/update":"ajax/user/create", $("#userModal form").serializeArray(), function(ret){
        if(ret)window.location.reload()
      })
    })
    

  // } felhasznalo admin
  
})(jQuery);
