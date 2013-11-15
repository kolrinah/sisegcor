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
    $('#registros').dataTable( {
	"sPaginationType": "full_numbers",
        "aaSorting": [[ 0, "desc" ]]
			} );
    
    $( "#FechaIni" ).datepicker({
              showOn: "both",
              buttonImage: $('#base_url').val()+"imagenes/cal.gif",
              buttonText:"clic para Seleccionar",
              buttonImageOnly: true,
              showOtherMonths: true,
              selectOtherMonths: true,
              dateFormat:"dd/mm/yy",
              currentText:"Hoy",
              nextText:"Sig",
              //defaultDate: "01/01/"+$('#year_poa').val(),
              //minDate:_FechaMayor("01/01/"+$('#year_poa').val(),_DiaHoy()),
              maxDate: $('#FechaFin').val(),
              dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
              dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
              monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
              onSelect: function( selectedDate ) {
                  Actualiza();
                  $( "#FechaFin" ).datepicker( "option", "minDate", selectedDate );
                  }            
                       });
    $( "#FechaFin" ).datepicker({
              showOn: "both",
              buttonImage: $('#base_url').val()+"imagenes/cal.gif",
              buttonText:"clic para Seleccionar",
              buttonImageOnly: true,
              showOtherMonths: true,
              selectOtherMonths: true,
              dateFormat:"dd/mm/yy",
              nextText:"Sig",        
              //defaultDate:$('#fechaI').val()+5,
              minDate:$('#FechaIni').val(),
              maxDate:_DiaHoy(),
              dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
              dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
              monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
              onSelect: function( selectedDate ) {
                  Actualiza();
                  $( "#FechaIni" ).datepicker( "option", "maxDate", selectedDate );
                  }
          });
});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES
function Actualiza()
{        
   // ACTUALIZAMOS LAS VARIABLES DE SESION
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'bandeja/actualiza_sesion',
          data:{
                'entrante':($("#hideEntrante").val()=='t')?true:false,
                'fecha_inicial':$("#FechaIni").val(),
                'fecha_final':$("#FechaFin").val(),
                'id_tipo':$("#id_tipo").val(),
                'id_clasificacion':$("#id_clasificacion").val(),
                'bandeja':$("#bandeja").val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Error al Intentar Actualizar.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){
                      // $('#Tabla').hide();
                       $('#Tabla').html(data);
                       $('#registros').dataTable( {
                            "sPaginationType": "full_numbers",
                            "aaSorting": [[ 0, "desc" ]]
			} );
                      // $('#Tabla').show();
                       },
          dataType:'html'});
    return false;
}

function ToggleBotonEntrante()
{
   if ($("#hideEntrante").val()=='t')
   {
     $("#hideEntrante").val('f');      
     $("#imgEntrante").attr('src', $('#base_url').val()+'imagenes/saliente.png');
     $("#imgEntrante").attr('title', 'Ver Correspondencia Entrante');
     $("#spanEntrante").html('&nbsp;Correspondencia Saliente');
     
   }
   else
   {
     $("#hideEntrante").val('t');      
     $("#imgEntrante").attr('src', $('#base_url').val()+'imagenes/entrante.png');
     $("#imgEntrante").attr('title', 'Ver Correspondencia Saliente');
     $("#spanEntrante").html('&nbsp;Correspondencia Entrante');
   }
}

function VerDetalles(id_correspondencia)
{
   var specs='toolbar=yes, titlebar=yes, status=yes, menubar=no, location=yes, fullscreen=yes, scrollbars=yes'; 
   window.open($('#base_url').val()+'revisar/correspondencia/'+id_correspondencia,'','',false);
   //window.open(URL,name,specs,replace);
}