$(document).ready(function(){

	$("[data-toggle=tooltip]").tooltip();

})


function IsRut(rut){
    var rexp = new RegExp(/^([0-9])+\-([kK0-9])+$/);
    if(rut.match(rexp)){
        var RUT		= rut.split("-");
        var elRut	= RUT[0];
        var factor	= 2;
        var suma	= 0;
        var dv;
        for(i=(elRut.length-1); i>=0; i--){
            factor = factor > 7 ? 2 : factor;
            suma += parseInt(elRut[i])*parseInt(factor++);
        }
        dv = 11 -(suma % 11);
        if(dv == 11){
            dv = 0;
        }else if (dv == 10){
            dv = "k";
        }

        if(dv == RUT[1].toLowerCase()){
            return true;
        }else{            
            return false;
        }
    }else{
        return false;
    }
}



function formateaRut(rut){
	if( rut != "" ){
	    arr_rut = rut.split("-");
	    
	    var the_rut = arr_rut[0];
	    the_rut = the_rut.replace(/\./g,'');
	    
	    if( arr_rut.length == 1 ){
	        the_dv = the_rut.substring((the_rut.length - 1), (the_rut.length));
	        the_rut = the_rut.substring(0, (the_rut.length - 1));
	    } else {
	        the_dv = arr_rut[1];
	        the_rut = the_rut;
	    }
	    
	    return_rut = the_rut+"-"+the_dv;
   	} else {
   		return_rut = "";
   	}
    
    return return_rut;
}

