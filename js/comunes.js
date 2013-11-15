/************************************************************************/
/* SISTEMA DE SEGUIMIENTO Y CONTROL DE CORRESPONDENCIA                  */
/* DESARROLLADO POR: ING.REIZA GARCÍA                                   */
/*                   ING.HÉCTOR MARTÍNEZ                                */
/* PARA EL MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES      */
/* ENERO DE 2013                                                        */
/* TELEFONOS DE CONTACTO PARA SOPORTE: 0416-9052533 / 0212-5153033      */
/************************************************************************/
$(document).ready(function()
{  
   setInterval('VerificarSesion()',300000);    
});

// FUNCIONES ESPECIALES
// 
// VERIFICA LA EXISTENCIA DE LA SESION
function VerificarSesion()
{  
  $.post($('#base_url').val()+"acceso/verificar_sesion", function(data) {
         var aprobado;  
         aprobado=true;
         if (data==='FALSE'){aprobado=false;}         
         if (aprobado==false)
         {
            window.location=$('#base_url').val()+"acceso/salir";
         }  
        });
}

// DEVUELVE LA FECHA DEL DIA DE HOY
function _DiaHoy(opcion)
{
   var Hoy= new Array(2);
   var fecha=new Date();
   Hoy[1]= fecha.getFullYear();
   Hoy[0]=(fecha.getDate()<10)? '0'+fecha.getDate(): fecha.getDate();
   Hoy[0]+=((fecha.getMonth()+1)<10)? '/0'+(fecha.getMonth()+1):'/'+(fecha.getMonth()+1);
   Hoy[0]+= '/'+Hoy[1];
   if (opcion==1){return Hoy[1];}
   else {return Hoy[0];}
}

// DEVUELVE LA FECHA MAYOR ENTRE LAS DOS SUMINISTRADAS
function _FechaMayor(FechaIni, FechaFin)
{
  //Obtiene dia, mes y año  
   var fecha1 = new _fecha( FechaIni );     
   var fecha2 = new _fecha( FechaFin );

  //Obtiene Objetos Date
  var miFecha1 = new Date( fecha1.anio, fecha1.mes, fecha1.dia );
  var miFecha2 = new Date( fecha2.anio, fecha2.mes, fecha2.dia ); 
	//Resta fechas y redondea  
  return (miFecha1.getTime()>miFecha2.getTime())? FechaIni:FechaFin;  
}

// DEVUELVE LA DIFERENCIA EN DIAS DE DOS FECHAS
function _DiferenciaFechas(FechaIni, FechaFin)
{
   //Obtiene dia, mes y año  
   var fecha1 = new _fecha( FechaIni );     
   var fecha2 = new _fecha( FechaFin );

   //Obtiene Objetos Date
   var miFecha1 = new Date( fecha1.anio, fecha1.mes, fecha1.dia );
   var miFecha2 = new Date( fecha2.anio, fecha2.mes, fecha2.dia ); 
   //Resta fechas y redondea  
   var diferencia = (miFecha2.getTime() - miFecha1.getTime())/1000*60;  
   //var dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));  
   //return dias;   	
   return diferencia;		
}

// CONSTRUCTOR DE CADENA (Formato "dd/mm/YYYY" A FECHA
function _fecha(cadena) 
{  
 //Separador para la introduccion de las fechas  
 var separador = "/"; 
 //Separa por dia, mes y año  
 if ( cadena.indexOf( separador ) != -1 ) 
 {
	 var posi1 = 0;  
	 var posi2 = cadena.indexOf( separador, posi1 + 1 );  
	 var posi3 = cadena.indexOf( separador, posi2 + 1 );  
	 this.dia = cadena.substring( posi1, posi2 );  
	 this.mes = cadena.substring( posi2 + 1, posi3 );  
	 this.mes =this.mes -1
   this.anio = cadena.substring( posi3 + 1, cadena.length );  
 } 
 else
 {  
	 this.dia = 0;  
	 this.mes = 0;  
	 this.anio = 0;     
 }  
}

function CancelarModal()
{
  $('#VentanaModal').html('');
  $('#VentanaModal').hide();        
}

function CajaDialogo(tipo, Mensaje, Botones)
{
  var caja;
  Mensaje= Mensaje || "Atención";
  var Titulo;
  var Imagen;
  var Botones=Botones || {Cerrar: function(){$( this ).dialog( "close" )}};
  switch(tipo)
  {
    case "Guardar":
         Titulo="Guardar";
         Imagen=$('#base_url').val()+'imagenes/guardar.png';
         break;
    case "Alerta":
         Titulo="Atención";
         Imagen=$('#base_url').val()+"imagenes/alerta.png";
         break;
    case "Borrado":
         Titulo="Operación Exitosa";
         Imagen=$('#base_url').val()+"imagenes/borrado64.png";
         break;         
    case "DepBorrada":
         Titulo="Operación Exitosa";
         Imagen=$('#base_url').val()+"imagenes/dep_borrada.png";
         break;
    case "DepCreada":
         Titulo="Operación Exitosa";
         Imagen=$('#base_url').val()+"imagenes/dep_creada.png";
         break;
    case "Exito":
         Titulo="Operación Exitosa";
         Imagen=$('#base_url').val()+"imagenes/exito.png";
         break;     
    case "Pregunta":
         Titulo="Pregunta";
         Imagen=$('#base_url').val()+"imagenes/pregunta.png";
         break;
    case "Error":
         Titulo="Error";
         Imagen=$('#base_url').val()+"imagenes/error.png";
         break;
    default:
         Titulo="Mensaje";
         Imagen=$('#base_url').val()+"imagenes/warning.png";
  }    
  caja='<div title="'+Titulo+'">';
  caja+='<table width=100%"><tr>';
  caja+='<td style="vertical-align: middle; width: 80px; text-align: center; padding:15px 5px 0 0">';
  caja+='<img src="'+Imagen+'" /></td>';
  caja+='<td style="vertical-align: middle; padding:15px 0 0 10px; font-size:1.2em">';
  caja+=Mensaje+'</td></tr></table></div>';             
  caja=$(caja);
  caja.dialog({
        modal: true,
        zIndex:1000,
        draggable:false,
        resizable: false,
        minHeight:200,
        width:400,
        buttons:Botones});
}


function onlyDigits(e, value, allowDecimal, allowNegative, allowThousand, decSep, thousandSep, decLength)
{
	var _ret = true, key;
	if(window.event) { key = window.event.keyCode; isCtrl = window.event.ctrlKey }
	else if(e) { if(e.which) { key = e.which; isCtrl = e.ctrlKey; }}
	if(key == 8) return true;
	if(isNaN(key)) return true;
	if(key < 44 || key > 57) { return false; }
	keychar = String.fromCharCode(key);
	if(decLength == 0) allowDecimal = false;
	if(!allowDecimal && keychar == decSep || !allowNegative && keychar == '-' || !allowThousand && keychar == thousandSep) return false;
	return _ret;
}

function verificarCI(ci)
{
  if (isNaN(ci) || ci=='' || ci.length<4) return false;
  return true;    
}

function verificarEmail(email)
{
    var ereg=/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;   
    return ereg.test(email);
}

function verificarNombre(nombre)
{
    var ereg=/^([A-Za-zÑÁÉÍÓÚñáéíóúÜü]{1}[A-Za-zÑÁÉÍÓÚñáéíóúÜü]+[\s]*)+$/;
    return ereg.test(nombre);    
}



function trim (myString)
{
   return myString.replace(/^\s+/g,'').replace(/\s+$/g,'');
}