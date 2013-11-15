<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>
  <div class="clear"></div>
  <div class="grid_16" id="Filtros">    
  </div>  
  <div class="clear"></div>
  <div class="grid_16 Formulario">   
  <form action='' method="post" accept-charset="utf-8" autocomplete="on">
    <table>
      <thead>
        <tr>
          <th colspan="3">
            <div style="position: relative;">  
              <?php echo $titulo_formulario;?>
              <div style="display:inline-block; position:absolute; top:-4px; right:0px;">
                <?php echo $botonsalir;?>
              </div>          
            </div>
          </th>      
        </tr>    
      </thead>
      <tbody>
        <tr>
          <td colspan="3">
             <legend><?php echo $leyenda;?></legend>  <!-- PROCEDENCIA / DESTINO-->
             <table class="SubFormulario">                 
                 <tbody>
                     <tr>
                       <td>
                           <label>Tipo de Organismo:</label>                                
                       </td>
                     </tr>
                     <tr>
                       <td>
                          <?php echo $tipo_organismo;?>
                       </td>
                     </tr>  
                     <tr>
                       <td>
                          <label><?php echo $ente['label']; ?></label>
                       </td>
                     </tr>   
                     <tr>
                       <td>
                        <div>
                         <?php echo form_input($id_organismo);
                               echo form_input($ente);?>  
                        </div>
                       </td>
                     </tr>  
                     <tr>
                       <td>                         
                         <label><?php echo $remitente['label']; ?></label>                        
                       </td>
                     </tr>
                     <tr>
                       <td>
                        <div>
                         <?php echo form_input($remitente);?>    
                        </div>      
                       </td>
                     </tr>
                 </tbody>
             </table>            
          </td>
        </tr>
        <tr>
          <td>
            <legend>Identificación</legend>
             <table class="SubFormulario">
                 <tbody>
                     <tr>
                         <td>
                             <label>Nº Comunicado:</label>                                
                         </td>
                     </tr>
                     <tr>
                         <td>
                         <?php echo form_input($nro_comunicado);?>    
                         </td>
                     </tr>                    
                     <tr>
                         <td>
                             <label>Código Interno MPPRE:</label>
                         </td>
                     </tr>
                     <tr>
                         <td>
                         <?php echo form_input($codigo_interno);?>    
                         </td>
                     </tr>                      
                 </tbody>
             </table>            
          </td>      
          <td>
            <legend>Clasificación</legend>
             <table class="SubFormulario">
                 <tbody>
                     <tr>
                         <td>                           
                           <label>Tipo de Correspondencia:</label>
                         </td>
                     </tr>
                     <tr>
                         <td>
                           <div>
                            <?php echo $tipo;?> 
                           </div>    
                         </td>
                     </tr>                       
                     <tr>
                         <td>
                             <label>Categoría:</label>
                         </td>
                     </tr>   
                     <tr>
                         <td>
                          <div>      
                            <?php echo $clasificacion;?> 
                          </div>    
                         </td>
                     </tr>                      
                 </tbody>
             </table>            
          </td>  
          <td style="width:180px">
             <legend>Fecha</legend> 
             <table class="SubFormulario">                 
                 <tbody>
                     <tr>
                         <td>
                             <label><?php echo $fecha_recepcion['label']; ?></label>                                
                         </td>                            
                     </tr>
                     <tr>                         
                         <td>
                             <?php echo form_input($fecha_recepcion);?>
                         </td> 
                     </tr>
                     <tr>
                         <td>
                             <label>Fecha de Emisión:</label>                                
                         </td>                            
                     </tr>
                     <tr> 
                         <td>
                             <?php echo form_input($fecha_emision);?>
                         </td> 
                     </tr>                                  
                 </tbody>
             </table>     
          </td>
        </tr>  
        <tr>
          <td colspan="3">
            <legend>Contenido</legend>
                <table class="SubFormulario">
                    <tbody>
                     <tr>
                       <td>
                          <label>Asunto:</label>
                       </td>
                     </tr>
                     <tr>
                       <td>
                         <div>
                           <?php echo form_input($asunto);?>
                         </div>
                       </td>
                     </tr>                        
                     <tr>
                       <td>
                         <label>Observaciones:</label>
                       </td>
                    </tr>
                     <tr>
                       <td>
                         <?php echo form_textarea($observaciones);?>    
                       </td>
                    </tr>                         
                    </tbody>
                </table>            
          </td>
        </tr>
        <tr>
          <td style="width:280px">
            <legend>Registrador</legend>
                <div class="SubFormulario" style="text-align:center; vertical-align: middle;">                  
                  <?php echo $analista;?>
                </div>
          </td>
          <td>
          </td>
          <td style="vertical-align:bottom; text-align: right">
             <?php echo $boton_registrar;?>  
          </td>          
        </tr>        
      </tbody>               
    </table>
  </form>
  </div>  
  <div class="clear"></div>    
  <div class="grid_16 AjusteBottom"></div>
  <div class="clear"></div>
</div>
<div class="clear"></div>  