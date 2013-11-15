<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>

  <div class="clear"></div>
  <!--  Contenedor de Información de la Correspondencia-->
  <div class="grid_16 Formulario"> 
    <table>
      <thead>
        <tr>
         <th colspan="4">
           <div style="position: relative;">Correspondencia:&nbsp;
               <?php echo $c->codigo_generado; echo form_input($id_correspondencia);?>
            <div style="display:inline-block; position:absolute; top:-4px; right:0px;">
                <?php echo $botonsalir;?>
            </div>
           </div>
         </th>      
        </tr>    
      </thead>
      <tbody>
        <tr>
          <td colspan="4">           
            <legend><?php echo (($c->entrante=='t')?'Procedencia':'Destino');?></legend>  <!-- PROCEDENCIA / DESTINO-->                  
            <div class="SubFormulario">
              <?php echo trim($c->organismo);?>                          
              <span style="font-size: .9em;">
                <?php echo (trim($c->pertenece_a)!='')?(' - '.trim($c->pertenece_a)):'';?>
              </span>
              <br/>
              <label><?php echo (($c->entrante=='t')?'Envía:':'Recibe:');?>&nbsp;</label> 
                       <?php echo trim($c->remitente_receptor);?>
            </div>           
          </td>
        </tr>
        <tr>
          <td rowspan="2" style="width:235px">
             <legend>Archivo Adjunto</legend>          
             <div class="SubFormulario" style="text-align: center; height: 200px; position: relative;">               
               <?php if(trim($c->ruta_archivo)=='') 
                     { 
                       $imagen=array(
                                 'src' => base_url().'imagenes/nodoc.png',  
                                 'alt' => $c->codigo_generado,
                               'title' => 'No Posee Imagen Digitalizada',
                               'style' => 'max-width:150px; max-height:150px'
                        );
                   
                       $img=br(2).img($imagen);
                     }
                     else
                     {
                       $ruta_archivo=(substr($c->ruta_archivo, -3)=='pdf')?
                                      (base_url().'imagenes/pdf.png"'):
                                      (base_url().substr($c->ruta_archivo, 2).'" height="200px"');
                       $img='<a target="_blank" href="'.base_url().substr($c->ruta_archivo, 2)
                               .'" title="Clic para ver Digitalización">';                      
                       $img.='<img src="'.$ruta_archivo.
                               ' alt="Imagen Digitalizada" />';
                       $img.='</a>';                    
                     }           
                     // AGREGAMOS EL ICONO SUPERPUESTO DE CAMBIAR ARCHIVO
                     if ($permiso_adjuntar)
                     {
                       $img.='<div style="position: absolute; bottom: 10px; right: 10px; z-index:5;">';
                       $img.='<a  target="_parent" href="'.base_url().'registrar/adjuntar/'
                                .$c->id_correspondencia
                                .'" title="Clic para Reemplazar el Archivo Adjunto" >';
                       $img.='<img src="'.base_url().'imagenes/scanner48.png"/>';                       
                       $img.='</a>';                       
                       $img.='</div>';                           
                     }                        
                     echo (substr($c->ruta_archivo, -3)=='pdf')?br(2):'';
                     echo $img;
               ?>               
             </div>                
          </td>
          <td width="25%">
             <legend>Fecha</legend> 
             <table class="SubFormulario">                   
                 <tbody>
                     <tr>
                         <td>
                             <label><?php echo (($c->entrante=='t')?'Recibido:':'Enviado:'); ?>&nbsp;</label>
                         </td>
                         <td style="padding: 5px 5px 0px 0px;">
                              <?php echo Date("d/m/Y",strtotime($c->fecha_recepcion));?>
                         </td>                    
                     </tr>
                     <tr>                         
                         <td>                         
                         </td> 
                     </tr>
                     <tr>
                         <td>
                             <label>Emitido:</label>
                         </td>
                         <td style="padding: 5px 5px 0px 0px;">
                             <?php  echo Date("d/m/Y",strtotime($c->fecha_emision));?>
                         </td>                         
                     </tr>
                     <tr> 
                         <td>                             
                         </td> 
                     </tr>                                  
                 </tbody>
             </table>             
          </td>
          <td style="min-width:25%">
            <legend>Identificación</legend>
             <table class="SubFormulario">
                 <tbody>
                     <tr>
                         <td>
                             <label>Nº Com:</label>                                
                         </td>
                         <td style="padding-left: 0px">
                         <?php echo $c->nro_comunicado;?>    
                         </td>                         
                     </tr>
                     <tr>
                         <td> 
                         </td>
                     </tr>
                     <tr>
                         <td>
                             <label>Cód Interno:</label>
                         </td>
                         <td style="padding-left: 0px">
                         <?php echo $c->codigo_interno;?>    
                         </td>                         
                     </tr>
                     <tr>
                         <td>                             
                         </td>
                     </tr>                      
                 </tbody>
             </table>            
          </td>
          <td style="max-width:25%">                
            <legend>Tipo</legend>
            <div class="SubFormulario" style="text-align: center; padding: 0px">
             <?php $tipo='<img src="'.base_url().$c->icon_tipo.'" title="'.$c->tipo.'" height="53px"/>';
                   echo $tipo;?>    
            </div>
          </td>          
        </tr>
        <tr>
          <td colspan="3">
            <legend>Contenido</legend>
            <div class="SubFormulario" style="min-height: 116px;">
              <label>Asunto:</label>&nbsp;<?php echo trim($c->asunto);?><br/>
              <label>Observaciones:</label>
              <div style="width:100%; text-align:center;">
                  <?php echo form_textarea($observaciones);?>
                  <input type="button" id="Guardar" value="Guardar" onclick="javascript:EjecutarMovimiento();" />
              </div>            
            </div>
          </td>          
        </tr>
        <tr>
          <td colspan="4">
            <div style="display: inline-block; margin-right: 5px; width:235px">
              <legend>Clasificación</legend>
              <div class="SubFormulario" style="text-align: center;">
                  <?php echo $clasificacion;?>
              </div>
            </div>              
            <div style="display:inline-block; margin-right: 5px; width: 235px">
              <legend>Estatus</legend>
              <div class="SubFormulario" style="text-align: center;">
                  <?php echo $estatus;?>
              </div>
            </div>              
            <div style="display: inline-block; margin-right: 5px; width: 270px">
              <legend>Ubicación Física</legend>
              <div class="SubFormulario" style="text-align: center;">
                 <?php echo form_input($lugar_archivo);
                       if($permisoEscribir) echo '<input id="BotonCambiarLugar" type="button" value="Cambiar" />'
                 ?>
              </div>
            </div>
            <div style="display:inline-block; margin-right: 5px; width: 140px">
              <legend>Analista Asignado</legend>
              <div id="Usuario" class="SubFormulario" style="text-align: center;">
                  <?php echo mb_convert_case($c->nombre.' '.$c->apellido,MB_CASE_TITLE);?>
              </div>
            </div>                
          </td>
        </tr>
<!--  Contenedor de Movimientos de la Correspondencia-->        
        <tr>
          <td colspan="4">            
            <legend>Registro de Movimientos</legend>
            <div id="Movimientos" class="SubFormulario" style="text-align: center;">
                <?php echo $movimientos;?>
            </div>
          </td>
        </tr> 
<!--  Contenedor de Acciones a Tomar -->
        <tr>
          <td colspan="4">
            <legend>Acción a Tomar</legend>           
            <table class="SubFormulario">
              <tr>
               <td style="width:30%;">
                 <?php echo $acciones;?>
               </td>
               <td >
                 <input type="hidden"  id="idMP" value="0" />
                 <input type="text" id="Multiproposito" class="CampoFicha Editable"/>
               </td>
               <td style="width:166px; height: 48px;">
                <div style="position: relative; width: 100%; height: 38px;">
                   <div style="position: absolute; width:32px; top:2px; right: 20px;">
                        <?php if ($permisoBorrar) echo $botonborrar;?>
                   </div>                      
                   <div id="BotonEjecutor">
                        <?php echo $botonejecutar;?>   
                   </div>                                          
                </div>
               </td>
              </tr>
            </table>
          </td>
        </tr>         
      </tbody>
    </table>      
  </div>  
  <div class="clear"></div>
  <div class="grid_16">&nbsp;<br/></div>
  <div class="clear"></div>
  <div class="grid_16 AjusteBottom"></div> 
  <div class="clear"></div>
</div>
<div class="clear"></div>  