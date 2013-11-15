/************************************************************************/
/* SISTEMA DE SEGUIMIENTO Y CONTROL DE CORRESPONDENCIA                  */
/* DESARROLLADO POR: ING.REIZA GARCÍA                                   */
/*                   ING.HÉCTOR MARTÍNEZ                                */
/* PARA EL MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES      */
/* JULIO DE 2013                                                        */
/* TELEFONOS DE CONTACTO PARA SOPORTE: 0416-9052533 / 0212-5153033      */
/************************************************************************/

$(document).ready(function()
{     
    $('#bitacora').dataTable( {
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

function Actualiza()
{       
   // ACTUALIZAMOS LAS VARIABLES DE SESION
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'bitacora/actualiza_sesion',
          data:{                
                'fecha_inicial':$("#FechaIni").val(),
                'fecha_final':$("#FechaFin").val()                
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Error al Intentar Actualizar.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){
                       $('#Tabla').html('');                      
                       $('#Tabla').html(data);
                       $('#bitacora').dataTable( {
                            "sPaginationType": "full_numbers",
                            "aaSorting": [[ 0, "desc" ]]
			} );
                       
                       },
          dataType:'html'});
    return false;
}