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
    
});   // FINAL DEL DOCUMENT READY

function listar_campos(tabla)
{
   if ($('#'+tabla).html()!=''){$('#'+tabla).html('');return false}
   $.ajax({
     type:'POST',
     url:'adm_tablas/listar_campos',
     data:{
           'tabla':tabla               
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Ha Ocurrido un Error al Intentar Cargar los Campos de la tabla.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){                                          
                  $('#'+tabla).html(data);
                   },                   
     dataType:'html'});
    return false;
}

function agregar_campo(tabla)
{
  var fila;
  fila='<div class="EntraDatos">';
  fila+='<table>';
  fila+='<thead>'
  fila+='<tr><th colspan="2">';            
  fila+='Agregar Campos a la Tabla: "'+tabla+'"';  
  fila+='</th></tr>';           
  fila+='</thead>';            
  fila+='<tbody>';
  fila+='<tr><td width="50%">';              
  fila+='<label>Escriba el Nombre del Nuevo Campo:</label><br/>';
  fila+='<input type="text" id="Nombre" class="Editable Campos" tabindex="1000" title="Escriba el Nombre del Campo"/>';
  fila+='</td>';
  fila+='<td>';
  fila+='</td></tr>';
  fila+='<tr><td>';
  fila+='<label>Tipo de Dato:</label><br/>';
  fila+='<select class="Editable Campos" id="Tipo" title="Tipo de Dato" tabindex="1001" ';
  fila+='onchange="javascript:VerificaTipo()"';
  fila+='>';
  fila+='<option selected="selected" value="0">[Seleccione]</option>';
  fila+='<option value="integer">Integer</option>';
  fila+='<option value="bigint">Bigint</option>';
  fila+='<option value="smallint">Smallint</option>';
  fila+='<option value="numeric">Numeric</option>';
  fila+='<option value="boolean">Boolean</option>';
  fila+='<option value="date">Date</option>';  
  fila+='<option value="character">Character</option>';
  fila+='<option value="character varying">Character Varying</option>';
  fila+='<option value="text">Text</option>';
  fila+='</select>';  
  fila+='</td>';
  fila+='<td>';  
  fila+='<label>Tamaño:</label><br/>';
  
    var plus;
    plus=' onkeypress="return onlyDigits(event, this.value,true,false,false,\'.\',\',\',2);"';
    plus+=' onkeyup="return onlyDigits(event, this.value,true,false,false,\'.\',\',\',2);"';
    plus+=' onblur="return onlyDigits(event, this.value,true,false,false,\'.\',\',\',2);"';
    plus+=' readonly="readonly"';
    
  fila+='<input type="text" id="Longitud" class="Editable Campos" tabindex="1002" title="Longitud"'+plus+'/>';  
         
  fila+='</td></tr>';
  fila+='<tr><td>';
  fila+='<label>Nulo:</label><br/>';
  fila+='<select class="Editable Campos" id="Nulo" title="Tipo de Dato" tabindex="1003">';
  fila+='<option selected="selected" value="NOT NULL">No Nulo</option>';
  fila+='<option value="">Nulo</option>';
  fila+='</select>';  
  fila+='</td>';
  fila+='<td>';  
  fila+='<label>Valor por Omisión:</label><br/>';
  fila+='<input type="text" id="Omision" class="Editable Campos" tabindex="1004" title="Valor de Omisión"/>';
  fila+='</td></tr>';
  fila+='</tbody>';
  fila+='<tfoot>';
  fila+='<tr><td colspan="2">';
  fila+='<div class="BotonIco" onclick="javascript:GuardarCampo(\''+tabla+'\')" title="Agregar el Campo">';
  fila+='<img src="imagenes/guardar32.png"/>&nbsp;';   
  fila+='Guardar';
  fila+= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  fila+='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">';
  fila+='<img src="imagenes/cancel.png"/>&nbsp;';
  fila+='Cancelar';
  fila+= '</div>';
  fila+='</td></tr>';
  fila+='</tfoot>';
  fila+='</table>';   
  fila+='</div>';
  $('#VentanaModal').html(fila);
  $('#VentanaModal').show();           
  $('#nombreNvo').focus();
}

function GuardarCampo(tabla)
{
   if($('#Nombre').val()=='' || $('#Tipo').val()==0)
      {
        var Mensaje='Debe llenar los campos correctamente.';  
        CajaDialogo("Alerta", Mensaje);
        return false;
      }       
  
   $.ajax({
     type:'POST',
     url:'adm_tablas/guardar_campo',
     data:{
           'tabla':tabla,
           'nombre':$("#Nombre").val(),
           'tipo':$("#Tipo").val(),
           'longitud':$("#Longitud").val(),
           'nulo':$("#Nulo").val(),
           'omision':$("#Omision").val()
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Ha Ocurrido un Error al Intentar Agregar el Campo en la tabla.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){                                          
                 var Mensaje='Se ha Agregado el campo correctamente.';
                 var Botones={Cerrar: function(){
                  $('#'+tabla).html('');                     
                     CancelarModal();                       
                     $( this ).dialog( "close" )}};
                 CajaDialogo('Guardar', Mensaje, Botones);
                   },                   
     dataType:'text'});
    return false;
}

function BorrarCampo(tabla,campo)
{    
    var Botones={No: function(){$( this ).dialog( "close" )},
                   Sí: function(){ 
     $.ajax({
     type:'POST',
     url:'adm_tablas/borrar_campo',
     data:{
           'tabla':tabla,
           'campo':campo
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Ha Ocurrido un Error al Intentar Borrar el Campo.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){                                          
                 var Mensaje='Se ha Borrado el campo correctamente. ';
                 var Botones={Cerrar: function(){
                    $('#'+tabla).html('');                  
                     CancelarModal();                       
                     $( this ).dialog( "close" )}};
                 CajaDialogo('Borrado', Mensaje, Botones);
                   },                   
     dataType:'text'});
     
     $( this ).dialog( "close" )}
                  };                   
    var Mensaje='¿Está Seguro que desea Eliminar el campo?';      
    CajaDialogo('Pregunta', Mensaje, Botones);    
}

function VerificaTipo()
{
    if ($('#Tipo').val()=='character' || $('#Tipo').val()=='character varying')
    {
      $('#Longitud').removeAttr('readonly');
      $('#Longitud').focus();
    }
    else
    {
      $('#Longitud').val('');  
      $('#Longitud').attr('readonly','readonly');
    }
}

function CorrerSQL()
{   
   if(trim($('#sql').val())=='')
      {
        var Mensaje='Debe llenar el campo correctamente.';  
        CajaDialogo("Alerta", Mensaje);
        return false;
      }       
  
  $.ajax({
     type:'POST',
     url:'adm_tablas/correr_sql',
     data:{
           'sql':trim($('#sql').val())
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Ha Ocurrido un Error al correr la consulta.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){                                          
                 $('#Resultado').html(data);
                 $('#Tabla').dataTable( {
                        "sPaginationType": "full_numbers"                        
			} );
                   },                   
     dataType:'html'}); 
    return false;
}