<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>
  <div class="clear"></div>  
  <div class="grid_5">
    <legend>&nbsp;</legend>      
    <table>                 
        <tbody>
            <tr>
              <td>
                &nbsp;<?php echo form_input($bandeja);?>
              </td>
            </tr>
            <tr>
              <td>
                <center><h5><?php echo $boton_entrante; ?></h5></center>
              </td>
            <tr>
              <td>
                &nbsp;
              </td>
            </tr>              
            </tr>                          
        </tbody>
    </table>      
  </div>  
  <div class="grid_4">
    <legend>&nbsp;</legend>
    <table class="SubFormulario">                 
        <tbody>
            <tr>
              <td>
                  <label>Desde:</label>
              </td>
              <td>
                  <?php echo form_input($fecha_ini);?>
              </td>
            </tr>
            <tr>
              <td>                 
              </td>
            </tr> 
            <tr>
              <td>
                  <label>Hasta:</label>
              </td>
              <td>
                  <?php echo form_input($fecha_fin);?>
              </td>
            </tr>
            <tr>
              <td>                 
              </td>
            </tr>                          
        </tbody>
    </table>         
  </div> 
  <div class="grid_6 suffix_1">
    <legend>Filtros por Clasificación</legend>
    <table class="SubFormulario">                 
        <tbody>
            <tr>
              <td>
                  <label>Tipo:</label>                                
              </td>
              <td style="padding-left: 0px">
                 <?php echo $tipo;?>
              </td>              
            </tr>
            <tr>
              <td>              
              </td>
            </tr> 
            <tr>
              <td>
                  <label>Categoría:</label>                       
              </td>
              <td style="padding-left: 0px">
                 <?php echo $clasificacion;?>
              </td>              
            </tr>
            <tr>
              <td>                 
              </td>
            </tr>                          
        </tbody>
    </table>         
  </div>  
  <div class="clear"></div>  
  <div class="grid_16" id="Tabla">
    <?php echo $tabla;?>
  </div>
  <div class="clear"></div>  
  <div class="grid_16 AjusteBottom">&nbsp;</div>
  <div class="clear"></div>  
</div>