<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>
  <div class="clear"></div>
  <div class="grid_4" style="margin-bottom: 20px">
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
  <div class="clear"></div>
  <div class="grid_16" id="Tabla">
    <?php echo $tabla_bitacora;?>
  </div> 
  <div class="clear"></div>
  <div class="grid_16 AjusteBottom"></div>
  <div class="clear"></div>
</div>