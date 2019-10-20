$(function(){
    
    function hash(str){	// kulon fuggveny, mert ketszer hash-elunk es az elozo vegeredmenyet kell
        var h = new jsSHA("SHA-256","TEXT");
        h.update(str)
        return h.getHash("HEX")
    }
    
    $("form").on("submit",function(evt){
        var hpass = hash( $("#plainpass").val() + $("form").data("hashso")) // ez eddig a tarolt jelszo
        hpass = hash(hpass + $("[name=nonce]").val() ) // nonce-cal tovabb hash-eljuk, igy minden belepeskor egyedi
        $("[name=password]").val(hpass)
        $("button").attr("disabled",true)
    })
})