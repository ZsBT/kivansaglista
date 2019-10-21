/* ez a sajat kodunk */

(function($) {
  var Path = $("body").data("path");
  
  
  

  
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
  
  
  



  var nonce = $("[nonce]").attr("nonce")
  
  // kivansaglista {

  var dtList = $("#dtList").DataTable({	// lista datatables
    "ajax":"ajax/list/list",
    "language":{url:"js/dt-hu.lang"},
    "columns":[
      {data:"nev"},
      {data:"ar",className:'align-right'},
      {data:"letrehozva"}
    ],
  })

  var dtWish = $("#dtWish").DataTable({ // kivansag datatables
    "language":{url:"js/dt-hu.lang"},
    "columns":[
      {data:"nev"},
      {data:"ar",className:'align-right'},
      {data:"letrehozva"},
    ]
  })
  dtWish.betolt = function(listID){	// egyszerusitjuk a betoltest
    dtWish.ajax.url("ajax/wish/list?listID="+listID).load()
    dtList.ajax.reload()
  };
  

  // uj lista gombra kattintas
  $("#showListModal").on("click",function(E){
    $("#listDel").hide()
    $("#ListName").val("");
    $("#ListID").val("")
    $(".modal .share").hide()
  })

  // lista mentes gombja
  $("#listSave").on("click",function(E){
    var listName = $("#ListName").val()
    if(!listName.length)return;
    $.getJSON( $("#ListID").val() ? "ajax/list/update":"ajax/list/create"
      , {id:$("#ListID").val(),nev:listName,nonce:nonce}
      , function(ret){
      window.location.reload()
    })
  })

  // szimpla kattintas a lista egy sorara
  $('#dtList tbody').on('click', 'tr', function () {
      var data = dtList.row( this ).data();
      $("#listName").html(data.nev).attr("list",data.id)
      $("div.kivansag").show()
      dtWish.betolt(data.id)
  })

  // dupla kattintas a lista egy sorara
  $('#dtList tbody').on('dblclick', 'tr', function () {
      var data = dtList.row( this ).data();
      
      $("#ListID").val(data.id)
      $("#ListName").val(data.nev)
      $("#ListModal .share").attr("href", "lista-pub?hash="+data.linkhash)
      
      $("#listDel").show()
      $("#ListModal").modal()
      $(".modal .share").show()
  })
  
  
  // megosztott link ujrageneralo gomb
  $("#regenshare").on("click",function(){
      var me=$(this)
      
      $.getJSON("ajax/list/regenhash", {id:$("#ListID").val(), nonce:nonce}, function(ret){
        $("#ListModal .share").attr("href", "lista-pub?hash="+ret.newhash)
        me.attr("class","fa fa-check").attr("disabled",true)
      })
  })


  //	torles
  $("#listDel").on("click",function(E){
    if(!confirm("Biztosan törli a listát?"))return false;
    
    $.getJSON("ajax/list/delete", {id:$("#ListID").val(),nonce:nonce}, function(ret){
      window.location.reload()
    })
  })

  // } kivansaglista
  


  
  
  // kivansag {

  // felsorolas
  $("#showWishModal").on("click",function(E){
    $("#wishDel").hide()
    $("#WishName").val("");
    $("#WishPrice").val("");
    $("#WishID").val("")
  })

  // rogzites
  $("#wishSave").on("click",function(E){
    var wishName = $("#WishName").val(), listaID=$("#listName").attr("list");
    if(!wishName.length)return;
    $.getJSON( $("#WishID").val() ? "ajax/wish/update":"ajax/wish/create"
      , {id:$("#WishID").val(), nev:wishName, nonce:nonce, lista:listaID, ar:$("#WishPrice").val() }
      , function(ret){
          if(ret.hiba) return alert(ret.hiba);
          dtWish.betolt(listaID)
          $("#WishModal").modal('hide')
        }
      )
  })


  // kattintas egy kivansagra
  $('#dtWish tbody').on('click', 'tr', function () {
      var data = dtWish.row( this ).data();
      
      $("#WishID").val(data.id)
      $("#WishName").val(data.nev)
      $("#WishPrice").val(data.ar)
      $("#wishDel").show()
      $("#WishModal").modal()
  });


  //	torles
  $("#wishDel").on("click",function(E){
    if(!confirm("Biztosan törli a kívánságot?"))return false;
    
    $.getJSON("ajax/wish/delete", {id:$("#WishID").val(),nonce:nonce}, function(ret){
      dtWish.betolt($("#listName").attr("list"))
      $("#WishModal").modal('hide')
    })
  })
  // } kivansag



  // altalanos UI tamogatas

  // toast
  var Toast=$(".toast");
  Toast.toast({delay:3000})
  Toast.ertesites = function(cim, uzenet){
    Toast.find("strong").html(cim)
    Toast.find(".toast-body").html(uzenet)
    Toast.toast("show")
  }
  window.Toast=Toast
      
})(jQuery);
