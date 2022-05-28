(function() {    

    function control2(direccion) {

        let tl = gsap.timeline();

        switch(direccion) {
            case "Abajo": 
                tl.to($(".caja2"), {duration: 0.100, y:"+=25"});
                
            break;
            case "Arriba": 
                tl.to($(".caja2"), {duration: 0.100, y:"-=25"});
            break;
            case "Izquierda": 
                tl.to($(".caja2"), {duration: 0.100, x:"-=25"});
            break;
            case "Derecha":
                tl.to($(".caja2"), {duration: 0.100, x:"+=25"});
            break;
            case "Agrandar": 
                tl.to($(".caja2"), {duration: 0.100, height:"+=50", width:"+=50"});
            break;
            case "Disminuir": 
                tl.to($(".caja2"), {duration: 0.100, height:"-=50", width:"-=50"});
            break;
            case "Reset":
                tl.to($(".caja2"), {duration: 1, height:"20", width:"20", x:"0", y:"0"});
            break;
        }
    }
    $(document).on("keypress", function (e) {
        var keyCode = e.keyCode;
        console.log(keyCode);
        $('.caja2').clearQueue();
        
        switch(keyCode) {
            case 119: 
                control("Arriba");
            break;
            case 115: 
                control("Abajo");
            break;
            case 97: 
                control("Izquierda");
            break;
            case 100: 
                control("Derecha");
            break;
            case 114: 
                control("Reset");
            break;
            case 101: 
                control("Agrandar");
            break;
            case 113: 
                control("Disminuir");
            break;
        }
    
    });

    function control(direccion) {

        let tl = gsap.timeline();

        switch(direccion) {
            case "Abajo": 
                tl.to($(".caja1"), {duration: 0.100, y:"+=25"});
            break;
            case "Arriba": 
                tl.to($(".caja1"), {duration: 0.100, y:"-=25"});
            break;
            case "Izquierda": 
                tl.to($(".caja1"), {duration: 0.100, x:"-=25"});
            break;
            case "Derecha":
                tl.to($(".caja1"), {duration: 0.100, x:"+=25"});
            break;
            case "Agrandar": 
                tl.to($(".caja1"), {duration: 0.100, height:"+=50", width:"+=50"});
            break;
            case "Disminuir": 
                tl.to($(".caja1"), {duration: 0.100, height:"-=50", width:"-=50"});
            break;
            case "Reset":
                tl.to($(".caja1"), {duration: 1, height:"20", width:"20", x:"0", y:"0"});
            break;
        }
    }
    $(document).on("keypress", function (e) {
        var keyCode = e.keyCode;
        console.log(keyCode);
        $('.caja1').clearQueue();
        
        switch(keyCode) {
            case 121: 
                control3("Arriba");
            break;
            case 104: 
                control3("Abajo");
            break;
            case 103: 
                control3("Izquierda");
            break;
            case 106: 
                control3("Derecha");
            break;
            case 105: 
                control3("Reset");
            break;
            case 117: 
                control3("Agrandar");
            break;
            case 116: 
                control3("Disminuir");
            break;
        }
        DetectarColision();
    });

    function control3(direccion) {

        let tl = gsap.timeline();

        switch(direccion) {
            case "Abajo": 
                tl.to($(".caja3"), {duration: 0.100, y:"+=25"});
                
            break;
            case "Arriba": 
                tl.to($(".caja3"), {duration: 0.100, y:"-=25"});
            break;
            case "Izquierda": 
                tl.to($(".caja3"), {duration: 0.100, x:"-=25"});
            break;
            case "Derecha":
                tl.to($(".caja3"), {duration: 0.100, x:"+=25"});
            break;
            case "Agrandar": 
                tl.to($(".caja3"), {duration: 0.100, height:"+=50", width:"+=50"});
            break;
            case "Disminuir": 
                tl.to($(".caja3"), {duration: 0.100, height:"-=50", width:"-=50"});
            break;
            case "Reset":
                tl.to($(".caja3"), {duration: 1, height:"20", width:"20", x:"0", y:"0"});
            break;
        }
    }
    $(document).on("keypress", function (e) {
        var keyCode = e.keyCode;
        console.log(keyCode);
        $('.caja3').clearQueue();
        
        switch(keyCode) {
            case 56: 
                control2("Arriba");
            break;
            case 53: 
                control2("Abajo");
            break;
            case 52: 
                control2("Izquierda");
            break;
            case 54: 
                control2("Derecha");
            break;
            case 43: 
                control2("Reset");
            break;
            case 57: 
                control2("Agrandar");
            break;
            case 55: 
                control2("Disminuir");
            break;
        }
    });

    function DetectarColision(){
        /// "a" y "b" deben ser dos objetos HTMLElement
    var a = $(".caja1");
    var b = $(".caja2");
    var c = $(".caja3");

    var b1 = $(".b1");
    
    var a_pos = {t : a.position().top, 
                                l: a.position().left, 
                    r: a.position().left + a.width(), 
                    b: a.position().top + a.height()};
    var b_pos =  {t : b.position().top, 
                                l: b.position().left, 
                    r: b.position().left + b.width(), 
                    b: b.position().top + b.height()};
    var c_pos =  {t : c.position().top, 
                                l: c.position().left, 
                    r: c.position().left + c.width(), 
                    b: c.position().top + c.height()};
                    
    
    //Detecta si se superponen las áreas
    if( (a_pos.l <= b_pos.r && a_pos.r >= b_pos.l 
        && a_pos.b >= b_pos.t && a_pos.t <= b_pos.b)
        ||
        (a_pos.l <= c_pos.r && a_pos.r >= c_pos.l 
        && a_pos.b >= c_pos.t && a_pos.t <= c_pos.b)
        ||
        (b_pos.l <= c_pos.r && b_pos.r >= c_pos.l 
        && b_pos.b >= c_pos.t && b_pos.t <= c_pos.b) ){

            $('body').attr('class', 'morado');
        } else {
            $('body').attr('class', '');
        }
    
    }
})();