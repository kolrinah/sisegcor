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
    $("#tipo_organismo").focus();

    // INICIALIZACIÓN DE CAMPOS DE FECHA  
    $( "#fecha_recepcion" ).datepicker({
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
              minDate:$( "#fecha_emision" ).val(),
              maxDate: _DiaHoy(),
              dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
              dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
              monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
              onSelect: function( selectedDate ) {
                  $( "#fecha_emision" ).datepicker( "option", "maxDate", selectedDate );
                  }            
                       });
    $( "#fecha_emision" ).datepicker({
              showOn: "both",
              buttonImage: $('#base_url').val()+"imagenes/cal.gif",
              buttonText:"clic para Seleccionar",
              buttonImageOnly: true,
              showOtherMonths: true,
              selectOtherMonths: true,
              dateFormat:"dd/mm/yy",
              currentText:"Hoy",
              nextText:"Sig",        
              //defaultDate:$('#fechaI').val()+5,
              //minDate:$('#fecha_recepcion').val(),
              maxDate:$('#fecha_recepcion').val(),
              dayNames:[ "Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado" ],
              dayNamesMin:[ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
              monthNames:["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
              onSelect: function( selectedDate ) {
                  $( "#fecha_recepcion" ).datepicker( "option", "minDate", selectedDate );
                  }
          });
  
  // AUTOCOMPLETAR
  var enteMsj='   -- Escriba el Nombre del Organismo que '+$("#ente").attr('e_s')+' la Correspondencia --';        
 
  if (trim($("#ente").val())=='')
  {
    $("#ente").val(enteMsj);
  }
   
  $("#ente").focusin(function()
  {if ($(this).val()==enteMsj){$(this).val('');}}).focusout(function(){
      if (trim($(this).val())==''  || $("#id_organismo").val()=='0')
      {$(this).val(enteMsj);
       $("#id_organismo").val(0)}});
  
  // AL SELECCIONAR TIPO DE ORGANISMO
  $("#tipo_organismo").change(function(){
      if ($(this).val()!=0){
         $("#ente").removeAttr('disabled'); 
         $("#ente").val(enteMsj);
         $.ajax({
          type:'POST',
          url:$('#base_url').val()+'registrar/cuantos_organismos',
          data:{
                'id_tipo_organismo':$("#tipo_organismo").val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Cargar la Data.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                    $("#id_organismo").val((data.id_organismo!=0)?data.id_organismo:'0') ;
                    $("#ente").val((data.id_organismo!=0)?data.organismo:$("#ente").val()); 
                   },               
          dataType:'json'});
          return false;
      }
      else
      {
         $("#ente").attr('disabled','disabled'); 
         $("#ente").val('   -- Seleccione Primero el tipo de Organismo que '+$("#ente").attr('e_s')+' la Correspondencia --');
         $("#id_organismo").val('0');
      }
   });
   
   selectorOrganismo(); 
   
});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES
function selectorOrganismo()
{  
     $('#ente').autocomplete({
     minLength:1, //le indicamos que busque a partir de haber escrito dos o mas caracteres en el input
     delay:1,
     position:{my: "left top", at: "left bottom", collision: "none"},
     source: function(request, response)
             {
                var url=$('#base_url').val()+"registrar/listar_organismos";  //url donde buscará los organismos
                $.post(url,{'frase':request.term,'tipo_organismo':$("#tipo_organismo").val()}, response, 'json');
             },
     select: function( event, ui ) 
             {                  
                $("#ente").val( ui.item.organismo );                
                $("#id_organismo").val(ui.item.id_organismo); 
                return false;
             }
   }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )                
                .data( "item.autocomplete", item )
		.append( "<a>" + ((item.organismo==undefined)?'Sin coincidencias':item.organismo) +
                         "<br/><span style='font-size:10px;'>" +
                         ((item.pertenece_a==undefined)?'':item.pertenece_a) + "</span></a>" )
		.appendTo( ul );
	  };
}

// VERIFICAMOS QUE LA CORRESPONDENCIA NO ESTE REPETIDA
function BuscarCorrespondencia(entrante)
{
  if (verificarFormulario()===false)
  {
    var Mensaje='Debe llenar los campos necesarios de manera correcta para poder registrar.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  } 
  entrante=(entrante==='entrante')?'t':'f'; 
  $.ajax({
          type:'POST',
          url:$('#base_url').val()+'registrar/buscar_correspondencia',
          data:{
                'id_organismo':$('#id_organismo').val(),                
                'nro_comunicado':$('#nro_comunicado').val(),                
                'fecha_emision':$('#fecha_emision').val(),                
                'entrante':entrante // 't' o 'f'
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Buscar la Correspondencia.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                     
                      if (data.cantidad==0)  // SI NO HAY REPETIDAS POSIBLES REGISTRAMOS
                      {
                         RegistrarCorrespondencia(data.entrante);                         
                      }
                      else
                      {
                         var Mensaje='La Correspondencia parece estar Repetida.';
                         var Botones={
                               Revisar:function(){  
                                        var entrante=(data.entrante=='entrante')?'t':'f';
                                        window.open($('#base_url').val()+'bandeja/repetidas/'
                                            +data.id_organismo+'/'
                                            +data.nro_comunicado+'/'
                                            +data.fecha_emision+'/'
                                            +entrante,'','',false);
                                        $( this ).dialog( "close" );},
                               Ignorar:function(){
                                        RegistrarCorrespondencia(data.entrante);
                                        $( this ).dialog( "close" );
                                                 }
                                     };
                         CajaDialogo('Alerta', Mensaje, Botones);
                      }                      
                   },
          dataType:'json'});
  return true;
}

function RegistrarCorrespondencia(entrante)
{
  if (verificarFormulario()==false)
  {
    var Mensaje='Debe llenar los campos necesarios de manera correcta para poder registrar.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  } 
  entrante=(entrante=='entrante')?'t':'f'; 
  $.ajax({
          type:'POST',
          url:$('#base_url').val()+'registrar/registrar_correspondencia',
          data:{
                'id_organismo':$('#id_organismo').val(),
                'remitente_receptor':trim($('#remitente_receptor').val()),
                'nro_comunicado':$('#nro_comunicado').val(),
                'codigo_interno':$('#codigo_interno').val(),
                'id_tipo':$('#id_tipo').val(),
                'id_clasificacion':$('#id_clasificacion').val(),
                'fecha_recepcion':$('#fecha_recepcion').val(),
                'fecha_emision':$('#fecha_emision').val(),
                'asunto':trim($('#asunto').val()),
                'observaciones':trim($('#observaciones').val()),
                'entrante':entrante // 't' o 'f'
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Registrar la Correspondencia.';
                      CajaDialogo('Error', Mensaje);
                      return false},
          success: function(data){                          
                   var Mensaje='Correspondencia Registrada bajo el Código: ';
                       Mensaje+='<span style="font-size: 1.2em; font-weight: bold;">';
                       Mensaje+=data.codigo_generado+'</span>';
                   var Botones={Cerrar: function(){                       
                       $( this ).dialog( "close" );
                       // REDIRECCIONAMOS A REVISAR CORRESPONDENCIA PARA DISTRIBUIR                       
                       $('form').submit();
                       window.open($('#base_url').val()+'revisar/correspondencia/'
                           +data.id_correspondencia,'','',false);                       
                        }};
                   CajaDialogo('Exito', Mensaje, Botones);
                   },               
          dataType:'json'});         
  return true;
}

function verificarFormulario()
{
  var verificacion;
  verificacion=true;  
  if($('#id_organismo').val()==0) 
      {$('#ente').parent().addClass('CampoInvalido'); verificacion=false;}
  else $('#ente').parent().removeClass('CampoInvalido');
    
  if(trim($('#remitente_receptor').val()).length<3) 
      {$('#remitente_receptor').parent().addClass('CampoInvalido'); verificacion=false;}
  else $('#remitente_receptor').parent().removeClass('CampoInvalido');
      
  if($('#id_tipo').val()==0) 
      {$('#id_tipo').parent().addClass('CampoInvalido'); verificacion=false;}
  else $('#id_tipo').parent().removeClass('CampoInvalido');

  if($('#id_clasificacion').val()==0) 
      {$('#id_clasificacion').parent().addClass('CampoInvalido'); verificacion=false;}
  else $('#id_clasificacion').parent().removeClass('CampoInvalido');
     
  if(trim($('#asunto').val()).length<3) 
      {$('#asunto').parent().addClass('CampoInvalido'); verificacion=false;}
  else $('#asunto').parent().removeClass('CampoInvalido');
  
  if($('#id_usuario').val()==0) 
      {$('#id_usuario').parent().addClass('CampoInvalido'); verificacion=false;}
  else $('#id_usuario').parent().removeClass('CampoInvalido');
  
  return verificacion;
}

// FUNCIONES A REVISAR  

function ToggleBotonGantt()
{
   if ($("#hideGantt").val()=='t')
   {
     $("#hideGantt").val('f');      
     $("#imgGantt").attr('src', 'imagenes/tabla.png');
     $("#imgGantt").attr('title', 'Visualizar Planificación en Diagrama Gantt');
     
   }
   else
   {
     $("#hideGantt").val('t');      
     $("#imgGantt").attr('src', 'imagenes/gantt16.png');
     $("#imgGantt").attr('title', 'Visualizar Planificación en Tabla');
   }
}