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
    // PROGRAMAMOS LOS COMBOS DE SELECCION
    $('#BotonEjecutor').hide();
    $('#Multiproposito').hide();
    $('#idMP').val(0);
    $('#observaciones').attr('readonly','readonly').removeClass('Editable');
    $('#Guardar').hide();
    $('#id_tipo_movimiento').change(function(){prepararMovimiento();});
    $('#id_estatus').change(function(){cambiarEstatus();});
    $('#id_clasificacion').change(function(){cambiarClasificacion()});
    $('#BotonCambiarLugar').click(function(){cambiarUbicacion();});
        
});   // FINAL DEL DOCUMENT READY

function cambiarEstatus()
{
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/cambiar_estatus',
          data:{
                'id_correspondencia':$('#id_correspondencia').val(),               
                'id_estatus':$('#id_estatus').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cambiar el Estatus.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Cambio de Estatus a:<br/> ';
                       Mensaje+='<center><span style="font-size: 1.1em; font-weight: bold;">';
                       Mensaje+='"'+data.estatus+'"</center></span>';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" );
                       $('#Movimientos').html(data.movimientos);
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;   
}

function cambiarClasificacion()
{
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/cambiar_clasificacion',
          data:{
                'id_correspondencia':$('#id_correspondencia').val(),               
                'id_clasificacion':$('#id_clasificacion').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cambiar la Clasificación.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Cambio de Clasificación a:<br/> ';
                       Mensaje+='<center><span style="font-size: 1.1em; font-weight: bold;">';
                       Mensaje+='"'+data.clasificacion+'"</center></span>';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" );
                       $('#Movimientos').html(data.movimientos);
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;   
}

function cambiarUbicacion()
{
   if (trim($('#lugar_archivo').val())=='') return false
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/cambiar_ubicacion',
          data:{
                'id_correspondencia':$('#id_correspondencia').val(),               
                'lugar_archivo':trim($('#lugar_archivo').val())
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cambiar la Ubicación.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Cambio de Ubicación Física a:<br/> ';
                       Mensaje+='<center><span style="font-size: 1.1em; font-weight: bold;">';
                       Mensaje+='"'+data.lugar_archivo+'"</center></span>';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" );
                       $('#lugar_archivo').val(data.lugar_archivo);
                       $('#Movimientos').html(data.movimientos);
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;  
}

function prepararMovimiento()
{
   // Si no hay selección, limpiamos los campos especiales
   limpiar();
   if ($('#id_tipo_movimiento').val()=='0')
   {
      $('#observaciones').attr('readonly','readonly').removeClass('Editable');
      $('#Guardar').hide();
      $('#Multiproposito').hide();
      $('#BotonEjecutor').hide();
      return false;  
   }
   $('#BotonEjecutor').show();
   switch ($('#id_tipo_movimiento').val())
   {
      case '2':// DISTRIBUCION A UNIDAD ADMINISTRATIVA
               var msj='   -- Escriba el Nombre de la Unidad Administrativa --';   
               $('#observaciones').attr('readonly','readonly').removeClass('Editable');
               $('#Guardar').hide();
               $('#Multiproposito').show();
               $('#Multiproposito').val(msj);
               $("#Multiproposito").focusin(function()
                    {if ($(this).val()==msj){$(this).val('');}}).focusout(function(){
                     if (trim($(this).val())=='')$(this).val(msj);
                     });
               selectorEstructura();
               break;

      case '4':// ASIGNACIÓN A USUARIO
               var msj='   -- Escriba el Nombre del Usuario --';
               $('#observaciones').attr('readonly','readonly').removeClass('Editable');
               $('#Guardar').hide();
               $('#Multiproposito').val(msj);
               $('#Multiproposito').show();               
               $("#Multiproposito").focusin(function()
                    {if ($(this).val()==msj){$(this).val('');}}).focusout(function(){
                     if (trim($(this).val())=='')$(this).val(msj);
                     });
               selectorAnalista();
               break;

      case '9':// Edición de Observaciones
               $('#observaciones').removeAttr('readonly').addClass('Editable').focus();
               $('#Guardar').show();
               $('#Multiproposito').hide();  
               break;         
               
      case '11':// OTRAS ACCIONES
               var msj='   -- Escriba la Acción que desea Registrar --';
               $('#observaciones').attr('readonly','readonly').removeClass('Editable');
               $('#Guardar').hide();
               $('#Multiproposito').show();
               $('#Multiproposito').val(msj);
               $("#Multiproposito").focusin(function()
                    {if ($(this).val()==msj){$(this).val('');}}).focusout(function(){
                     if (trim($(this).val())=='')$(this).val(msj);
                     });
               break;
               
      default: // POR OMISIÓN: '3', '5', '6', '7', '8'
               $('#observaciones').attr('readonly','readonly').removeClass('Editable');
               $('#Guardar').hide();
               $('#Multiproposito').hide();  
               break;                
   }
   return true;   
}

function EjecutarMovimiento()
{
   switch ($('#id_tipo_movimiento').val())
   {
      case '2':// DISTRIBUCION A UNIDAD ADMINISTRATIVA
               if ($('#idMP').val()=='0') return false;
               movimiento_tipo_2();
               break;
      case '3':// RECEPCIÓN EN UNIDAD ADMINISTRATIVA
               movimiento_tipo_3();
               break; 
      case '4':// ASIGNACIÓN A USUARIO
               if ($('#idMP').val()=='0') return false;
               movimiento_tipo_4();               
               break; 
      case '5':// RECEPCIÓN POR EL USUARIO ASIGNADO
               movimiento_tipo_5();
               break;                 
      case '6':// DEVOLUCIÓN DE CORRESPONDENCIA               
               movimiento_tipo_6();
               break; 
      case '7':// ENVÍO A SUPERIOR
               movimiento_tipo_7();
               break;
      case '8':// RECUPERAR CORRESPONDENCIA
               movimiento_tipo_8();
               break;                 
      case '9':// EDICIÓN DE OBSERVACIONES
               movimiento_tipo_9();
               break;                 
      case '11':// OTRAS ACCIONES      
               var msj='   -- Escriba la Acción que desea Registrar --';
               if ($('#Multiproposito').val()==msj || trim($('#Multiproposito').val())=='') return false;
               movimiento_tipo_11();
               break;              
   }
}

function movimiento_tipo_2() // DISTRIBUCION A UNIDAD
{   
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/movimiento_tipo_2',
          data:{
                'id_correspondencia':$('#id_correspondencia').val(),
               'id_estructura_owner':$('#idMP').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Distribuir a Unidad.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Correspondencia Distribuida a Unidad Correctamente';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" ); 
                       $('#id_tipo_movimiento').html(data.movimientos_posibles);
                       $('#Movimientos').html(data.movimientos);
                       limpiar();
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;     
}

function movimiento_tipo_3() // RECEPCIÓN EN UNIDAD ADMINISTRATIVA
{   
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/movimiento_tipo_3',
          data:{
                'id_correspondencia':$('#id_correspondencia').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Recibir la Correspondencia.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Correspondencia Recibida Correctamente';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" ); 
                       $('#Usuario').html(data.usuario);
                       $('#id_tipo_movimiento').html(data.movimientos_posibles);
                       $('#Movimientos').html(data.movimientos);
                        }};
                       limpiar();
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;     
}

function movimiento_tipo_4() // ASIGNAR ANALISTA
{   
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/movimiento_tipo_4',
          data:{
                'id_correspondencia':$('#id_correspondencia').val(),
                'id_usuario_asignado':$('#idMP').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Asignar el Usuario.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Usuario Asignado Correctamente';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" ); 
                       $('#Usuario').html(data.usuario);
                       $('#id_tipo_movimiento').html(data.movimientos_posibles);
                       $('#Movimientos').html(data.movimientos);
                       limpiar();
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;     
}

function movimiento_tipo_5() // RECEPCIÓN POR USUARIO
{   
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/movimiento_tipo_5',
          data:{
                'id_correspondencia':$('#id_correspondencia').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Recibir la Correspondencia.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Correspondencia Recibida Correctamente';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" );      
                       $('#id_tipo_movimiento').html(data.movimientos_posibles);
                       $('#Movimientos').html(data.movimientos);
                       limpiar();
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;     
}

function movimiento_tipo_6() // DEVOLUCIÓN DE CORRESPONDENCIA
{   
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/movimiento_tipo_6',
          data:{
                'id_correspondencia':$('#id_correspondencia').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Error al Intentar Devolver la Correspondencia. Verifique la Unidad Registradora';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Correspondencia Devuelta a Unidad Superior';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" ); 
                       $('#id_tipo_movimiento').html(data.movimientos_posibles);
                       $('#Movimientos').html(data.movimientos);
                       limpiar();
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;     
}

function movimiento_tipo_7() // ENVÍO A SUPERIOR
{   
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/movimiento_tipo_7',
          data:{
                'id_correspondencia':$('#id_correspondencia').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Enviar la Correspondencia.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Correspondencia Enviada a Unidad Superior';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" ); 
                       $('#id_tipo_movimiento').html(data.movimientos_posibles);
                       $('#Movimientos').html(data.movimientos);
                       limpiar();
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;     
}

function movimiento_tipo_8() // RECUPERAR CORRESPONDENCIA
{   
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/movimiento_tipo_8',
          data:{
                'id_correspondencia':$('#id_correspondencia').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Recuperar la Correspondencia.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Correspondencia Recuperada Correctamente';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" ); 
                       $('#Usuario').html(data.usuario);
                       $('#id_tipo_movimiento').html(data.movimientos_posibles);
                       $('#Movimientos').html(data.movimientos);
                        }};
                       limpiar();
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;     
}

function movimiento_tipo_9() // EDICIÓN DE OBSERVACIONES  
{     
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/movimiento_tipo_9',
          data:{
                'id_correspondencia':$('#id_correspondencia').val(),               
                'observaciones':trim($('#observaciones').val())
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Guardar los Cambios.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Observaciones Registradas Correctamente';                       
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" );
                       $('#observaciones').attr('readonly','readonly').removeClass('Editable');
                       $('#Guardar').hide();
                       $('#Movimientos').html(data.movimientos);
                       $('#id_tipo_movimiento').val(0);
                       limpiar();
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;     
}

function movimiento_tipo_11() // OTRAS ACCIONES   
{   
   $.ajax({
          type:'POST',
          url:$('#base_url').val()+'revisar/movimiento_tipo_11',
          data:{
                'id_correspondencia':$('#id_correspondencia').val(),               
                'accion':trim($('#Multiproposito').val())
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Registrar la Acción.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Acción Registrada Correctamente';                       
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" );
                       $('#Multiproposito').val('');
                       $('#Movimientos').html(data.movimientos);
                       $('#id_tipo_movimiento').val(0);
                       limpiar();
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
   return true;     
}

function BorrarCorrespondencia(id_correspondencia)
{
   var Botones={No: function(){$( this ).dialog( "close" )},
           Sí: function(){
    $.ajax({
    type:'POST',
    url:$('#base_url').val()+'revisar/borrar_correspondencia',
    data:{
          'id_correspondencia':id_correspondencia
         },
    beforeSend:function(){$("#cargandoModal").show()},
    complete: function(){
                $("#cargandoModal").hide()},               
    error: function(){
                var Mensaje='No se pudo Eliminar la Correspondencia';
                CajaDialogo('Error', Mensaje)},
    success: function(data){                         
             var Botones={Cerrar: function(){
                     window.close();
                     $( this ).dialog( "close" )}};
             var Mensaje='Producto Eliminado satisfactoriamente.';
             CajaDialogo('Borrado', Mensaje, Botones);},
    dataType:'json'});
    $( this ).dialog( "close" )}
               };
   var Mensaje='¿Está Seguro que desea Eliminar la Correspondencia?';      
   CajaDialogo('Pregunta', Mensaje, Botones);    
}

function limpiar()
{
    $('#idMP').val(0);
    $('#Multiproposito').val('').hide().autocomplete( "destroy" );
    $('#BotonEjecutor').hide();
}

function selectorAnalista()
{  
     $('#Multiproposito').autocomplete({
     minLength:1, //le indicamos que busque a partir de haber escrito dos o mas caracteres en el input
     delay:1,
     position:{my: "left top", at: "left bottom", collision: "flip"},
     source: function(request, response)
             {
                var url=$('#base_url').val()+"revisar/listar_analistas";  //url donde buscará los analistas de la unidad
                $.post(url,{'frase':request.term}, response, 'json');
             },
     select: function( event, ui ) 
             {                  
                $("#Multiproposito").val( ui.item.usuario );                
                $("#idMP").val(ui.item.id_usuario); 
                return false;
             }
   }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )                
                .data( "item.autocomplete", item )
		.append( "<a>" + ((item.usuario==undefined)?'Sin coincidencias':item.usuario) + "<br/><span style='font-size:10px;'>" +((item.nivel==undefined)?'':item.nivel) + "</span></a>" )
		.appendTo( ul );
	  };
  }  
  
  function selectorEstructura()
  {  
     $('#Multiproposito').autocomplete({
     minLength:1, //le indicamos que busque a partir de haber escrito dos o mas caracteres en el input
     delay:1,
     position:{my: "left top", at: "left bottom", collision: "flip"},
     source: function(request, response)
             {  //url donde buscará las unidades inferiores inmediatas
                var url=$('#base_url').val()+"revisar/listar_unidades_distribucion";  
                $.post(url,{'frase':request.term}, response, 'json');
             },
     select: function( event, ui ) 
             {                  
                $("#Multiproposito").val( ui.item.estructura );                
                $("#idMP").val(ui.item.id_estructura); 
                return false;
             }
   }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )                
                .data( "item.autocomplete", item )
		.append( "<a>" + ((item.estructura==undefined)?'Sin coincidencias':item.codigo_estructura+' '
                               +item.estructura) + "<br/><span style='font-size:10px;'>" 
                               +((item.codigo_superior==undefined)?'':item.codigo_superior
                               +' '+item.superior) + "</span></a>" )
		.appendTo( ul );
	  };
  }