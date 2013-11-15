<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script'); ?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>
  <div class="clear"></div>
        
  <div class="grid_10 alpha omega prefix_3 suffix_3 EntraDatos">  
   <table>
      <thead>
          <tr>
              <th colspan="2">
               Autenticación   
              </th>
          </tr>
      </thead>
      <tbody>
          <tr>
              <td>
                 <?php
                  echo img(base_url().'imagenes/candado.png');
                  ?>  
              </td>
              <td>                 
                   <?php
                    echo form_open('/acceso/verificar_ingreso');
                   ?>                
                   <?php
                    echo form_label('Usuario: ');
                   ?>                 
                  <div> 
                   <?php
                    echo form_input($usuario);
                   ?>
                  </div>
                   <?php
                    echo form_label('Contraseña: ');
                    ?>
                  <div>
                   <?php
                    echo form_password($clave);
                   ?>              
                  </div>
                  <p><?php echo $this->session->userdata('errores');?></p>                 
              </td>
          </tr>
      </tbody>
      <tfoot>
          <tr>
              <td colspan="2">         
                  <div class="BotonIco" onclick="javascript:$('form').submit()" title="Entrar">
                    <img src="imagenes/agregado.png"/>
                    <a href="#" tabindex="3">Entrar</a></div>
                  <?php
                    echo form_close();
                  ?>                 
              </td>                
          </tr>
      </tfoot>        
   </table>
  </div>
  <div class="clear"></div>
  <div class="grid_16 AjusteBottom"></div>
  <div class="clear"></div>  
</div>
<div class="clear"></div>  