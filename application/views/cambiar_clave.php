<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script'); ?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>
  <div class="clear"></div>
  <div class="grid_10 alpha omega prefix_3 suffix_3 EntraDatos">  
    <table>
        <thead>
            <tr>
                <th colspan="2">
                 Cambiar Contraseña   
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
                      echo form_open('/acceso/verificar_clave');
                     ?>                
                     <?php
                      echo form_label('Contraseña Actual: ');
                     ?>                 
                    <div> 
                     <?php
                      echo form_password($actual);
                     ?>
                    </div>
                     <?php
                      echo form_label('Contraseña Nueva: ');
                      ?>
                    <div>
                     <?php
                      echo form_password($nueva);
                     ?>              
                    </div>
                     <?php
                      echo form_label('Confirme Contraseña: ');
                     ?>
                    <div>
                     <?php
                      echo form_password($nueva2);
                     ?>              
                    </div>
                    <p><?php echo $this->session->userdata('errores');?></p>                 
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                  <div class="BotonIco" onclick="javascript:window.location='../bandeja/asignadas'" title="Cancelar">
                  <?php echo img(base_url().'imagenes/cancel.png');?>                    
                  <a href="#" tabindex="5">Cancelar</a></div>
                  <?php
                      echo nbs(5);
                      echo form_close();                    
                  ?>
                  <div class="BotonIco" onclick="javascript:$('form').submit()" title="Entrar">
                  <?php echo img(base_url().'imagenes/agregado.png');?>
                  <a href="#" tabindex="4">Entrar</a></div>              
                </td>                
            </tr>
        </tfoot>        
    </table>
    
  </div>
  <div class="grid_16 AjusteBottom"></div>
  <div class="clear"></div>
</div>
<div class="clear"></div>