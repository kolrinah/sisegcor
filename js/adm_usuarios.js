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
    $('#usuarios').dataTable( {
	"sPaginationType": "full_numbers",
        "aaSorting": [[ 3, "asc" ],[ 2, "asc" ]]        
			} );
});   // FINAL DEL DOCUMENT READY

// FUNCIONES ESPECIALES
function AgregarUsuario(admin)
{  
  var form;
  form='<div class="EntraDatos">';
  form+='<table>';
  form+='<thead>';
  form+='<tr><th colspan="2">';            
  form+='Nuevo Usuario';  
  form+='</th></tr>';           
  form+='</thead>';            
  form+='<tbody>';
  form+='<tr>';
  form+='<td width="50%">';              
  form+='<label>Cédula de Identidad:</label><br/>';
  form+='<input type="text" id="CI" class="Editable" tabindex="10" title="Introduzca el Número de Cédula"/>';
  form+='<input type="button" onclick="javascript:BuscarUsuario()" tabindex="1001" title="Buscar" value="Buscar"/>';
  form+='</td>';
  form+='<td>';
  form+='<label>Correo Electrónico:</label><br/>';
  form+='<input type="text" class="Campos" id="Correo" tabindex="11" title="Correo Electrónico" readonly="readonly"/>';
  form+='</td>';
  form+='</tr>';
  form+='<tr>';
  form+='<td>';
  form+='<label>Nombre:</label><br/>';
  form+='<input type="text" class="Campos" id="Nombre" tabindex="12" title="Nombre" readonly="readonly"/>';
  form+='</td>';
  form+='<td>';
  form+='<label>Apellido:</label><br/>';
  form+='<input type="text" class="Campos" id="Apellido" tabindex="13" title="Apellido" readonly="readonly"/>';
  form+='</td>';
  form+='</tr>';  
  form+='<tr>';
  form+='<td colspan="2">';
  form+='<input type="hidden" id="id_unidad" />';  
  form+='<label>Unidad Administrativa:</label><br/>';
  form+='<center><input type="text" class="Campos Editable" id="Unidad" tabindex="14" title="Unidad Administrativa"/></center>';
  form+='</td>';  
  form+='</tr>';
  form+='<tr>';
  form+='<td>';
  form+='<label>Nivel de Usuario:</label><br/>';
  form+='<select class="Campos Editable" id="Nivel" title="Seleccione el Nivel del Usuario" tabindex="15">';
  form+='<option selected="selected" value="0">[Seleccione]</option>';
  form+='</select>';
  form+='</td>';
  form+='<td>';
    if (admin==1)
    {
      form+='<label>Rol de Usuario:</label><br/>';
      form+='<div class="ToggleBoton" onclick="javascript:ToggleBotonAdmin()" title="Haga clic para cambiar">';
      form+='<img id="imgAdmin" src="imagenes/user16.png"/>';
      form+='</div>';
      form+='<span id="spanAdmin">&nbsp;Usuario Normal</span>';      
    }
  form+='<input type="hidden" id="hideAdmin" value="f" />';     
  form+='</td>';
  form+='</tr>';       
  form+='</tbody>';
  
  form+='<tfoot>';
  form+='<tr><td colspan="2">';
  form+='<div class="BotonIco" onclick="javascript:GuardarUsuario()" title="Guardar Usuario">';
  form+='<img src="imagenes/guardar32.png"/>&nbsp;';   
  form+='Guardar';
  form+= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  form+='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">';
  form+='<img src="imagenes/cancel.png"/>&nbsp;';
  form+='Cancelar';
  form+= '</div>';
  form+='</td></tr>';
  form+='</tfoot>';
  form+='</table>';   
  form+='</div>';
  $('#VentanaModal').html(form);
  $('#VentanaModal').show();           
  $('#CI').focus();
  
  selector_autocompletar();  
}

function GuardarUsuario()
{
  if (!verificarEmail($('#Correo').val()) ||
      !verificarCI($('#CI').val()) ||
     // !verificarNombre($('#Nombre').val()) ||
     // !verificarNombre($('#Apellido').val()) ||
      $('#id_unidad').val()=='' || $('#Nivel').val()=='0'
      ) 
  {
    var Mensaje='Debe llenar los campos correctamente para guardar el Usuario.';  
    CajaDialogo("Alerta", Mensaje);
    return false;
  }

  $.ajax({
          type:'POST',
          url:'adm_usuarios/insertar_usuario',
          data:{
                'cedula':$('#CI').val(),
                'nombre':$('#Nombre').val(),
                'apellido':$('#Apellido').val(),
                'correo':$('#Correo').val(),
                'id_estructura':$('#id_unidad').val(),
                'id_nivel':$('#Nivel').val(),
                'administrador':$('#hideAdmin').val()
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Ha Ocurrido un Error al Intentar Guardar el Usuario.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){                  
                   var Mensaje='El Usuario se ha Guardado Correctamente.';
                   var Botones={Cerrar: function(){                       
                       CancelarModal();
                       window.location.reload( true );
                       $( this ).dialog( "close" )}};
                   CajaDialogo('Guardar', Mensaje, Botones);},
          dataType:'text'});         
  return false;
}

function BuscarUsuario()
{
  if (!(verificarCI($('#CI').val()) || verificarEmail($('#CI').val())))
  {    
    var Mensaje='Debe Introducir Datos Válidos';  
    CajaDialogo("Alerta", Mensaje);
    $('#CI').val('');
    return false; 
  }  
  
  $.ajax({
          type:'POST',
          url:'adm_usuarios/buscar_usuario',
          data:{
                'patron':trim($('#CI').val())                 
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Error Interno del Servidor.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){
                      if (data==1)
                      {
                        var Mensaje='Usuario Ya Existe.';
                        var Botones={Cerrar: function(){
                            $( this ).dialog( "close" )}};
                        CajaDialogo('Error', Mensaje, Botones);        
                      }
                      else
                      {
                       // Desbloquea(); // COMENTAR AL USAR LDAP
                        BuscarLDAP();     
                      }
                                },
          dataType:'text'});          
  return false;  
}

function Desbloquea()
{
    $('#CI').removeClass('Editable').attr('readonly', 'readonly');
    $('#Nombre').addClass('Editable').removeAttr("readonly");
    $('#Apellido').addClass('Editable').removeAttr("readonly");
    $('#Correo').addClass('Editable').removeAttr("readonly");
    $('#Correo').focus();
}

function BuscarLDAP()
{
   $.ajax({
          type:'POST',
          url:'adm_usuarios/buscarLDAP',
          data:{
                'patron':trim($('#CI').val()) 
               },
          beforeSend:function(){$("#cargandoModal").show()},
          complete: function(){
                      $("#cargandoModal").hide()},               
          error: function(){
                      var Mensaje='Error Interno del Servidor.';
                      CajaDialogo('Error', Mensaje)},
          success: function(data){                      
                      if (data.count!=0)
                      {                        
                        $('#CI').val(trim(data.cedula));
                        $('#CI').attr('readonly','readonly');
                        $('#Correo').val(trim(data.mail));
                        $('#Nombre').val(trim(data.nombre));
                        $('#Apellido').val(trim(data.apellido));
                      }
                      else
                      {
                        var Mensaje='Usuario No Encontrado en LDAP.';
                        var Botones={Cerrar: function(){
                            $( this ).dialog( "close" )}};
                        CajaDialogo('Error', Mensaje, Botones);        
                      }
                                  },
          dataType:'json'});          
  return false;   
}

function EditarUsuario(id_usuario)
{
   $.ajax({
      type:'POST',
      url:'adm_usuarios/editar_usuario',
      data:{'id_usuario':id_usuario},
      beforeSend:function(){$("#cargandoModal").show()},
      complete: function(){
                  $("#cargandoModal").hide()},               
      error: function(){
                  var Mensaje='Error Interno del Servidor.';
                  CajaDialogo('Error', Mensaje)},
      success: function(data){
                  $('#VentanaModal').html(data);
                  $('#VentanaModal').show();
                  
                  selector_autocompletar();                                     
                             },
      dataType:'html'});
   return false; 
}

function ActualizarUsuario(id_usuario)
{
  if($('#CI').val()=='' || $('#id_unidad').val()=='' || $('#Nivel').val()=='0' || $('#Correo').val()=='')
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }    
  $.ajax({
     type:'POST',
     url:'adm_usuarios/actualizar_usuario',
     data:{
           'id_usuario':id_usuario,
           'id_estructura':$('#id_unidad').val(),
           'id_nivel':$('#Nivel').val(),
           'administrador':$('#hideAdmin').val(),
           'activo':$('#hideActivo').val()
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Ha ocurrido un error al Actualizar el Usuario.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){
              var Mensaje='Se han guardado los cambios correctamente.';
              var Botones={Cerrar: function(){                  
                   CancelarModal();                    
                   window.location.reload( true );
                   $( this ).dialog( "close" )}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;  
}  

function ResetearClave(id_usuario)
{
  if($('#CI').val()=='' || $('#id_unidad').val()=='' || $('#Nivel').val()=='0' || $('#Correo').val()=='')
  {
    var Mensaje='Debe completar todos los campos para guardar los cambios.';  
    CajaDialogo("Alerta", Mensaje);    
    return false;
  }    
  $.ajax({
     type:'POST',
     url:'adm_usuarios/resetear_clave',
     data:{
           'id_usuario':id_usuario
          },
     beforeSend:function(){$("#cargandoModal").show()},
     complete: function(){
                 $("#cargandoModal").hide()},               
     error: function(){
                 var Mensaje='Ha ocurrido un error al Actualizar el Usuario.';
                 CajaDialogo('Error', Mensaje)},
     success: function(data){
              var Mensaje='Contraseña reiniciada correctamente.<br/>La nueva contraseña es "123"';
              var Botones={Cerrar: function(){
                  
                   CancelarModal();                    
                   
                   $( this ).dialog( "close" )}};
              CajaDialogo('Guardar', Mensaje, Botones);},
     dataType:'text'});   
  return false;  
}

function ToggleBotonAdmin()
{
   if ($("#hideAdmin").val()=='t')
   {
     $("#hideAdmin").val('f');      
     $("#imgAdmin").attr('src', $('#base_url').val()+'imagenes/user16.png');
     $("#spanAdmin").html('&nbsp;Usuario Normal');
   }
   else
   {
     $("#hideAdmin").val('t');      
     $("#imgAdmin").attr('src', $('#base_url').val()+'imagenes/admin16.png');
     $("#spanAdmin").html('&nbsp;Administrador');
   }
}

function ToggleBotonActivo()
{
   if ($("#hideActivo").val()=='t')
   {
     $("#hideActivo").val('f');      
     $("#imgActivo").attr('src', $('#base_url').val()+'imagenes/cancel16.png');
     $("#spanActivo").html('&nbsp;Usuario Inactivo');
   }
   else
   {
     $("#hideActivo").val('t');      
     $("#imgActivo").attr('src', $('#base_url').val()+'imagenes/activo16.png');
     $("#spanActivo").html('&nbsp;Usuario Activo');
   }
}

function selector_autocompletar()
{
    $("#Unidad").autocomplete({
        minLength:1,
        delay:3,
        source: function(request, response)
                {
                  var url=$('#base_url').val()+"adm_usuarios/listar_unidades";  //url donde buscará las oficinas
                  $.post(url,{'frase':request.term}, response, 'json');
                },
        select: function( event, ui ) 
                {                    
                  var unidad='';
                  var combo='<option selected="selected" value="0">[Seleccione]</option>';
                  switch (parseInt(ui.item.tipo))
                  {                       
                   case 1: // Vicepresidente
                     unidad=ui.item.codigo_estructura+' - '+ui.item.estructura;
                     combo+='<option value="7">Revisor</option>';
                     combo+='<option value="6">Analista</option>';
                     combo+='<option value="5">Distribuidor</option>';
                     combo+='<option value="1">Ministro / Canciller</option>';
                     break;
                   case 2: // Director General 
                     unidad=ui.item.codigo_estructura+' - '+ui.item.estructura;
                     combo+='<option value="7">Revisor</option>';
                     combo+='<option value="6">Analista</option>';
                     combo+='<option value="5">Distribuidor</option>';
                     combo+='<option value="2">Viceministro / Director General</option>';
                     break;
                   case 3: // Director de Línea
                     unidad=ui.item.codigo_estructura+' - '+ui.item.estructura+' / '+ui.item.oficina;
                     combo+='<option value="7">Revisor</option>';
                     combo+='<option value="6">Analista</option>';
                     combo+='<option value="5">Distribuidor</option>';
                     combo+='<option value="3">Director de Línea</option>';
                     break;
                   case 4: // Coordinador
                     unidad=ui.item.codigo_estructura+' - '+ui.item.estructura+' / '+ui.item.oficina;
                     combo+='<option value="7">Revisor</option>';
                     combo+='<option value="6">Analista</option>';
                     combo+='<option value="5">Distribuidor</option>';
                     combo+='<option value="4">Coordinador de Área</option>';
                     break;                     
                   default: // Funcionario
                     unidad=ui.item.codigo_estructura+' - '+ui.item.estructura+' / '+ui.item.oficina;
                     combo+='<option value="7">Revisor</option>';
                     combo+='<option value="6">Analista</option>';
                     combo+='<option value="5">Distribuidor</option>';
                     break;
                  }
                  $("#Unidad").val( unidad );                  
                  $("#id_unidad").val(ui.item.id);   
                  $("#Nivel").html(combo);   
                  return false;
                }
      }).data( "autocomplete" )._renderItem = function( ul, item ) {
                return $( "<li></li>" )                
                .data( "item.autocomplete", item )
		.append( "<a>" + ((item.estructura==undefined)?'Sin coincidencias':item.estructura) + "<br/><span style='font-size:10px;'>" +((item.oficina==undefined)?'':item.oficina) + "</span></a>" )
		.appendTo( ul );
	  };
   uniMsj='-- Escriba aquí el nombre de la Unidad donde pertenece el Usuario --';
   if ($('#Unidad').val()=='')
   {
       $('#Unidad').val(uniMsj);        
   }  
   $('#Unidad').focusin(function()
       {if ($(this).val()==uniMsj){$(this).val('');}}).focusout(function(){
        if ($(this).val()=='')
        {
            $(this).val(uniMsj);
            $("#id_unidad").val('');   
            $("#Nivel").html('<option selected="selected" value="0">[Seleccione]</option>');
        }});     
}