<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>
  <div class="clear"></div>
  <div class="grid_16">
    <?php echo $tablas;?>
      <br/>
      <br/>
  </div>
  <div class="clear"></div>
  <div class="grid_16">     
      <table class="TablaNivel1">
          <thead>
              <tr>
                  <th style="text-align: left">&nbsp;&nbsp;&nbsp;Sentencia SQL</th>
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td style="padding:10px">
                  <?php echo $sql;?>
                  </td>
              </tr>              
          </tbody>          
      </table>      
      <br/>
  </div>   
  <div class="clear"></div>  
  <div class="grid_16">
   <?php echo $boton;?> 
  </div> 
  <div class="clear"></div>
  <div class="grid_16" id="Resultado">
  </div>  
  <div class="clear"></div>  
  <div class="grid_16 AjusteBottom"></div>
  <div class="clear"></div>
</div>